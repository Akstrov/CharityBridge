<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    /** @use HasFactory<\Database\Factories\DonationFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category',
        'quantity',
        'location',
        'status',
        'is_urgent',
        'monetary_value',
        'expiry_date',
    ];

    protected $casts = [
        'is_urgent' => 'boolean',
        'monetary_value' => 'decimal:2',
        'expiry_date' => 'date',
    ];

    // Relationship with User (donor)
    public function donor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relationship with Claims
    public function claims()
    {
        return $this->hasMany(Claim::class);
    }
}
