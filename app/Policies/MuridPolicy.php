<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Murid;

class MuridPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Murid $murid): bool
    {
        return $user->role === 'admin' || $user->id === $murid->user_id;
    }

    /**
     * Determine whether the user can manage (create, update, delete) the model.
     */
    public function manage(User $user): bool
    {
        return $user->role === 'admin';
    }
}
