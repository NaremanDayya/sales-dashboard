<?php

namespace App\Http\Controllers;

use App\Models\SalesRep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManagerDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $salesRep = $user->getEffectiveSalesRep();

        if (!$salesRep || !$salesRep->isManager()) {
            abort(403, 'You do not have manager privileges.');
        }

        $teamMembers = $salesRep->teamMembers()->with(['user', 'clients', 'agreements'])->get();
        
        $teamStats = [
            'total_members' => $teamMembers->count(),
            'total_clients' => $salesRep->getTeamClientsQuery()->count(),
            'total_agreements' => $salesRep->getTeamAgreementsQuery()->count(),
            'active_agreements' => $salesRep->getTeamAgreementsQuery()->where('agreement_status', 'active')->count(),
        ];

        return view('manager.dashboard', compact('salesRep', 'teamMembers', 'teamStats'));
    }

    public function teamMemberDetails(SalesRep $teamMember)
    {
        $user = Auth::user();
        $salesRep = $user->getEffectiveSalesRep();

        if (!$user->isAdmin()) {
            if (!$salesRep || !$salesRep->isManager()) {
                abort(403, 'You do not have manager privileges.');
            }

            if ($teamMember->manager_id !== $salesRep->id) {
                abort(403, 'This sales representative is not in your team.');
            }
        }

        $clients = $teamMember->clients()->with('agreements')->paginate(20);
        $agreements = $teamMember->agreements()->with('client')->paginate(20);

        return view('manager.team-member-details', compact('teamMember', 'clients', 'agreements'));
    }

    public function teamClients()
    {
        $user = Auth::user();
        $salesRep = $user->getEffectiveSalesRep();

        if (!$salesRep || !$salesRep->isManager()) {
            abort(403, 'You do not have manager privileges.');
        }

        $clients = $salesRep->getTeamClientsQuery()
            ->with(['salesRep.user', 'agreements'])
            ->paginate(20);

        return view('manager.team-clients', compact('clients', 'salesRep'));
    }

    public function teamAgreements()
    {
        $user = Auth::user();
        $salesRep = $user->getEffectiveSalesRep();

        if (!$salesRep || !$salesRep->isManager()) {
            abort(403, 'You do not have manager privileges.');
        }

        $agreements = $salesRep->getTeamAgreementsQuery()
            ->with(['client', 'salesRep.user'])
            ->paginate(20);

        return view('manager.team-agreements', compact('agreements', 'salesRep'));
    }
}
