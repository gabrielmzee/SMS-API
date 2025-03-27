<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classs extends Model
{
    protected $fillable = [
        'name',
        'created_by',
        'updated_by',
        'is_active'
    ];

    public function year()
    {
        return $this->belongsTo(Year::class, 'year_id');
    }
}
