<?php

namespace App\Policies;

use App\Models\Agreement;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AgreementPolicy
{
        use HandlesAuthorization;
    /**
     * Determine whether the user can view any models.
     */
    public function before(User $user, $ability)
    {
        if ($user->hasRole('admin')) {
            return true;
        }
    }

    /**
     * Check if the agreement belongs to the sales rep of the authenticated user.
     */
    protected function isAgreementOwner(User $user, Agreement $agreement): bool
    {
        return $user->salesRep && $user->salesRep->id === $agreement->sales_rep_id;
    }

    public function view(User $user, Agreement $agreement): bool
    {
        return $this->isAgreementOwner($user, $agreement);
    }

    public function update(User $user, Agreement $agreement): bool
    {
        return $this->isAgreementOwner($user, $agreement);
    }

    public function delete(User $user, Agreement $agreement): bool
    {
        return $this->isAgreementOwner($user, $agreement);
    }

    public function restore(User $user, Agreement $agreement): bool
    {
        return $this->isAgreementOwner($user, $agreement);
    }

    public function forceDelete(User $user, Agreement $agreement): bool
    {
        return $this->isAgreementOwner($user, $agreement);
    }

    public function viewAny(User $user): bool
    {
        return $user->salesRep !== null;
    }

    public function create(User $user): bool
    {
        return $user->salesRep !== null;
    }

    }
