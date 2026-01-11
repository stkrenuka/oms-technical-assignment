<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Upload extends Model
{
    protected $fillable = [
        'upload_id',
        'order_id',
        'file_name',
        'file_path',
        'size',
        'total_chunks',
        'is_completed',
        'created_by',
        'uploaded_chunks'
    ];
}
