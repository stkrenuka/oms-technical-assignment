<?php
namespace App\Http\Controllers;
use App\Models\Order;
use App\Services\Order\OrderService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Cache;
use Log;
class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {
    }
    public function index(Request $request)
    {
        return response()->json(
            $this->orderService->getAllOrders($request)
        );
    }
    public function show(Order $order)
    {
        $this->authorize('view', $order);
        return response()->json($order->load('items'));
    }
    public function store(Request $request)
    {
        $this->authorize('create', Order::class);
        $data = $request->validate([
            'customer_id' => 'required_if:role,admin|exists:users,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'status' => 'required',
        ]);
        $order = $this->orderService->create($data, $request->user());
        return response()->json([
            'data' => $order->load('customer'),
        ]);
    }
    public function statuses()
    {
        $statuses = Cache::rememberForever('order:statuses', function () {
            Log::info('Order statuses loaded from DATABASE');
            return $this->orderService->getStatuses();
        });
        return response()->json($statuses);
    }
    public function changeStatus(
        Request $request,
        Order $order,
        OrderService $orderService
    ) {
        $validated = $request->validate([
            'status_id' => 'required|exists:order_statuses,id',
            'note' => 'nullable|string|max:1000',
        ]);
        $orderService->changeStatus(
            $order,
            $validated['status_id'],
            $validated['note'] ?? null // ✅ PASS NOTE
        );
        return response()->json([
            'message' => 'Order status updated successfully',
        ]);
    }
    public function statusHistory(Order $order)
    {
        $history = $order->statusHistories()
            ->with([
                'status:id,name',
                'author:id,name'
            ])
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json($history);
    }
    public function cancel(Order $order, OrderService $orderService)
    {
        $this->authorize('cancel', $order);
        $orderService->cancelOrder($order);
        return response()->json([
            'message' => 'Order cancelled successfully',
        ]);
    }
    public function destroy(Order $order, OrderService $orderService)
    {
        $this->authorize('delete', $order);
        $orderService->softDeleteOrder($order);
        return response()->json([
            'message' => 'Order deleted successfully',
        ]);
    }
    public function downloadInvoice(Order $order)
    {
        $this->authorize('downloadInvoice', $order);
        $order->load('items.product', 'customer', 'status');
        Log::info('Invoice items', $order->items->toArray());
        $pdf = Pdf::loadView('invoices.order', [
            'order' => $order,
        ]);
        return $pdf->download("invoice-{$order->id}.pdf");
    }
}
