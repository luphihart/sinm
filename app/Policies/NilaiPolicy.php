<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Nilai;

class NilaiPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Nilai $nilai): bool
    {
        return $user->role === 'admin' || $user->id === $nilai->murid->user_id;
    }

    /**
     * Determine whether the user can manage (create, update, delete) grades.
     */
    public function manage(User $user): bool
    {
        return $user->role === 'admin';
    }
}
