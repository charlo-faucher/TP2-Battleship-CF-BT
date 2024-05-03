<?php

namespace App\Policies;

use App\Models\Partie;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PartiePolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Partie $partie): bool
    {
        return $user->id === $partie->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function store(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Partie $partie): bool
    {
        return $user->id === $partie->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Partie $partie): bool
    {
        return true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Partie $partie): bool
    {
        return false;
    }
}
