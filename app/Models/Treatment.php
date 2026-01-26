<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Treatment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'cost',
        'status',

        'created_by',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'status' => 'string',

    ];


    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}