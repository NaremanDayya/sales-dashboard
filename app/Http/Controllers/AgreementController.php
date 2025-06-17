<?php

namespace App\Http\Controllers;

use App\Exports\AgreementsExport;
use App\Models\Agreement;
use App\Models\Client;
use App\Models\Commission;
use App\Models\RequestType;
use App\Models\SalesRep;
use App\Models\Service;
use App\Models\Target;
use App\Models\User;
use App\Notifications\AgreementNoticePeriodStarted;
use App\Notifications\AgreementRenewed;
use App\Notifications\NewAgreementCreated;
use App\Notifications\TargetAchievedNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Box\Spout\Writer\XLSX\Writer as XLSXWriter;
use Box\Spout\Common\Type;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class AgreementController extends Controller
{
    public function allAgreements()
    {
        $Agreements = Agreement::with(['client', 'service'])->get()->map(function ($agreement) {
            return [
                // Client logo (assuming relation `client` exists and client has `company_logo`)
                'client_logo' => $agreement->client && $agreement->client->company_logo
                    ? asset('storage/' . $agreement->client->company_logo)
                    : null,
                'client_name' => $agreement->client ? $agreement->client->company_name : '—',
                'signing_date' => $agreement->signing_date ? $agreement->signing_date->format('Y-m-d') : '—',
                'duration_years' => $agreement->duration_years ?? '—',
                'termination_type' => $agreement->termination_type ?? '—',
                'implementation_date' => $agreement->implementation_date ? $agreement->implementation_date->format('Y-m-d') : '—',
                'end_date' => $agreement->end_date ? $agreement->end_date->format('Y-m-d') : '—',
                'notice_months' => $agreement->notice_months ?? '—',
                'notice_date' => $agreement->notice_date ?? '—',
                'notice_status' => $agreement->notice_status ?? '—',
                'service_type' => $agreement->service ? $agreement->service->name : '—',
                'product_quantity' => $agreement->product_quantity ?? '—',
                'price' => number_format($agreement->price),
                'total_amount' => number_format($agreement->total_amount),
                'sales_rep_id' => $agreement->sales_rep_id,
                'return_value' => $agreement->return_value,
                'agreement_id' => $agreement->id,
                'is_notice_at_time' => $agreement->isNoticedAtTime(),
                'required_notice_date' => $agreement->getRequiredNoticeDate()->format('Y-m-d'),

            ];
        });
        return view('agreements.table', data: compact('Agreements'));
    }
    public function index(SalesRep $salesrep)
    {
        $Agreements = Agreement::where('sales_rep_id', $salesrep->id)->with(['client', 'service'])->get()->map(function ($agreement) {
            return [
                // Client logo (assuming relation `client` exists and client has `company_logo`)
                'client_logo' => $agreement->client && $agreement->client->company_logo
                    ? asset('storage/' . $agreement->client->company_logo)
                    : null,
                'client_name' => $agreement->client ? $agreement->client->company_name : '—',
                'signing_date' => $agreement->signing_date ? $agreement->signing_date->format('Y-m-d') : '—',
                'duration_years' => $agreement->duration_years ?? '—',
                'termination_type' => $agreement->termination_type ?? '—',
                'implementation_date' => $agreement->implementation_date ? $agreement->implementation_date->format('Y-m-d') : '—',
                'end_date' => $agreement->end_date ? $agreement->end_date->format('Y-m-d') : '—',
                'notice_months' => $agreement->notice_months ?? '—',
                'notice_status' => $agreement->notice_status ?? '—',
                'service_type' => $agreement->service ? $agreement->service->name : '—',
                'product_quantity' => $agreement->product_quantity ?? '—',
                'price' => number_format($agreement->price),
                'total_amount' => number_format($agreement->total_amount),
                'sales_rep_id' => $agreement->sales_rep_id,
                'return_value' => $agreement->return_value,
                'agreement_id' => $agreement->id,
                'is_notice_at_time' => $agreement->isNoticedAtTime(),
                'required_notice_date' => $agreement->getRequiredNoticeDate()->format('Y-m-d'),


            ];
        });
        return view('agreements.table', data: compact('Agreements'));
    }

    public function create(SalesRep $salesrep)
    {
        // $this->authorizeAccess($salesrep);
        $agreement = new Agreement();
        $clients = $salesrep->clients; // only their clients
        $services = Service::all();

        return view('agreements.create', compact('clients', 'services', 'salesrep', 'agreement'));
    }
    public function store(Request $request)
    {
        $validated = $this->validateAgreementRequest($request);

        $agreement = $this->createAgreement($validated);
        $adminUser = User::where('role', 'admin')->first();

        $adminUser?->notify(new NewAgreementCreated($agreement));
        $this->handleTargetUpdates($agreement);
        return redirect()->route('salesrep.agreements.index', [
            'salesrep' => $agreement->sales_rep_id,
        ])->with('success', 'Agreement saved successfully.');
    }
    protected function validateAgreementRequest(Request $request): array
    {
        $service = Service::find($request->input('service_id'));

        $isFlatPrice = optional($service)->is_flat_price ?? false;

        return $request->validate([
            'agreement_id' => 'nullable|exists:agreements,id',
            'client_id' => 'required|exists:clients,id',
            'service_id' => 'required|exists:services,id',
            'signing_date' => 'required|date',
            'implementation_date' => 'required|date|after_or_equal:signing_date',
            'duration_years' => 'required|integer|min:1',
            'termination_type' => 'required|in:returnable,non_returnable',
            'notice_months' => 'required|integer|min:0',
            'product_quantity' => [
                Rule::requiredIf(!$isFlatPrice),
                'nullable',
                'integer',
                'min:1',
            ],
            'price' => 'required|numeric|min:0',
            'total_amount' => 'numeric|min:0',
            'status' => 'required|in:active,terminated,expired',
        ]);
    }

    protected function createAgreement(array $validated): Agreement
    {
        $implementationDate = Carbon::parse($validated['implementation_date']);
        $endDate = $implementationDate->copy()->addYears((int) $validated['duration_years']);

        $service = Service::find($validated['service_id']);
        $isFlatPrice = optional($service)->is_flat_price ?? false;

        $productQuantity = $isFlatPrice ? null : $validated['product_quantity'];
        $totalAmount = $isFlatPrice
            ? $validated['price']
            : $validated['price'] * $validated['product_quantity'];
        $sales_rep_id = Auth::user()->salesRep->id;
        return Agreement::create([
            'sales_rep_id' => $sales_rep_id,
            'client_id' => $validated['client_id'],
            'service_id' => $validated['service_id'],
            'signing_date' => $validated['signing_date'],
            'implementation_date' => $validated['implementation_date'],
            'duration_years' => $validated['duration_years'],
            'termination_type' => $validated['termination_type'],
            'notice_months' => $validated['notice_months'],
            'agreement_status' => $validated['status'],
            'return_value' => $validated['return_value'] ?? 0,
            'product_quantity' => $productQuantity,
            'price' => $validated['price'],
            'total_amount' => $totalAmount,
            'end_date' => $endDate,
        ]);
    }
    protected function handleTargetUpdates(Agreement $agreement): void
    {
        $service = $agreement->service;
        $isFlatPrice = $service->is_flat_price ?? false;

        if ($isFlatPrice) {
            $achievedValue = $agreement->price;
            $productQuantity = 1;
        } else {
            $achievedValue = $agreement->product_quantity;
            $productQuantity = $agreement->product_quantity;
        }

        $target = $this->updateSalesRepTargets(
            $agreement,
            $agreement->implementation_date,
            $agreement->sales_rep_id,
            $agreement->service_id,
            $productQuantity,
            $achievedValue
        );

        if ($target && $target->is_achieved) {
            $adminUser = User::where('role', 'admin')->get();
            Notification::send($adminUser, new TargetAchievedNotification($target));

        }
    }
    protected function updateSalesRepTargets(
        $agreement,
        Carbon $implementationDate,
        int $salesRepId,
        int $serviceId,
        float $productQuantity,
        float $totalAmount
    ): ?Target {
        $month = $implementationDate->month;
        $year = $implementationDate->year;

        return DB::transaction(function () use ($salesRepId, $serviceId, $month, $year, $totalAmount) {
            $target = $this->getOrCreateTarget($salesRepId, $serviceId, $month, $year);

            if ($target->target_amount <= 0) {
                return null;
            }

            // Calculate all new values first
            $newAchievedAmount = $target->achieved_amount + $totalAmount;
            $newCarriedOverAmount = max(0, $target->carried_over_amount - $totalAmount);
            $achievementRatio = $newAchievedAmount / $target->target_amount;
            $isNewAchievement = $this->isNewAchievement($target, $achievementRatio);

            // Single atomic update
            $target->update([
                'achieved_amount' => $newAchievedAmount,
                'carried_over_amount' => $newCarriedOverAmount,
                'achieved_percentage' => $achievementRatio * 100,
                'is_achieved' => $achievementRatio >= 1,
                'commission_due' => $newAchievedAmount > 0,
            ]);

            if ($isNewAchievement) {
                $this->handleAchievedTarget($target, $salesRepId, $serviceId, $month, $year);
            } else {
                // Update commission even if not newly achieved
                $achievedTotalAmount = $this->calculateAchievedTotalAmount($salesRepId, $serviceId, $month, $year);
                $commissionAmount = 0.005 * $achievedTotalAmount;
                $this->createOrUpdateCommission(
                    $target,
                    $salesRepId,
                    $serviceId,
                    $month,
                    $year,
                    $commissionAmount,
                    $achievedTotalAmount
                );
            }

            return $target->fresh();
        });
    }
    protected function getOrCreateTarget(
        int $salesRepId,
        int $serviceId,
        int $month,
        int $year,
        float $initialAchievedAmount = 0,
    ): Target {
        return DB::transaction(function () use ($salesRepId, $serviceId, $month, $year, $initialAchievedAmount) {
            $existingTarget = Target::where('sales_rep_id', $salesRepId)
                ->where('service_id', $serviceId)
                ->where('month', $month)
                ->where('year', $year)
                ->first();

            if ($existingTarget) {
                return $existingTarget;
            }

            $service = Service::findOrFail($serviceId);
            $salesRep = SalesRep::findOrFail($salesRepId);
            $currentDate = Carbon::create($year, $month, 1);
            $startWorkDate = Carbon::parse($salesRep->start_work_at)->startOfMonth();

            $carriedOverAmount = 0;
            $hasPreviousTarget = false;

            $checkDate = $currentDate->copy()->subMonth();
            while ($checkDate->greaterThanOrEqualTo($startWorkDate)) {
                $previousTarget = Target::where('sales_rep_id', $salesRepId)
                    ->where('service_id', $serviceId)
                    ->where('month', $checkDate->month)
                    ->where('year', $checkDate->year)
                    ->first();

                if ($previousTarget) {
                    $hasPreviousTarget = true;
                    $shortfall = max(0, $previousTarget->target_amount - $previousTarget->achieved_amount);
                    $carriedOverAmount += $shortfall;
                }

                $checkDate->subMonth();
            }

            if (!$hasPreviousTarget) {
                $carriedOverAmount = max(0, $service->target_amount - $initialAchievedAmount);
            }

            return Target::create([
                'sales_rep_id' => $salesRepId,
                'service_id' => $serviceId,
                'month' => $month,
                'year' => $year,
                'target_amount' => (float) $service->target_amount,
                'achieved_amount' => 0,
                'is_achieved' => false,
                'commission_due' => false,
                'carried_over_amount' => $carriedOverAmount,
                'achieved_percentage' => 0,
            ]);
        });
    }

    protected function isNewAchievement(Target $target, float $achievementRatio): bool
    {
        return $achievementRatio >= 0.9 && !$target->is_achieved;
    }

    protected function handleAchievedTarget(
        Target $target,
        int $salesRepId,
        int $serviceId,
        int $month,
        int $year
    ): Target {
        return DB::transaction(function () use ($target, $salesRepId, $serviceId, $month, $year) {
            $achievedTotalAmount = $this->calculateAchievedTotalAmount($salesRepId, $serviceId, $month, $year);
            $commissionAmount = 0.005 * $achievedTotalAmount;

            $target->update([
                'is_achieved' => true,
                'commission_due' => true,
            ]);

            $this->createOrUpdateCommission(
                $target,
                $salesRepId,
                $serviceId,
                $month,
                $year,
                $commissionAmount,
                $achievedTotalAmount
            );

            return $target->fresh();
        });
    }
    protected function calculateAchievedTotalAmount(
        int $salesRepId,
        int $serviceId,
        int $month,
        int $year
    ): float {
        return Agreement::query()
            ->where('sales_rep_id', $salesRepId)
            ->where('service_id', $serviceId)
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->sum('total_amount');
    }

    protected function createOrUpdateCommission(
        Target $target,
        int $salesRepId,
        int $serviceId,
        int $month,
        int $year,
        float $commissionAmount,
        float $achievedTotalAmount
    ): void {
        // Get the service to check commission rules if needed
        $service = Service::findOrFail($serviceId);

        $achievedPercentage = 0;
        if ($target->target_amount > 0) {
            $achievedPercentage =
                ($target->achieved_amount / $target->target_amount) * 100;

        }

        $finalCommissionAmount = $commissionAmount;

        Commission::updateOrCreate(
            [
                'sales_rep_id' => $salesRepId,
                'service_id' => $serviceId,
                'year' => $year,
                'month' => $month,
            ],
            [
                'target_id' => $target->id,
                'commission_amount' => $finalCommissionAmount,
                'total_achieved_amount' => $target->achieved_amount, // Use target's value
                'achieved_percentage' => $achievedPercentage,
            ]
        );

        if (!$target->commission_due && $finalCommissionAmount > 0) {
            $target->update(['commission_due' => true]);
        }
    }
    public function edit(SalesRep $salesrep, Agreement $agreement)
    {
        // $this->authorizeAccess($salesrep);
        $clients = $salesrep->clients;
        $services = Service::all();
        $approvedEditRequest = $agreement->editRequests()
            ->where('sales_rep_id', Auth::id())
            ->where('status', 'approved')
            ->where('request_type', 'agreement_data_change')
            ->latest()
            ->first();

        $editableField = $approvedEditRequest ? $approvedEditRequest->edited_field : null;
        return view('agreements.edit', compact('clients', 'services', 'salesrep', 'agreement', 'editableField'));
    }

    public function update(Request $request, SalesRep $salesrep, Agreement $agreement)
    {
        // Get the approved edit request for thisshow agreement and sales rep
        $approvedEditRequest = $agreement->editRequests()
            ->where('sales_rep_id', Auth::id())
            ->where('status', 'approved')
            ->where('request_type', 'agreement_data_change')
            ->latest()
            ->first();

        // If no approved request, block the update
        if (!$approvedEditRequest) {
            return back()->with('error', 'لا يوجد طلب تعديل معتمد لهذا العقد.');
        }

        $editableField = $approvedEditRequest->edited_field;

        // Validation rules for only the editable field
        $rules = [];

        switch ($editableField) {
            case 'client_id':
                $rules[$editableField] = 'required|exists:clients,id';
                break;
            case 'signing_date':
            case 'end_date':
                $rules[$editableField] = 'required|date_format:d-m-Y';
                break;
            case 'duration_years':
            case 'notice_months':
            case 'product_quantity':
                $rules[$editableField] = 'required|integer|min:1';
                break;
            case 'termination_type':
                $rules[$editableField] = 'required|in:non_returnable,returnable';
                break;
            case 'notice_status':
                $rules[$editableField] = 'required|in:not_sent,sent';
                break;
            case 'service_type':
                $rules[$editableField] = 'required|exists:services,id';
                break;
            case 'price':
                $rules[$editableField] = 'required|numeric|min:0';
                break;
            case 'agreement_status':
                $rules[$editableField] = 'required|in:active,terminated,expired';
                break;
            default:
                return back()->with('error', 'لا يمكن تعديل هذا الحقل.');
        }

        $validated = $request->validate($rules);

        if (in_array($editableField, ['price', 'product_quantity'])) {
            $otherField = $editableField === 'price' ? 'product_quantity' : 'price';
            $existingValue = $agreement->$otherField;

            // If the other field is updated in the same request, use that value
            if ($request->has($otherField)) {
                $validated[$otherField] = $request->input($otherField);
            }

            $price = $editableField === 'price' ? $validated['price'] : $agreement->price;
            $quantity = $editableField === 'product_quantity' ? $validated['product_quantity'] : $agreement->product_quantity;

            $validated['total_amount'] = $price * $quantity;
        }
        if (in_array($editableField, ['signing_date', 'duration_years'])) {
            $otherField = $editableField === 'signing_date' ? 'duration_years' : 'signing_date';

            if ($request->has($otherField)) {
                $validated[$otherField] = $request->input($otherField);
            } else {
                $validated[$otherField] = $agreement->$otherField;
            }

            if ($editableField === 'signing_date') {
                $signingDate = Carbon::createFromFormat('d-m-Y', $validated['signing_date']);
            } else {
                $signingDate = Carbon::parse($agreement->signing_date);
            }

            $durationYears = $editableField === 'duration_years' ? (int) $validated['duration_years'] : (int) $agreement->duration_years;

            $endDate = $signingDate->copy()->addYears($durationYears);

            $validated['signing_date'] = $signingDate->format('Y-m-d');
            $validated['end_date'] = $endDate->format('Y-m-d');

        }
        $agreement->update($validated);
        $permission = $agreement->salesRep->myLastPermission($agreement, $editableField);
        $permission->update(['used' => true]);

        return redirect()->route('salesrep.agreements.show', [
            'salesrep' => $salesrep->id,
            'agreement' => $agreement->id
        ])->with('success', 'تم تعديل العقد بنجاح.');
    }



    public function show(SalesRep $salesrep, Agreement $agreement)
    {
        $requestTypes = RequestType::all();
        $columns = [
            'service_type' => 'نوع الخدمة',
            'signing_date' => 'تاريخ التوقيع',
            'duration_years' => 'مدة السنوات',
            'termination_type' => 'نوع الإنهاء',
            'notice_months' => 'شهور الإخطار',
            'notice_status' => 'حالة الإخطار',
            'product_quantity' => 'كمية المنتج',
            'price' => 'السعر',
            'agreement_status' => 'حالة الاتفاقية',
            'implementation_date' => 'تاريخ التنفيذ',
        ];
        $approvedEditRequest = $agreement->editRequests()
            ->where('sales_rep_id', $salesrep->user->id)
            ->where('status', 'approved')
            ->where('request_type', 'agreement_data_change')
            ->latest()
            ->first();

        $editableField = $approvedEditRequest ? $approvedEditRequest->edited_field : null;

        $withinNoticePeriod = $agreement->isWithinNoticePeriod();

        $client = $agreement->client;
        //    dd($agreement->sales_rep_id); // dd($agreement->sales_rep_id);

        return view('agreements.show', compact('columns', 'agreement', 'requestTypes', 'salesrep', 'client', 'withinNoticePeriod', 'editableField'));
    }

    public function updateNoticeStatus(Request $request, SalesRep $salesrep, Agreement $agreement)
    {
        // $this->authorizeAccess($salesrep);

        if ($agreement->sales_rep_id !== $salesrep->id || !$agreement->isWithinNoticePeriod()) {
            abort(403);
        }
        if (!$agreement->isWithinNoticePeriod()) {
            return redirect()->back()->with('error', 'تم تجاوز فترة الإشعار، لا يمكن إرسال إشعار الآن.');
        }
        $validated = $request->validate([
            'notice_date' => ['required', 'date'],
        ]);

        $agreement->update([
            'notice_date' => $validated['notice_date'],
            'notice_status' => 'sent',
        ]);

        if ($agreement['notice_status'] === 'sent') {
            $adminUser = User::where('role', 'admin')->first();

            $adminUser->notify(new AgreementNoticePeriodStarted($agreement));

            $salesRepUser = $salesrep->user ?? null;
            if ($salesRepUser) {
                $salesRepUser->notify(new AgreementNoticePeriodStarted($agreement));
            }

            if (!$agreement->isNoticedAtTime()) {
                $this->renewAgreement($agreement);
                return redirect()->back()->with('warning', 'تم إرسال الإشعار ولكن تم تجديد الاتفاقية تلقائيًا بسبب التأخير.');
            }
        }

        return redirect()->back()->with('success', 'تم إرسال الإشعار بنجاح ولن يتم تجديد الاتفاقية.');
    }

    protected function renewAgreement(Agreement $agreement)
    {
        $oldAgreementData = clone $agreement;

        $newSigningDate = now();
        $newImplementationDate = Carbon::parse($agreement->end_date)->addDay();
        $newEndDate = $newImplementationDate->copy()->addYears($agreement->duration_years);

        $agreement->update([
            'signing_date' => $newSigningDate,
            'implementation_date' => $newImplementationDate,
            'end_date' => $newEndDate,
            'notice_date' => null,
            'notice_status' => 'not_sent',
        ]);

        // Notify admins
        $adminUser = User::where('role', 'admin')->first();
        $adminUser->notify( new AgreementRenewed($oldAgreementData, $agreement));

        if ($salesRepUser = $agreement->salesRep->user ?? null) {
            $salesRepUser->notify(new AgreementRenewed($oldAgreementData, $agreement));
        }
    }



}
