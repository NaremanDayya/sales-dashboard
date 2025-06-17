<?php

namespace App\Http\Controllers;

use App\Models\SalesRep;
use App\Models\Service;
use App\Models\Target;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TargetController extends Controller
{
    public function index(SalesRep $salesRep)
    {
        $services = Service::all();

        // Fetch this sales rep’s targets and key by service_id for quick access
        $targetsByService = $salesRep->targets()->with('service')->get()->keyBy('service_id');

        $data = $services->map(function ($service) use ($salesRep, $targetsByService) {
            $target = $targetsByService->get($service->id);

            $row = [
                'service_type' => $service->name,
                'target_amount' => number_format($service->target_amount),
                'year_achieved_target' => $target?->yearAchievedAmount($service, $salesRep) ?? 0,
                'commission_status' => $target?->commission_status ?? 'غير مستحق',
                'current_month_achieved' => $salesRep->serviceMonthAchievedAmount($service),
            ];

            for ($month = 1; $month <= 12; $month++) {
                $row["month_achieved_$month"] = $target?->monthAchievedAmount($month, $service, $salesRep) ?? 0;
            }

            return $row;
        });

        return view('targets.table', ['Targets' => $data]);
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
