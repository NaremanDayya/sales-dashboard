<?php

namespace App\Policies;

use App\Models\SalesRep;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SalesRepPolicy
{
    public function viewOwn(User $user, SalesRep $salesRep): bool
    {
        return $user->id === $salesRep->user_id;
    }

    public function assignManager(User $user, SalesRep $salesRep): bool
    {
        return $user->isAdmin();
    }

    public function viewAsManager(User $user, SalesRep $salesRep): bool
    {
        $userSalesRep = $user->getEffectiveSalesRep();
        
        if (!$userSalesRep) {
            return false;
        }

        return $salesRep->manager_id === $userSalesRep->id;
    }

    public function viewTeamData(User $user): bool
    {
        $salesRep = $user->getEffectiveSalesRep();
        return $salesRep && $salesRep->isManager();
    }
}
