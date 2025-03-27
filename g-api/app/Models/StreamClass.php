<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StreamClass extends Model
{
    protected $fillable = [
        'stream_id',
        'class_year_id',
        'created_by',
        'updated_by',
        'is_active'
    ];
}
