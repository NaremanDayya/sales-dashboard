<?php

namespace App\Services;

use App\Models\Agreement;
use App\Models\Client;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Answers natural-language questions about the caller's own system data
 * (clients, agreements, targets, pending requests) using Claude's tool-use.
 * The model never sees raw data directly - it can only call the tools below,
 * which each enforce the caller's role-based scope in code.
 */
class AiAssistantService
{
    protected const MAX_TOOL_ROUNDS = 4;
    protected const API_URL = 'https://api.anthropic.com/v1/messages';

    /**
     * @param  array  $history  Prior messages in Anthropic's {role, content} format.
     * @return array{messages: array, reply: string}
     */
    public function reply(User $user, array $history, string $message): array
    {
        $messages = $history;
        $messages[] = ['role' => 'user', 'content' => $message];

        $malformedRetries = 0;

        for ($round = 0; $round < self::MAX_TOOL_ROUNDS; $round++) {
            $response = $this->callClaude($user, $messages);
            $content = $response['content'] ?? [];
            $toolUses = collect($content)->where('type', 'tool_use');
            $stopReason = $response['stop_reason'] ?? null;

            // Rare glitch observed in testing: the API reports stop_reason
            // "tool_use" but the response has no structured tool_use block
            // (the model narrates the call as plain text instead). Retry the
            // same turn instead of surfacing that raw text to the user.
            if ($stopReason === 'tool_use' && $toolUses->isEmpty() && $malformedRetries < 2) {
                $malformedRetries++;
                $round--;
                continue;
            }

            if ($stopReason !== 'tool_use' || $toolUses->isEmpty()) {
                $text = collect($content)->where('type', 'text')->pluck('text')->implode("\n");
                $messages[] = ['role' => 'assistant', 'content' => $this->normalizeContent($content)];

                return [
                    'messages' => $messages,
                    'reply' => $text !== '' ? $text : 'لم أتمكن من الحصول على إجابة، حاول صياغة السؤال بشكل مختلف.',
                ];
            }

            $messages[] = ['role' => 'assistant', 'content' => $this->normalizeContent($content)];

            $toolResults = $toolUses->map(function (array $toolUse) use ($user) {
                $result = $this->runTool($user, $toolUse['name'], $toolUse['input'] ?? []);

                return [
                    'type' => 'tool_result',
                    'tool_use_id' => $toolUse['id'],
                    'content' => json_encode($result, JSON_UNESCAPED_UNICODE),
                ];
            })->values()->all();

            $messages[] = ['role' => 'user', 'content' => $toolResults];
        }

        return [
            'messages' => $messages,
            'reply' => 'تعذر إكمال الطلب بعد عدة محاولات، برجاء إعادة صياغة السؤال.',
        ];
    }

    /**
     * PHP's json_decode() turns a JSON "{}" into an empty array, indistinguishable
     * from "[]". When we echo a tool_use block back to the API (required to keep
     * the conversation valid), an argument-less call's input round-trips as a
     * JSON array and Anthropic rejects it with "Input should be an object".
     * Force empty tool_use inputs back into an object before re-sending them.
     */
    protected function normalizeContent(array $content): array
    {
        return array_map(function ($block) {
            if (($block['type'] ?? null) === 'tool_use' && isset($block['input']) && is_array($block['input']) && $block['input'] === []) {
                $block['input'] = new \stdClass();
            }

            return $block;
        }, $content);
    }

    protected function callClaude(User $user, array $messages): array
    {
        $response = Http::withHeaders([
            'x-api-key' => config('services.anthropic.api_key'),
            'anthropic-version' => '2023-06-01',
            'content-type' => 'application/json',
        ])->timeout(60)->post(self::API_URL, [
            'model' => config('services.anthropic.model'),
            'max_tokens' => 1024,
            'system' => $this->systemPrompt($user),
            'messages' => $messages,
            'tools' => $this->toolDefinitions(),
        ]);

        if ($response->failed()) {
            Log::error('AI assistant request failed', ['status' => $response->status(), 'body' => $response->body()]);
            throw new \RuntimeException('AI request failed: ' . $response->status());
        }

        return $response->json();
    }

    protected function systemPrompt(User $user): string
    {
        $roleLabel = match (true) {
            $user->role === 'admin' => 'مدير النظام (يرى كل بيانات النظام)',
            (bool) $user->salesRep?->isManager() => 'مدير فريق (يرى بيانات فريقه وبيانات نفسه)',
            default => 'مندوب مبيعات (يرى بيانات نفسه فقط)',
        };

        return <<<PROMPT
أنت مساعد ذكي داخلي لنظام إدارة مبيعات. تجاوب دائمًا باللغة العربية، بإيجاز ووضوح.
المستخدم الحالي: {$user->name} - دوره: {$roleLabel}.
استخدم الأدوات المتاحة فقط للحصول على البيانات الحقيقية، ولا تخترع أرقامًا أو معلومات غير موجودة في نتائج الأدوات.
إذا كان السؤال خارج نطاق بيانات هذا النظام (العملاء، الاتفاقيات، التارجت، الطلبات المعلقة)، اعتذر بلطف ووضح أنك مخصص لهذا النظام فقط.
PROMPT;
    }

