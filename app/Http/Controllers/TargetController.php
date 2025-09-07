<?php

namespace App\Http\Controllers;

use App\Models\SalesRep;
use App\Models\Service;
use App\Models\Target;
use App\Models\User;
use App\Models\Commission;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

class TargetController extends Controller
{
    public function index(SalesRep $salesRep)
    {
        $selectedYear = request('year', now()->year);
        $selectedMonth = request('month'); // Remove default to show all months

        $services = Service::all();

        // Sales rep start date
        $startDate = $salesRep->start_work_date;
        $startYear = $startDate?->year;
        $startMonth = $startDate?->month;

        // Fetch targets once (grouped by service)
        $targetsByService = Target::where('sales_rep_id', $salesRep->id)
            ->with('commissions') // eager load commissions
            ->get()
            ->keyBy('service_id');

        $data = $services->map(function ($service) use ($salesRep, $targetsByService, $selectedYear, $selectedMonth, $startYear, $startMonth) {

            $target = $targetsByService->get($service->id);
            $carriedOver = $target ? number_format($target->target_amount - $service->target_amount) : 0;

            // Determine commission according to month filter
            $commissionForMonth = null;
            if ($selectedMonth) {
                $commissionForMonth = $target?->commissions?->where('month', $selectedMonth)?->first();
            }

            $row = [
                'service_type' => $service->name,
                'target_amount' => number_format($service->target_amount),
                'current_month_achieved_amount' => (int)$target?->currentMonthAchievedAmount($service, $salesRep, $selectedMonth) ?? 0,
                'year_achieved_target' => $target?->yearAchievedAmount($service, $salesRep) ?? 0,
                'year_achieved_amount' => $target?->yearAchievedAmountValue($service, $salesRep) ?? 0,
                'commission_status' => $commissionForMonth?->commission_status ?? 'غير مستحق',
                'commission_value' => $commissionForMonth?->commission_amount ?? 0,
                'commission_id' => $commissionForMonth?->id ?? null,
                'needed_achieved_percentage' => $target->needed_achieved_percentage ?? 0,
                'actual_target_amount' =>  number_format($target?->target_amount) ?? 0,
                'carried_over_amount' => $carriedOver,
                'achieved_target_percentage_needed' => Setting::where('key', 'commission_threshold')->value('value') ?? 90,
            ];

            // Monthly values - show ALL months by default
            for ($month = 1; $month <= 12; $month++) {

                // Check if this month is before the sales rep's start date
                $isBeforeStartDate = $startYear && $startMonth &&
                    ($selectedYear < $startYear ||
                        ($selectedYear == $startYear && $month < $startMonth));

                if ($isBeforeStartDate) {
                    $row["month_achieved_$month"] = '-';
                    $row["commission_value_month_$month"] = 0;
                    $row["commission_payment_status_month_$month"] = 0;
                    $row["commission_status_month_$month"] = 'غير مستحق';
                    $row["commission_id_month_$month"] = null;
                } else {
                    // Always show the month value, regardless of selected month filter
                    $monthAchievedAmount = $target?->monthAchievedAmount($month, $service, $salesRep) ?? 0;
                    $row["month_achieved_$month"] = number_format($monthAchievedAmount);

                    $monthCommission = $target?->commissions?->where('month', $month)?->first();
                    $row["commission_value_month_$month"] = $monthCommission?->commission_amount ?? 0;
                    $row["commission_payment_status_month_$month"] = $monthCommission?->payment_status ?? 0;

                    // Use the commission_due column from the TARGET table
                    $commissionDue = $target?->commission_due ?? false;

                    // Store commission status for each month
                    $row["commission_status_month_$month"] = $commissionDue ? 'مستحق' : 'غير مستحق';
                    $row["commission_id_month_$month"] = $monthCommission?->id ?? null;
                }
            }

            return $row;
        });

        return view('targets.table', [
            'Targets' => $data,
            'selectedYear' => $selectedYear,
            'selectedMonth' => $selectedMonth,
            'salesRep' => $salesRep
        ]);
    }
    public function create(SalesRep $sales_rep)
    {
        $services = Service::all();
        return view('targets.create', compact('services', 'sales_rep'));
    }

    public function store(Request $request, SalesRep $sales_rep)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|digits:4',
            'target_amount' => 'required|numeric|min:0',
        ]);

        $validated['user_id'] = $sales_rep->id;
        $validated['target_amount'] = $validated['target_amount'] ?? Service::find($validated['service_id'])->default_target_amount;

        Target::create($validated);

        return redirect()->route('sales-rep.targets.index', $sales_rep->id)
            ->with('success', 'Target created successfully.');
    }

    public function show(SalesRep $sales_rep, Target $target)
    {
        return view('targets.show', compact('target', 'sales_rep'));
    }

    public function edit(SalesRep $sales_rep, Target $target)
    {
        $services = Service::all();
        return view('targets.edit', compact('target', 'services', 'sales_rep'));
    }

    public function update(Request $request, SalesRep $sales_rep, Target $target)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|digits:4',
            'target_amount' => 'required|numeric|min:0',
        ]);

        $target->update($validated);

        return redirect()->route('sales-rep.targets.index', $sales_rep->id)
            ->with('success', 'Target updated successfully.');
    }

    public function destroy(SalesRep $sales_rep, Target $target)
    {
        $target->delete();

        return redirect()->route('sales-rep.targets.index', $sales_rep->id)
            ->with('success', 'Target deleted successfully.');
    }
}
