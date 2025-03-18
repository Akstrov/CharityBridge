<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CharityDetail extends Model
{
    /** @use HasFactory<\Database\Factories\CharityDetailFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'organization_name',
        'organization_logo',
        'mission_statement',
        'description',
        'website',
        'registration_number',
        'verified',
        'category',
        'tax_id',
        'areas_of_focus',
        'year_established',
        'social_media_links',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'verified' => 'boolean',
        'year_established' => 'integer',
    ];

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
