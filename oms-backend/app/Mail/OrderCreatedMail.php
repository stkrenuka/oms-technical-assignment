<?php

namespace App\Mail;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderCreatedMail extends Mailable
{
    public function __construct(
        public Order $order
    ) {}

    public function build()
    {
        return $this
            ->subject('Your Order Has Been Placed')
            ->view('emails.order-created');
    }
}
