<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Order;

class DashboardController extends Controller
{
    public function stats()
    {
        $user = Auth::user();

        $query = Order::query();

        // 🔐 Customer → only own orders
        if ($user->role === 'customer') {
            $query->where('customer_id', $user->id);
        }

        return response()->json([
            'total_orders' => $query->count(),
            'total_revenue' => $query->sum('total'),
        ]);
    }
}
