<?php

namespace App\Models;

use App\Models\ClassYear;
use Illuminate\Database\Eloquent\Model;

class Year extends Model
{
    protected $fillable = [
        'year',
        'from_date',
        'to_date',
        'created_by',
        'updated_by',
        'is_active'
    ];

    public function classYears()
    {
        return $this->hasMany(ClassYear::class, 'year_id');
    }
}
