<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'dob',
        'phone',
        'email',
        'address',
        'status',

        'created_by',
    ];

    protected $casts = [
        'dob' => 'date',
        'status' => 'string',

    ];


    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}