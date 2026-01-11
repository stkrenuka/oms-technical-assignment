<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UploadChunk extends Model
{
       protected $fillable = [
        'upload_id',
        'chunk_index',
        'is_uploaded'

    ];
}
