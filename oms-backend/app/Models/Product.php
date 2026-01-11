<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
class Product extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'status',
        'sku',
        'image',
    ];
    public function getImageUrlAttribute()
    {
        return $this->image
            ? Storage::url($this->image)
            : null;
    }
}
