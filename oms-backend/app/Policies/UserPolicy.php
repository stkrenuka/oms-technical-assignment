<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }
    public function delete(User $authUser, User $user): bool
{
    return $authUser->role === 'admin'
        && $authUser->id !== $user->id
        && $user->role === 'customer';
}
 public function update(User $authUser, User $user): bool
    {
        return $authUser->role === 'admin';
    }
}
