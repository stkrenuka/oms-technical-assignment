<?php

namespace App\Services\User;

use App\Models\User;

class UserService
{
    public function ensureAdmin($user): void
    {
        if ($user->role !== 'admin') {
            abort(403, 'Unauthorized');
        }
    }

    public function getCustomers(int $excludeUserId)
    {
        return User::where('role', 'customer')
            ->where('id', '!=', $excludeUserId)
            ->select('id', 'name', 'email', 'role')
            ->paginate(10);
    }

    public function searchCustomers(string $search, int $limit = 10)
    {
        return User::where('role', 'customer')
            ->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })
            ->select('id', 'name', 'email')
            ->limit($limit)
            ->get();
    }

    public function delete(User $user): void
    {
        $user->delete();
    }
}
