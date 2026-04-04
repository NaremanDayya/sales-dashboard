<?php

namespace App\Http\Controllers\Admin;

use App\Models\SalesRep;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TeamManagementController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user->isAdmin()) {
            abort(403, 'You do not have admin privileges.');
        }

        // Get all managers with their team members
        $managers = SalesRep::managers()
            ->with(['user', 'teamMembers.user', 'teamMembers.clients', 'teamMembers.agreements'])
            ->get();

        // Get all sales reps with their stats
        $allSalesReps = SalesRep::with(['user', 'clients', 'agreements', 'manager'])
            ->get();

        $totalStats = [
            'total_sales_reps' => $allSalesReps->count(),
            'total_managers' => $managers->count(),
            'total_clients' => $allSalesReps->sum(fn($rep) => $rep->clients->count()),
            'total_agreements' => $allSalesReps->sum(fn($rep) => $rep->agreements->count()),
            'active_agreements' => $allSalesReps->sum(fn($rep) => $rep->active_agreements_count),
        ];

        return view('admin.team-management.index', compact('managers', 'allSalesReps', 'totalStats'));
    }

    public function teamMemberDetails(SalesRep $teamMember)
    {
        $user = Auth::user();

        if (!$user->isAdmin()) {
            abort(403, 'You do not have admin privileges.');
        }

        $clients = $teamMember->clients()->with('agreements')->paginate(20);
        $agreements = $teamMember->agreements()->with('client')->paginate(20);

        return view('admin.team-management.team-member-details', compact('teamMember', 'clients', 'agreements'));
    }

    public function managerTeam(SalesRep $manager)
    {
        $user = Auth::user();

        if (!$user->isAdmin()) {
            abort(403, 'You do not have admin privileges.');
        }

        if (!$manager->isManager()) {
            abort(404, 'This sales rep is not a manager.');
        }

        $teamMembers = $manager->teamMembers()->with(['user', 'clients', 'agreements'])->get();
        
        $teamStats = [
            'total_members' => $teamMembers->count(),
            'total_clients' => $manager->getTeamClientsQuery()->count(),
            'total_agreements' => $manager->getTeamAgreementsQuery()->count(),
            'active_agreements' => $manager->getTeamAgreementsQuery()->where('agreement_status', 'active')->count(),
        ];

        return view('admin.team-management.manager-team', compact('manager', 'teamMembers', 'teamStats'));
    }

    public function teamClients(SalesRep $manager)
    {
        $user = Auth::user();

        if (!$user->isAdmin()) {
            abort(403, 'You do not have admin privileges.');
        }

        if (!$manager->isManager()) {
            abort(404, 'This sales rep is not a manager.');
        }

        $clients = $manager->getTeamClientsQuery()
            ->with(['salesRep.user', 'agreements'])
            ->paginate(20);

        return view('admin.team-management.team-clients', compact('clients', 'manager'));
    }

    public function teamAgreements(SalesRep $manager)
    {
        $user = Auth::user();

        if (!$user->isAdmin()) {
            abort(403, 'You do not have admin privileges.');
        }

        if (!$manager->isManager()) {
            abort(404, 'This sales rep is not a manager.');
        }

        $agreements = $manager->getTeamAgreementsQuery()
            ->with(['client', 'salesRep.user'])
            ->paginate(20);

        return view('admin.team-management.team-agreements', compact('agreements', 'manager'));
    }

    public function allTeamClients()
    {
        $user = Auth::user();

        if (!$user->isAdmin()) {
            abort(403, 'You do not have admin privileges.');
        }

        $clients = \App\Models\Client::with(['salesRep.user', 'salesRep.manager', 'agreements'])
            ->paginate(20);

        return view('admin.team-management.all-clients', compact('clients'));
    }

    public function allTeamAgreements()
    {
        $user = Auth::user();

        if (!$user->isAdmin()) {
            abort(403, 'You do not have admin privileges.');
        }

        $agreements = \App\Models\Agreement::with(['client', 'salesRep.user', 'salesRep.manager'])
            ->paginate(20);

        return view('admin.team-management.all-agreements', compact('agreements'));
    }
}
