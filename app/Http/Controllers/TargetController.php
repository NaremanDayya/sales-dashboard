<?php

namespace App\Http\Controllers;

use App\Models\SalesRep;
use App\Models\Service;
use App\Models\Target;
use App\Models\User;
use App\Models\Commission;
use App\Services\TargetService;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TargetController extends Controller
{
    public function index(SalesRep $salesRep, TargetService $targetService)
    {
        $now = now();
        $selectedYear = (int) request('year', $now->year);
        $selectedMonth = request('month') ? (int) request('month') : null; // null = show all months

        $services = Service::all();

        // Sales rep start date
        $startDate = $salesRep->start_work_date;
        $startYear = $startDate?->year;
        $startMonth = $startDate?->month;

        // Which month's numbers back the summary columns (target/carried-over/
        // achieved-this-month): the filtered month if one is picked, otherwise
        // the current calendar month (only meaningful when viewing the current year).
        $summaryMonth = $selectedMonth ?? ($selectedYear === $now->year ? $now->month : null);

        // Make sure the summary month's target (with carry-over compounded from
        // every eligible month since the rep joined) exists before we read it, in
        // case the monthly generation job hasn't caught up yet. Never generate for
        // a month that hasn't happened yet.
        if ($summaryMonth) {
            $summaryDate = Carbon::create($selectedYear, $summaryMonth, 1);
            if (!$summaryDate->startOfMonth()->gt($now->copy()->startOfMonth())) {
                foreach ($services as $service) {
                    $targetService->getOrCreateTarget($salesRep->id, $service->id, $summaryDate);
                }
            }
        }

        // All of this rep's targets for the selected year, grouped by service then keyed by month.
        $targetsByService = Target::where('sales_rep_id', $salesRep->id)
            ->where('year', $selectedYear)
            ->with('commissions')
            ->get()
            ->groupBy('service_id');

        $data = $services->map(function ($service) use (
            $salesRep, $targetsByService, $selectedYear, $selectedMonth,
            $summaryMonth, $startYear, $startMonth, $now
        ) {
            $monthsForService = ($targetsByService->get($service->id) ?? collect())->keyBy('month');
            $target = $summaryMonth ? $monthsForService->get($summaryMonth) : null;

            // How much of the summary month's target came from prior shortfalls.
            $carriedOver = $target ? number_format($target->target_amount - $service->target_amount) : 0;

            // Determine commission according to month filter
            $commissionForMonth = null;
            if ($selectedMonth) {
                $commissionForMonth = $target?->commissions?->where('month', $selectedMonth)?->first();
            }

            // Year-level aggregates are independent of any single month's target
            // row existing, so compute them regardless of whether $target is set.
            $yearAggregator = new Target();

            $row = [
                'service_type' => $service->name,
                'target_amount' => number_format($service->target_amount),
                'current_month_achieved_amount' => (int) ($target?->achieved_amount ?? 0),
                'year_achieved_target' => $yearAggregator->yearAchievedAmount($service, $salesRep, $selectedYear),
                'year_achieved_amount' => $yearAggregator->yearAchievedAmountValue($service, $salesRep, $selectedYear),
                'commission_status' => $commissionForMonth?->commission_status ?? 'غير مستحق',
                'commission_value' => $commissionForMonth?->commission_amount ?? 0,
                'commission_id' => $commissionForMonth?->id ?? null,
                'needed_achieved_percentage' => $target?->needed_achieved_percentage ?? 0,
                'actual_target_amount' => $target ? number_format($target->target_amount) : 0,
                'carried_over_amount' => $carriedOver,
                // Banked over-achievement auto-filling future months, one month's
                // target at a time, until exhausted.
                'surplus_carried_amount' => (float) ($target?->surplus_carried_amount ?? 0),
                'achieved_target_percentage_needed' => Setting::where('key', 'commission_threshold')->value('value') ?? 90,
            ];

            // Monthly values - show ALL months of the selected year
            for ($month = 1; $month <= 12; $month++) {

                // Before the sales rep's start date, or a month that hasn't happened yet.
                $isBeforeStartDate = $startYear && $startMonth &&
                    ($selectedYear < $startYear ||
                        ($selectedYear == $startYear && $month < $startMonth));

                $isFutureMonth = $selectedYear > $now->year ||
                    ($selectedYear == $now->year && $month > $now->month);

                if ($isBeforeStartDate || $isFutureMonth) {
                    $row["month_achieved_$month"] = '-';
                    $row["commission_value_month_$month"] = 0;
                    $row["commission_payment_status_month_$month"] = 0;
                    $row["commission_status_month_$month"] = 'غير مستحق';
                    $row["commission_id_month_$month"] = null;
                } else {
                    $monthTarget = $monthsForService->get($month);

                    // month_achieved_$month is rendered as a PERCENTAGE of that
                    // month's own target, not a raw amount - read it straight off
                    // the stored, already-computed achieved_percentage.
                    $row["month_achieved_$month"] = $monthTarget
                        ? number_format($monthTarget->achieved_percentage, 2)
                        : '-';

                    $monthCommission = $monthTarget?->commissions?->where('month', $month)?->first();
                    $row["commission_value_month_$month"] = $monthCommission?->commission_amount ?? 0;
                    $row["commission_payment_status_month_$month"] = $monthCommission?->payment_status ?? 0;

                    // Use the commission_due column from THAT month's target row.
                    $commissionDue = $monthTarget?->commission_due ?? false;

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
