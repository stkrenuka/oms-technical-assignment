<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function view(User $user, Order $order): bool
    {
        return $user->role === 'admin'
            || $order->customer_id === $user->id;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'customer']);
    }

    public function update(User $user, Order $order): bool
    {
        // Admin can update any order
        if ($user->role === 'admin') {
            return true;
        }

        // Customer can update ONLY draft orders
        return $order->customer_id === $user->id
            && $order->status->slug === 'draft';
    }

    public function delete(User $user, Order $order): bool
    {
        return $user->role === 'admin';
    }
}
