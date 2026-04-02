<?php

namespace App\Http\Controllers;

use App\Models\SalesRep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminImpersonationController extends Controller
{
    public function start(SalesRep $manager)
    {
        $user = Auth::user();

        if (!$user->isAdmin()) {
            abort(403, 'Only admins can impersonate managers.');
        }

        if (!$manager->isManager()) {
            abort(400, 'This sales representative is not a manager.');
        }

        $user->startImpersonating($manager);

        return redirect()->route('manager.dashboard')->with('success', 'Now viewing as ' . $manager->name);
    }

    public function stop()
    {
        $user = Auth::user();

        if (!$user->isAdmin()) {
            abort(403, 'Only admins can stop impersonation.');
        }

        $user->stopImpersonating();

        return redirect()->route('admin.dashboard')->with('success', 'Stopped impersonating manager.');
    }
}
