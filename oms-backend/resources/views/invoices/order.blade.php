<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: DejaVu Sans; font-size: 14px; }
        .header { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background: #f3f3f3; }
    </style>
</head>
<body>

<div class="header">
    <h2>Invoice</h2>
    <p>Order #{{ $order->id }}</p>
</div>

<p><strong>Customer:</strong> {{ $order->customer->name }}</p>
<p><strong>Status:</strong> {{ $order->status->name }}</p>

<table>
    <thead>
        <tr>
            <th>Item</th>
            <th>Qty</th>
            <th>Price</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
      @foreach ($order->items as $item)
<tr>
    <td>{{ $item->product->name ?? 'Product' }}</td>
    <td>{{ $item->quantity }}</td>
    <td>₹{{ number_format($item->price, 2) }}</td>
    <td>₹{{ number_format($item->total, 2) }}</td>
</tr>
@endforeach

    </tbody>
</table>

<p><strong>Grand Total:</strong> ${{ $order->total }}</p>

</body>
</html>
