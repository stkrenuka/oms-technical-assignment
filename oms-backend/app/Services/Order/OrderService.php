<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Models\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Jobs\SendOrderStatusChangedMail;
use App\Jobs\SendOrderCreatedMail;
use Illuminate\Support\Facades\DB;

class OrderService
{
public function getAllOrders(Request $request)
{
    $search = $request->search;
 $user = Auth::user();
    return Order::with(['customer', 'items.product', 'statusHistories','status:id,name',])->when($user->role === 'customer', function ($query) use ($user) {
                $query->where('customer_id', $user->id);
            })
        ->when($search, function ($query) use ($search) {

            $words = explode(' ', $search);

            $query->where(function ($q) use ($words) {
                foreach ($words as $word) {

                    // ✅ ORDER ID
                    $q->orWhere('id', 'like', "%{$word}%")

                      // ✅ CUSTOMER NAME
                      ->orWhereHas('customer', function ($c) use ($word) {
                          $c->where('name', 'like', "%{$word}%");
                      })

                      // ✅ PRODUCT NAME
                      ->orWhereHas('items.product', function ($p) use ($word) {
                          $p->where('name', 'like', "%{$word}%");
                      })

                      // ✅ STATUS HISTORY NOTE (THIS IS THE FIX)
                      ->orWhereHas('statusHistories', function ($s) use ($word) {
                          $s->where('note', 'like', "%{$word}%");
                      });
                }
            });
        })
        ->latest()
        ->paginate(10);
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
    // public function updateStatus(Order $order, int $newStatusId): Order
    // {
    //     $oldStatusId = $order->status_id;

    //     $order->update([
    //         'status_id' => $newStatusId
    //     ]);

    //     // ✅ Send mail only if status actually changed
    //     if ($oldStatusId !== $newStatusId) {
    //         SendOrderStatusChangedMail::dispatch(
    //             $order->load('customer', 'status')
    //         );
    //     }

    //     return $order;
    // }

 public function changeStatus(
        Order $order,
        int $statusId,
        ?string $note = null
    ): void {
        DB::transaction(function () use ($order, $statusId, $note) {
      $oldStatusId = $order->status_id;
            // Update current order status
            $order->update([
                'status_id' => $statusId,
            ]);

            // Store status history
            $order->statusHistories()->create([
                'status_id' => $statusId,
                'changed_by' => Auth::id(),
                'note' => $note,
            ]);

            // Send mail only if status actually changed
            if ($oldStatusId !== $statusId) {
                SendOrderStatusChangedMail::dispatch(
                    $order->load('customer', 'status')
                );
            }
        });
    }
    public function cancelOrder(Order $order): void
{
    // 🔐 Guard: prevent cancel after confirmed stage
    if ($order->status_id > 3) {
        abort(403, 'Order cannot be cancelled at this stage.');
    }

    DB::transaction(function () use ($order) {

        // Update order status → Cancelled (7)
        $order->update([
            'status_id' => 6,
        ]);

        // Add history record
        $order->statusHistories()->create([
            'status_id' => 6,
            'changed_by' => auth()->id(),
            'note' => 'Order cancelled by ' . auth()->user()->name,
        ]);
    });
}
public function softDeleteOrder(Order $order): void
{
    if ($order->status_id > 2) {
        abort(403, 'Order cannot be deleted at this stage.');
    }

    DB::transaction(function () use ($order) {

        // Add history before delete
        $order->statusHistories()->create([
            'status_id' => $order->status_id,
            'changed_by' => auth()->id(),
            'note' => 'Order soft-deleted by admin',
        ]);

        // Soft delete
        $order->delete();
    });
}



}
