<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalesRep;
use App\Models\SalesRepWorkHistory;
use Illuminate\Http\Request;

class WorkHistoryController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user->role === 'admin';

        $query = SalesRepWorkHistory::query();

        if (!$isAdmin) {
            abort_unless($user->salesRep, 403);
            $query->where('sales_rep_id', $user->salesRep->id);
        } elseif ($request->filled('sales_rep_id')) {
            $query->where('sales_rep_id', $request->sales_rep_id);
        }

        if ($isAdmin && $request->filled('search')) {
            $query->where('sales_rep_name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('year')) {
            $query->whereYear('start_date', $request->year);
        }

        $histories = $query->orderByDesc('end_date')->paginate(15)->withQueryString();

        $years = SalesRepWorkHistory::selectRaw('DISTINCT YEAR(start_date) as year')
            ->orderByDesc('year')
            ->pluck('year');

        $filteredSalesRep = $isAdmin
            ? ($request->filled('sales_rep_id') ? SalesRep::find($request->sales_rep_id) : null)
            : $user->salesRep;

        return view('admin.work-history.index', compact('histories', 'years', 'filteredSalesRep', 'isAdmin'));
    }
}
