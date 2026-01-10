<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\Order\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {}

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
        $statuses = $this->orderService->getStatuses();
        return response()->json($statuses);
    }
}
