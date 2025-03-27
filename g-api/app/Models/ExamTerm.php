<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamTerm extends Model
{
    protected $fillable = [
        'exam_id',
        'term_year_id',
        'from_date',
        'to_date',
        'created_by',
        'updated_by',
        'is_active'
    ];
}
