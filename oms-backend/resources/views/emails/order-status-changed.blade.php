<p>Hello {{ $order->customer->name }},</p>

<p>Your order <strong>#{{ $order->id }}</strong> status has been updated.</p>

<p>
    New Status:
    <strong>{{ $order->status->name }}</strong>
</p>

<p>Thank you for shopping with us.</p>
