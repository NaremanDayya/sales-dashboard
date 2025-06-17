<?php

namespace App\Http\Controllers;

use App\Models\Commission;
use App\Models\SalesRep;
use Illuminate\Http\Request;

class AdminCommissionController extends Controller
{
public function index(Request $request)
{
    $commissions = Commission::with(['salesRep', 'service', 'target'])
        ->when($request->month, function($query) use ($request) {
            $query->where('month', $request->month);
        })
        ->when($request->year, function($query) use ($request) {
            $query->where('year', $request->year);
        })
        ->when($request->sales_rep, function($query) use ($request) {
            $query->where('sales_rep_id', $request->sales_rep);
        })
        ->orderBy('year', 'desc')
        ->orderBy('month', 'desc')
        ->paginate(15);

    $totalCommissions = $commissions->sum('commission_amount');
    $topPerformer = Commission::with('salesRep')
        ->selectRaw('sales_rep_id, SUM(commission_amount) as commission_amount')
        ->groupBy('sales_rep_id')
        ->orderBy('commission_amount', 'desc')
        ->first();

    $topService = Commission::with('service')
        ->selectRaw('service_id, SUM(total_achieved_amount) as total_achieved_amount')
        ->groupBy('service_id')
        ->orderBy('total_achieved_amount', 'desc')
        ->first();

    $averageAchievement = Commission::avg('achieved_percentage');

    return view('commissions.index', [
        'commissions' => $commissions,
        'salesReps' => SalesRep::all(),
        'totalCommissions' => $totalCommissions,
        'topPerformer' => $topPerformer,
        'topService' => $topService,
        'averageAchievement' => $averageAchievement
    ]);
}

public function show(Commission $commission)
{
    $commission->load(['salesRep', 'service', 'target']);

    return view('commissions.show', [
        'commission' => $commission,
    ]);
}
}
