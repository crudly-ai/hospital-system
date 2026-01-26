<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'specialty',
        'department_id',
        'phone',
        'email',
        'status',

        'created_by',
    ];

    protected $casts = [
        'department_id' => 'integer',
        'status' => 'string',

    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }


    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}