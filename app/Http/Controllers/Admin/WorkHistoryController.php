<?php

namespace App\Http\Controllers\Admin;

use App\Exports\WorkHistoryExport;
use App\Http\Controllers\Controller;
use App\Models\SalesRep;
use App\Models\SalesRepWorkHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Maatwebsite\Excel\Facades\Excel;

class WorkHistoryController extends Controller
{
    public function index(Request $request)
    {
        $rows = $this->buildRows($request);

        $stats = [
            'total_periods' => $rows->count(),
            'active_periods' => $rows->where('is_active', true)->count(),
            'total_work_days' => (int) $rows->sum('duration_days'),
            'average_days' => $rows->count() ? (int) round($rows->avg('duration_days')) : 0,
        ];

        $page = $request->integer('page', 1);
        $perPage = 15;
        $paginated = new LengthAwarePaginator(
            $rows->forPage($page, $perPage)->values(),
            $rows->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $isAdmin = auth()->user()->role === 'admin';
        $filteredSalesRep = $isAdmin && $request->filled('sales_rep_id')
            ? SalesRep::find($request->sales_rep_id)
            : (!$isAdmin ? auth()->user()->salesRep : null);

        return view('admin.work-history.index', [
            'histories' => $paginated,
            'stats' => $stats,
            'filteredSalesRep' => $filteredSalesRep,
            'isAdmin' => $isAdmin,
        ]);
    }

    public function export(Request $request)
    {
        $rows = $this->buildRows($request);

        return Excel::download(new WorkHistoryExport($rows), 'سجل-عمل-الموظفين.xlsx');
    }

    /**
     * Build the merged, filtered, sorted collection of work-history rows:
     * closed periods from sales_rep_work_histories + a live "still active"
     * row for every currently active sales rep.
     */
    private function buildRows(Request $request): \Illuminate\Support\Collection
    {
        $user = auth()->user();
        $isAdmin = $user->role === 'admin';

        $search = $request->input('search');
        $status = $request->input('status'); // active | ended | null
        $fromDate = $request->filled('from_date') ? Carbon::parse($request->input('from_date'))->startOfDay() : null;
        $toDate = $request->filled('to_date') ? Carbon::parse($request->input('to_date'))->endOfDay() : null;
        $scopedSalesRepId = $isAdmin ? $request->input('sales_rep_id') : optional($user->salesRep)->id;

        $rows = collect();

        // Closed periods (real DB records)
        if ($status !== 'active') {
            $closedQuery = SalesRepWorkHistory::with(['salesRep.user']);

            if (!$isAdmin) {
                abort_unless($user->salesRep, 403);
                $closedQuery->where('sales_rep_id', $user->salesRep->id);
            } elseif ($scopedSalesRepId) {
                $closedQuery->where('sales_rep_id', $scopedSalesRepId);
            }

            if ($search) {
                $closedQuery->where('sales_rep_name', 'like', '%' . $search . '%');
            }
            if ($fromDate) {
                $closedQuery->where('end_date', '>=', $fromDate);
            }
            if ($toDate) {
                $closedQuery->where('start_date', '<=', $toDate);
            }

            foreach ($closedQuery->get() as $history) {
                $rows->push($this->toRow(
                    $history->salesRep,
                    $history->sales_rep_name,
                    $history->start_date,
                    $history->end_date
                ));
            }
        }

        // Currently active reps: a live, still-open period
        if ($status !== 'ended') {
            $activeQuery = SalesRep::with('user')->whereHas('user', function ($q) {
                $q->where('account_status', 'active');
            });

            if (!$isAdmin) {
                abort_unless($user->salesRep, 403);
                $activeQuery->where('id', $user->salesRep->id);
            } elseif ($scopedSalesRepId) {
                $activeQuery->where('id', $scopedSalesRepId);
            }

            if ($search) {
                $activeQuery->where('name', 'like', '%' . $search . '%');
            }

            foreach ($activeQuery->get() as $rep) {
                if (!$rep->start_work_date) {
                    continue;
                }
                // Respect an explicit date-range filter: skip if the open period starts after the range end
                if ($toDate && Carbon::parse($rep->start_work_date)->gt($toDate)) {
                    continue;
                }
                if ($fromDate && Carbon::now()->lt($fromDate)) {
                    continue;
                }

                $rows->push($this->toRow($rep, $rep->name, $rep->start_work_date, null));
            }
        }

        return $rows->sortByDesc(fn ($row) => $row['sort_date'])->values();
    }

    private function toRow(?SalesRep $salesRep, string $name, $startDate, $endDate): array
    {
        $temp = new SalesRepWorkHistory([
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        return [
            'sales_rep_id' => $salesRep?->id,
            'name' => $name,
            'email' => optional($salesRep?->user)->email,
            'avatar' => optional($salesRep?->user)->personal_image,
            'start_date' => $temp->start_date,
            'end_date' => $temp->end_date,
            'is_active' => $temp->is_active,
            'period_label' => $temp->period,
            'duration_days' => $temp->duration_in_days,
            'sort_date' => $temp->end_date ?? Carbon::now(),
        ];
    }
}
