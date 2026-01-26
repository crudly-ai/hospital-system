<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Billing extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'amount',
        'payment_date',
        'status',

        'created_by',
    ];

    protected $casts = [
        'patient_id' => 'integer',
        'amount' => 'decimal:2',
        'payment_date' => 'date',
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