<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'status_id',
        'subtotal',
        'tax',
        'discount',
        'total',
        'payment_method',
        'payment_status',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Order belongs to a customer (user)
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    // Order has many items
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Current status
    public function status()
    {
        return $this->belongsTo(OrderStatus::class, 'status_id');
    }

    // Status history (optional but recommended)
    public function statusHistory()
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

}
