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
    $services = Service::all();

    // Get sales rep's start date
    $startDate = $salesRep->start_work_date;
    $startYear = $startDate?->year;
    $startMonth = $startDate?->month;

    // Fetch targets with commission data for this sales rep and year
    $targetsByService = Target::where('sales_rep_id', $salesRep->id)
    ->get()
    ->keyBy('service_id');

    $data = $services->map(function ($service) use ($salesRep, $targetsByService, $selectedYear, $startYear,
    $startMonth) {
    // Get target for this service and sales rep
    $target = $targetsByService->get($service->id);
    $carriedOver = $target ? number_format($target->target_amount - $service->target_amount) : 0;

    $row = [
        'service_type' => $service->name,
        'target_amount' => number_format($service->target_amount),
        'year_achieved_target' => $target?->yearAchievedAmount($service, $salesRep) ?? 0,
        'commission_status' => $target?->commission_status ?? 'غير مستحق',
        'commission_value' => $target->commission_value ?? 0,
        'commission_id' => $target->commission?->id ?? null,
        'needed_achieved_percentage' => $target->needed_achieved_percentage ?? 0,
	'actual_target_amount' =>  number_format($target?->target_amount) ?? 0,
        'carried_over_amount' => $carriedOver,
	'achieved_target_percentage_needed' => Setting::where('key', 'commission_threshold')->value('value') ?? 90,    
];

    // Calculate monthly values
    for ($month = 1; $month <= 12; $month++) {
        if ($startYear && $startMonth) {
            if ($selectedYear < $startYear || ($selectedYear == $startYear && $month < $startMonth)) {
                $row["month_achieved_" . $month] = '-';
                $row["commission_value_month_" . $month] = 0;
                $row["commission_payment_status_month_" . $month] = 0;
                continue;
            }
        }

        $row["month_achieved_" . $month] = number_format($target?->monthAchievedAmount($month, $service, $salesRep) ?? 0);

        // Get commission for this month (if exists)
        // This will use the eager loaded commissions
//dd($target->commission);
$targets = Target::with('commission')->get();

foreach ($targets as $target) {
    $commission = $target->commissions?->first();  // safe because of eager loading
    $row["commission_value_month_" . $month] = $commission?->commission_amount ?? 0;
    $row["commission_payment_status_month_" . $month] = $commission?->payment_status ?? 0;
}
}
    return $row;
});
//dd($data);
        return view('targets.table', [
        'Targets' => $data,
        'selectedYear' => $selectedYear,
        'salesRep' => $salesRep,
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
