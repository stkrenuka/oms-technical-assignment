<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\User;
class DashboardController extends Controller
{
    public function stats()
    {
        $user = Auth::user();
        // Base order query
        $orderQuery = Order::query();
        if ($user->role === 'customer') {
            $orderQuery->where('customer_id', $user->id);
        }
        $totalCustomers = null;
        if ($user->role === 'admin') {
            $totalCustomers = User::where('role', 'customer')->count();
        }
        return response()->json([
            'total_orders' => $orderQuery->count(),
            'total_revenue' => $orderQuery->sum('total'),
            'total_customers' => $totalCustomers, // null for customer
        ]);
    }
}
