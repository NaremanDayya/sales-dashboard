<?php

namespace App\Livewire;

use App\Services\AiAssistantService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AiAssistant extends Component
{
    public array $displayMessages = [];
    public string $input = '';
    public bool $loading = false;

    protected string $sessionKey = 'ai_assistant_history';

    public function mount(): void
    {
        $this->displayMessages = collect(session($this->sessionKey, []))
            ->flatMap(fn (array $message) => $this->toDisplay($message))
            ->values()
            ->all();
    }

    public function send(AiAssistantService $service): void
    {
        $text = trim($this->input);
        if ($text === '') {
            return;
        }

        $this->displayMessages[] = ['role' => 'user', 'text' => $text];
        $this->input = '';
        $this->loading = true;

        try {
            $result = $service->reply(Auth::user(), session($this->sessionKey, []), $text);
            session([$this->sessionKey => $result['messages']]);
            $this->displayMessages[] = ['role' => 'assistant', 'text' => $result['reply']];
        } catch (\Throwable $e) {
            report($e);
            $this->displayMessages[] = ['role' => 'assistant', 'text' => 'حدث خطأ أثناء التواصل مع المساعد، حاول مرة أخرى.'];
        }

        $this->loading = false;
    }

    public function clearConversation(): void
    {
        session()->forget($this->sessionKey);
        $this->displayMessages = [];
    }

    /**
     * Reduce a raw Anthropic-format message to its visible text turns,
     * dropping tool_use/tool_result internals from the shown transcript.
     */
    protected function toDisplay(array $message): array
    {
        if (!in_array($message['role'] ?? null, ['user', 'assistant'], true)) {
            return [];
        }

        if (is_string($message['content'])) {
            return [['role' => $message['role'], 'text' => $message['content']]];
        }

        $text = collect($message['content'])->where('type', 'text')->pluck('text')->implode("\n");

        return $text !== '' ? [['role' => $message['role'], 'text' => $text]] : [];
    }

    public function render()
    {
        return view('livewire.ai-assistant');
    }
}
