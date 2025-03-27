<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TermYear extends Model
{
    protected $fillable = [
        'term_id',
        'year_id',
        'from_date',
        'to_date',
        'created_by',
        'updated_by',
        'is_active'
    ];
}
