<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Mail\Mailable;

class OrderStatusChangedMail extends Mailable
{
    public function __construct(
        public Order $order
    ) {}

    public function build()
    {
        return $this
            ->subject('Your Order Status Has Been Updated')
            ->view('emails.order-status-changed');
    }
}
