<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class OrderStatusHistory extends Model
{
    protected $fillable = [
        'order_id',
        'status_id',
        'changed_by',
        'note'
    ];
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function status()
    {
        return $this->belongsTo(OrderStatus::class);
    }
    public function author()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
