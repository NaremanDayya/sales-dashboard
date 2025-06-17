<?php

namespace App\Policies;

use App\Models\SalesRep;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SalesRepPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewOwn(User $user, SalesRep $salesRep): bool
    {
        return $user->id === $salesRep->user_id;
    }
}
