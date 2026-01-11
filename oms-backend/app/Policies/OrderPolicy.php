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
      public function cancel(User $user, Order $order): bool
    {
        // Admin override
        if ($user->role === 'admin') {
            return true;
        }

        // Customer can cancel only their own order
        return $user->role === 'customer'
            && $order->customer_id === $user->id;
    }

    public function downloadInvoice(User $user, Order $order): bool
{
    // Admin can download any invoice
    if ($user->role === 'admin') {
        return true;
    }

    // Customer can download ONLY their own order invoice
    return $user->role === 'customer'
        && $order->customer_id === $user->id;
}

}
