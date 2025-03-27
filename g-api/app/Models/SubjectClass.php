<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubjectClass extends Model
{
    protected $fillable = [
        'subject_id',
        'class_year_id',
        'created_by',
        'updated_by',
        'is_active'
    ];
}
