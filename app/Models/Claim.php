<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Claim extends Model
{
    /** @use HasFactory<\Database\Factories\ClaimFactory> */
    use HasFactory;

    protected $fillable = [
        'donation_id',
        'charity_id',
        'status',
        'notes',
        'pickup_date',
    ];

    protected $casts = [
        'pickup_date' => 'datetime',
    ];

    // Relationship with Donation
    public function donation()
    {
        return $this->belongsTo(Donation::class);
    }

    // Relationship with User (charity)
    public function charity()
    {
        return $this->belongsTo(User::class, 'charity_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
}
