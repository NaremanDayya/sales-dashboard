<?php

namespace App\Http\Controllers;

use App\Models\Commission;
use App\Models\SalesRep;
use App\Models\Service;
use Illuminate\Http\Request;

class AdminCommissionController extends Controller
{
    public function index(SalesRep $salesRep)
    {
        $selectedYear = request('year', now()->year);
        $selectedMonth = request('month'); // Add month filter

        $services = Service::all();

        // Get the sales rep's start date
        $startDate = $salesRep->start_work_date;
        $startYear = $startDate?->year;
        $startMonth = $startDate?->month;

        // Fetch commissions for the selected year with month filter
        $commissionsQuery = $salesRep->commissions()
            ->where('year', $selectedYear);

        // Apply month filter if selected
        if ($selectedMonth) {
            $commissionsQuery->where('month', $selectedMonth);
        }

        $commissionsByServiceAndMonth = $commissionsQuery
            ->with(['service', 'target'])
            ->get()
            ->groupBy(['service_id', 'month']);

        $data = $services->map(function ($service) use ($salesRep, $commissionsByServiceAndMonth, $selectedYear, $selectedMonth, $startYear, $startMonth) {
            // Use selected month or current month
            $displayMonth = $selectedMonth ?: date('n');

            // Get the display month's commission specifically
            $displayMonthCommission = $commissionsByServiceAndMonth
                ->get($service->id)
                ?->get($displayMonth)
                ?->first();

            // Calculate yearly totals
            $yearCommissions = $commissionsByServiceAndMonth->get($service->id) ?? collect();
            $yearAchievedAmount = $yearCommissions->flatten()->sum(fn($c) => $c->target?->achieved_amount ?? 0);
            $totalTargetAmount = $yearCommissions->flatten()->sum(fn ($c) => $c->target?->target_amount ?? 0);

            $achievedPercentage = $totalTargetAmount > 0
                ? ($yearAchievedAmount / $totalTargetAmount) * 100
                : 0;

            $row = [
                'service_type' => $service->name,
                'total_achieved_amount' => $yearAchievedAmount,
                'achieved_percentage' => $achievedPercentage,
                'commission_rate' => $service?->commission_rate ?? 0,
                'total_commission' => $yearCommissions->flatten()->sum('commission_amount'),
                'id' => $displayMonthCommission?->id ?? 0,
                'item_fee' => $displayMonthCommission?->item_fee ?? 0,
                'calculation_type' => $displayMonthCommission?->calculation_type ?? 'rate',
                'commission_amount' => $displayMonthCommission?->commission_amount ?? 0,
                'month_achieved_amount' => $displayMonthCommission?->target?->achieved_amount ?? 0,
                'display_month' => $displayMonth, // Add display month for reference
            ];

            // Add monthly commission data
            for ($month = 1; $month <= 12; $month++) {
                if ($startYear && $startMonth) {
                    if ($selectedYear < $startYear || ($selectedYear == $startYear && $month < $startMonth)) {
                        $row["month_commission_$month"] = '-';
                        $row["payment_status_month_$month"] = 0;
                        continue;
                    }
                }

                $monthCommission = $commissionsByServiceAndMonth
                    ->get($service->id)
                    ?->get($month)
                    ?->first();

                $row["month_commission_$month"] = $monthCommission?->commission_amount ?? 0;
                $row["payment_status_month_$month"] = $monthCommission?->payment_status ?? 0;
                $row["commission_id_month_$month"] = $monthCommission?->id ?? null; // Add commission ID for each month
            }

            return $row;
        });

        return view('salesRep.commissions', [
            'Commissions' => $data,
            'selectedYear' => $selectedYear,
            'selectedMonth' => $selectedMonth, // Pass selected month to view
            'salesRep' => $salesRep
        ]);
    }
public function show(Commission $commission)
{
    $commission->load(['salesRep', 'service', 'target']);

    return view('commissions.show', [
        'commission' => $commission,
    ]);
}

public function updateCommissionType(Request $request, Commission $commission)
{
    $validated = $request->validate([
        'type' => 'required|in:rate,item',
        'item_fee' => 'nullable|numeric|min:0'
    ]);

    if ($validated['type'] === 'item') {
        $achievedAmount = $commission->target->achieved_amount;
        $commissionAmount = $achievedAmount * $validated['item_fee'];
        $commission->update([
            'commission_amount' => $commissionAmount,
            'commission_rate' => 0,
            'item_fee' => $validated['item_fee'],
            'calculation_type' => 'item',
        ]);
    } else {
        $commissionRate = $commission->target->service->commission_rate;

        $commissionAmount = ($commissionRate / 100) * $commission->total_achieved_amount;

        $commission->update([
            'commission_amount' => $commissionAmount,
            'commission_rate' => $commissionRate,
            'item_fee' => null,
            'calculation_type' => 'rate',
        ]);

    }

    return redirect()->back()->with('success', 'تم حساب العمولة بنجاح');
}


}
