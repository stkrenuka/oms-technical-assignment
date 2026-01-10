<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Models\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Jobs\SendOrderStatusChangedMail;
use App\Jobs\SendOrderCreatedMail;

class OrderService
{
    public function getAllOrders(Request $request)
    {
        $user = Auth::user();

        $query = Order::query()
            ->with([
                'customer:id,name,email',
                'items.product:id,name',
                'status:id,name',
            ])
            ->latest();

        // 🔐 Customers see only their orders
        if ($user->role === 'customer') {
            $query->where('customer_id', $user->id);
        }

        // ✅ Optional status filter (admin + customer)
        if ($request->filled('status')) {
            $query->whereHas('status', function ($q) use ($request) {
                $q->where('name', $request->status);
            });
        }

        return $query->paginate($request->per_page ?? 10);
    }

    public function create(array $data, $user): Order
    {
        $customerId = $user->role === 'admin'
            ? $data['customer_id']
            : $user->id;

        // $draftStatus = OrderStatus::where('slug', 'draft')->firstOrFail();

        $order = Order::create([
            'customer_id' => $customerId,
            'status_id' => $data['status'],
            'total' => $data['total'],
        ]);

        foreach ($data['items'] as $item) {
            $order->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['qty'],
                'price' => $item['price'],
                'total' => $item['qty'] * $item['price'],
            ]);
        }
        SendOrderCreatedMail::dispatch(
            $order->load('customer', 'status')
        );
        return $order;
    }
    public function getStatuses()
    {
        return OrderStatus::all(['id', 'name', 'slug']);
    }
    public function updateStatus(Order $order, int $newStatusId): Order
    {
        $oldStatusId = $order->status_id;

        $order->update([
            'status_id' => $newStatusId
        ]);

        // ✅ Send mail only if status actually changed
        if ($oldStatusId !== $newStatusId) {
            SendOrderStatusChangedMail::dispatch(
                $order->load('customer', 'status')
            );
        }

        return $order;
    }
}
