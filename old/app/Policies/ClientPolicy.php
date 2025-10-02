<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\SalesRep;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClientPolicy
{
    use HandlesAuthorization;

    /**
     * Grant all access to admins.
     */
    public function before(User $user, $ability)
    {
        if ($user->hasRole('admin')) {
            return true;
        }
    }

    /**
     * Determine whether the sales rep can view a client.
     */
//  public function view(SalesRep $salesRep, Client $client): bool
//     {
//         return $salesRep->id === $client->sales_rep_id;
//     }

    public function update(SalesRep $salesRep, Client $client): bool
    {
        return $salesRep->id === $client->sales_rep_id;
    }

    public function delete(SalesRep $salesRep, Client $client): bool
    {
        return false;
    }

    public function restore(SalesRep $salesRep, Client $client): bool
    {
        return $salesRep->id === $client->sales_rep_id;
    }

    public function forceDelete(SalesRep $salesRep, Client $client): bool
    {
        return $salesRep->id === $client->sales_rep_id;
    }

    public function viewAny(SalesRep $salesRep): bool
    {
        return true;
    }

    public function create(SalesRep $salesRep): bool
    {
        return true;
    }
}
