<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffTZ extends Model
{
    protected $table = 'staff_tz';
    protected $fillable = [
        'nin',
        'first_name',
        'middle_name',
        'last_name',
        'sex',
        'date_of_birth',
        'birth_country',
        'birth_region',
        'birth_district',
        'birth_ward',
        'nationality',
        'photo',
        'signature',
        'is_active',
        'created_by'
    ];
}
