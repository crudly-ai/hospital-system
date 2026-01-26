<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Admission extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'admitted_on',
        'discharged_on',
        'room_number',
        'status',

        'created_by',
    ];

    protected $casts = [
        'patient_id' => 'integer',
        'admitted_on' => 'date',
        'discharged_on' => 'date',
        'status' => 'string',

    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }


    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}