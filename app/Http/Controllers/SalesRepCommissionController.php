<?php

namespace App\Http\Controllers;

use App\Models\SalesRep;
use App\Models\Commission;
use Illuminate\Http\Request;

class SalesRepCommissionController extends Controller
{
    public function index(SalesRep $salesRep)
    {
        // Authorized via route middleware
        $commissions = $salesRep->commissions()
            ->with(['service', 'target'])
            ->latest('year')
            ->latest('month')
            ->paginate(10);

        return view('commissions.sales-rep-index', [
            'salesRep' => $salesRep,
            'commissions' => $commissions
        ]);
    }

    public function show(SalesRep $salesRep, Commission $commission)
    {
        // Authorized via route middleware
        $commission->load(['service', 'target']);

        return view('commissions.sales-rep-show', [
            'salesRep' => $salesRep,
            'commission' => $commission
        ]);
    }

    public function export(SalesRep $salesRep, Commission $commission)
    {
        // Authorized via route middleware
        return $commission->exportPdf();
    }
}
