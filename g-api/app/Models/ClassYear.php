<?php

namespace App\Models;

use App\Models\Year;
use App\Models\Classs;
use Illuminate\Database\Eloquent\Model;

class ClassYear extends Model
{
    protected $fillable = [
        'class_id',
        'year_id',
        'created_by',
        'updated_by',
        'is_active'
    ];

    /**
     * Get the class associated with the ClassYear.
     */
    public function classs() // Avoid "class" as it's a reserved keyword
    {
        return $this->belongsTo(Classs::class, 'class_id');
    }

    /**
     * Get the year associated with the ClassYear.
     */
    public function year()
    {
        return $this->belongsTo(Year::class, 'year_id');
    }
}




// 📌 Staff who are TANZANIANS API Endpoints:

// 🔹 Get all staff:
//    GET /staff

// 🔹 Get a specific staff:
//    GET /staff/tz/{id}

// 🔹 Create a new staff:
//    POST /staff/tz
//    Body (JSON):
//    {
//     'nin': 'TEST',
//     'first_name': 'TEST',
//     'middle_name': 'TEST',
//     'last_name': 'TEST',
//     'sex': 'TEST',
//     'date_of_birth': 'TEST',
//     'birth_country': 'TEST',
//     'birth_region': 'TEST',
//     'birth_district': 'TEST',
//     'birth_ward': 'TEST',
//     'nationality': 'TEST',
//     'photo': 'TEST',
//     'signature': 'TEST',
//    }

// 🔹 Delete a staff:
//    DELETE /staff/tz/{id}

// 📢 Authentication:Bearer
