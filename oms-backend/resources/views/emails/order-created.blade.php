<p>Hello {{ $order->customer->name }},</p>

<p>Your order <strong>#{{ $order->id }}</strong> has been successfully placed.</p>

<p>
    Status: <strong>{{ $order->status->name }}</strong><br>
    Total: ₹{{ number_format($order->total, 2) }}
</p>

<p>Thank you for shopping with us.</p>
