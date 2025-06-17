<?php

namespace App\Http\Controllers;

use App\Models\Agreement;
use App\Models\AgreementEditRequest;
use App\Models\Client;
use App\Models\ClientEditRequest;
use App\Models\SalesRep;
use App\Models\Target;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;



class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role == 'admin') {
            // Admin dashboard
            $salesReps = SalesRep::all()->map(function ($rep) {
                return [
                     'id' => $rep->id,
                'name' => $rep->name,
                'start_work_date' => $rep->start_work_date->format('Y-m-d'),
                'work_duration' => $rep->work_duration,
                'target_customers' => $rep->clients->count(),
                'late_customers' => $rep->lateCustomers,
                'total_orders' => $rep->totalOrders,
                'pending_orders' => $rep->totalPendedRequests,
                'interested_customers' => $rep->interestedClients->count(),
                'achieved_target_percentage' => (int) ($rep->currentMonthAchievedPercentage()),
                'achieved_target_amount' =>(int) ($rep->currentMonthAchievedAmount())

                ];
            });


            return view('salesRep.index', data: compact('salesReps'));

            // $data = $this->getAdminDashboardData();
            // return view('dashboards.AdminDashboard', compact('data'));
        } elseif ($user->role == 'salesRep') {
            // Sales Rep dashboard
            $salesRep = SalesRep::where('user_id', Auth::id())->first();
            $user = $salesRep->user;
            $user->load('permissions');

            $translatedPermissions = $user->permissions->map(function ($permission) {
                return __($permission->name);
            });
            return view('profile.show', compact('salesRep', 'user','translatedPermissions'));
            // $data = $this->getSalesRepDashboardData();
            // return view('dashboards.salesRepDashboard', $data); // Notice: $data is an array, not compact()
        } else {
            abort(403, 'Unauthorized');
        }
    }
    private function getAdminDashboardData()
    {
        $allSalesReps = SalesRep::count();
        $activeRepsCount = User::where('role', 'sales_rep')
            ->where('account_status', 'active')
            ->count();

        $newRepsThisMonth = User::where('role', 'sales_rep')
            ->where('account_status', 'active')
            ->whereMonth('created_at', now()->month)
            ->count();

        $repsGrowth = $this->calculateGrowthRate(
            User::where('role', 'sales_rep')
                ->where('account_status', 'active')
                ->whereMonth('created_at', now()->subMonth()->month)
                ->count(),
            $activeRepsCount
        );

        // Get clients data
        $totalClients = Client::count();
        $interestedClients = Client::where('interest_status', 'interested')->count();
        $notInterestedClients = $totalClients - $interestedClients;

        // Get agreements data
        $totalAgreements = Agreement::count();
        $activeAgreements = Agreement::where('agreement_status', 'active')->count();
        $pendingAgreements = Agreement::where('agreement_status', 'terminated')->count();
        $expiredAgreements = Agreement::where('agreement_status', 'expired')->count();
        $expiringSoon = Agreement::where('end_date', '<=', now()->addDays(30))
            ->where('end_date', '>=', now())
            ->count();
        $performanceCounts = SalesRep::with([
            'targets' => function ($q) {
                $q->latest(); // or orderBy('year', 'desc')->orderBy('month', 'desc')
            },
            'user'
        ])
            ->whereHas('user', function ($query) {
                $query->where('account_status', 'active');
            })
            ->get()
            ->groupBy(function ($rep) {
                $latestTarget = $rep->targets->first(); // get most recent
                $achieved = $latestTarget?->achieved_percentage ?? 0;

                if ($achieved >= 100)
                    return 'achieved';
                if ($achieved >= 50)
                    return 'partially_achieved';
                return 'not_achieved';
            })
            ->map->count();

        // Pending requests
        $pendingRequestsCount = ClientEditRequest::where('status', 'pending')->count()
            + AgreementEditRequest::where('status', 'pending')->count();
        return [
            'allSalesReps' => $allSalesReps,
            'activeRepsCount' => $activeRepsCount,
            'newRepsThisMonth' => $newRepsThisMonth,
            'repsGrowth' => $repsGrowth,
            'totalClients' => $totalClients,
            'inerestedClients' => $interestedClients,
            'notInerestedClients' => $notInterestedClients,
            'totalAgreements' => $totalAgreements,
            'activeAgreements' => $activeAgreements,
            'pendingAgreements' => $pendingAgreements,
            'expiredAgreements' => $expiredAgreements,
            'expiringSoon' => $expiringSoon,
            'pendingRequestsCount' => $pendingRequestsCount,
            'onTargetCount' => $performanceCounts['achieved'] ?? 0,
            'nearTargetCount' => $performanceCounts['partially_achieved'] ?? 0,
            'belowTargetCount' => $performanceCounts['not_achieved'] ?? 0,
        ];
    }
    private function calculateGrowthRate($previousValue, $currentValue)
    {
        if ($previousValue == 0) {
            return $currentValue > 0 ? 100 : 0;
        }

        return round((($currentValue - $previousValue) / $previousValue) * 100);
    }
    public function getSalesRepDashboardData(): array
    {
        $user = Auth::user();

        $clientsCount = $user->user?->clients->count();

        $newClientsThisMonth = $user->user?->clients()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $serviceTargets = Target::with('service')
            ->where('sales_rep_id', $user->id)
            ->where('month', now()->month)
            ->where('year', now()->year)
            ->get();

        $totalTarget = $serviceTargets->sum('target_amount');
        $totalAchieved = $serviceTargets->sum('achieved_amount');

        return [
            'clientsCount' => $clientsCount,
            'newClientsThisMonth' => $newClientsThisMonth,
            'serviceTargets' => $serviceTargets,
            'totalTarget' => $totalTarget,
            'totalAchieved' => $totalAchieved,
        ];
    }
}
