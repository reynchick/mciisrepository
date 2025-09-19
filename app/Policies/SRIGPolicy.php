<?php

namespace App\Policies;

use App\Models\SRIG;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SRIGPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isFaculty() || $user->isAdminOrStaff();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SRIG $sRIG): bool
    {
        return $user->isFaculty() || $user->isAdminOrStaff();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdminOrStaff();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SRIG $sRIG): bool
    {
        return $user->isAdminOrStaff();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SRIG $sRIG): bool
    {
        return $user->isAdminOrStaff();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SRIG $sRIG): bool
    {
        return $user->isAdminOrStaff();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SRIG $sRIG): bool
    {
        return $user->isAdminOrStaff();
    }
}