    protected function toolDefinitions(): array
    {
        return [
            [
                'name' => 'get_clients_needing_renewal',
                'description' => 'يرجع الاتفاقيات التي دخلت فترة الإخطار المطلوبة قبل الانتهاء ولم يُرسل إخطار تجديد بعد، ضمن نطاق صلاحية المستخدم.',
                'input_schema' => ['type' => 'object', 'properties' => (object) []],
            ],
            [
                'name' => 'get_late_customers',
                'description' => 'يرجع عدد العملاء المتأخر التواصل معهم، ضمن نطاق صلاحية المستخدم.',
                'input_schema' => ['type' => 'object', 'properties' => (object) []],
            ],
            [
                'name' => 'get_target_progress',
                'description' => 'يرجع تقدم المستخدم في تارجت الشهر الحالي (المبلغ المحقق والنسبة).',
                'input_schema' => ['type' => 'object', 'properties' => (object) []],
            ],
            [
                'name' => 'get_pending_requests_count',
                'description' => 'يرجع عدد طلبات تعديل العملاء/الاتفاقيات التي بانتظار موافقة الإدارة، ضمن نطاق صلاحية المستخدم.',
                'input_schema' => ['type' => 'object', 'properties' => (object) []],
            ],
            [
                'name' => 'search_client_agreements',
                'description' => 'يبحث عن اتفاقيات عميل معيّن بالاسم أو جزء منه ويرجع حالتها، ضمن نطاق صلاحية المستخدم.',
                'input_schema' => [
                    'type' => 'object',
                    'properties' => [
                        'client_name' => ['type' => 'string', 'description' => 'اسم الشركة أو جزء منه'],
                    ],
                    'required' => ['client_name'],
                ],
            ],
        ];
    }

    protected function runTool(User $user, string $name, array $input): mixed
    {
        return match ($name) {
            'get_clients_needing_renewal' => $this->getClientsNeedingRenewal($user),
            'get_late_customers' => $this->getLateCustomers($user),
            'get_target_progress' => $this->getTargetProgress($user),
            'get_pending_requests_count' => $this->getPendingRequestsCount($user),
            'search_client_agreements' => $this->searchClientAgreements($user, (string) ($input['client_name'] ?? '')),
            default => ['error' => 'أداة غير معروفة'],
        };
    }

    protected function agreementsScope(User $user)
    {
        if ($user->role === 'admin') {
            return Agreement::query();
        }

        $salesRep = $user->salesRep;
        if (!$salesRep) {
            return Agreement::query()->whereRaw('1 = 0');
        }

        return $salesRep->isManager() ? $salesRep->getTeamAgreementsQuery() : $salesRep->agreements();
    }

    protected function clientsScope(User $user)
    {
        if ($user->role === 'admin') {
            return Client::query();
        }

        $salesRep = $user->salesRep;
        if (!$salesRep) {
            return Client::query()->whereRaw('1 = 0');
        }

        return $salesRep->isManager() ? $salesRep->getTeamClientsQuery() : $salesRep->clients();
    }

    protected function getClientsNeedingRenewal(User $user): array
    {
        $agreements = $this->agreementsScope($user)
            ->where('agreement_status', 'active')
            ->where('notice_status', 'not_sent')
            ->with('client')
            ->get()
            ->filter(fn (Agreement $agreement) => $agreement->isWithinNoticePeriod())
            ->map(fn (Agreement $agreement) => [
                'client' => $agreement->client?->company_name,
                'end_date' => optional($agreement->end_date)->format('Y-m-d'),
                'required_notice_date' => $agreement->getRequiredNoticeDate()->format('Y-m-d'),
            ])
            ->values();

        return ['count' => $agreements->count(), 'agreements' => $agreements];
    }

    protected function getLateCustomers(User $user): array
    {
        $salesRep = $user->salesRep;

        if ($user->role === 'admin' || !$salesRep) {
            $days = optional(Setting::where('key', 'late_customer_days')->first())->value ?? 3;
            $count = $this->clientsScope($user)->where('last_contact_date', '<=', now()->subDays($days))->count();

            return ['count' => $count];
        }

        return [
            'interested' => $salesRep->lateCustomers('interested'),
            'not_interested' => $salesRep->lateCustomers('not interested'),
            'neutral' => $salesRep->lateCustomers('neutral'),
        ];
    }

    protected function getTargetProgress(User $user): array
    {
        $salesRep = $user->salesRep;
        if (!$salesRep) {
            return ['error' => 'لا يوجد ملف مندوب مبيعات مرتبط بهذا المستخدم.'];
        }

        return [
            'achieved_amount' => $salesRep->currentMonthAchievedAmount(),
            'achieved_percentage' => $salesRep->currentMonthAchievedPercentage(),
        ];
    }

    protected function getPendingRequestsCount(User $user): array
    {
        $salesRep = $user->salesRep;

        return ['count' => $salesRep ? $salesRep->total_pended_requests : 0];
    }

    protected function searchClientAgreements(User $user, string $clientName): array
    {
        if ($clientName === '') {
            return ['error' => 'يرجى تحديد اسم العميل.'];
        }

        $clientIds = $this->clientsScope($user)
            ->where('company_name', 'like', '%' . $clientName . '%')
            ->pluck('id');

        $agreements = $this->agreementsScope($user)
            ->whereIn('client_id', $clientIds)
            ->with(['client', 'service'])
            ->get()
            ->map(fn (Agreement $agreement) => [
                'client' => $agreement->client?->company_name,
                'service' => $agreement->service?->name,
                'status' => $agreement->agreement_status,
                'end_date' => optional($agreement->end_date)->format('Y-m-d'),
                'notice_status' => $agreement->notice_status,
            ]);

        return ['count' => $agreements->count(), 'agreements' => $agreements];
    }
}
