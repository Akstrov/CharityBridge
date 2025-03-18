<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'profile_image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function charityDetail()
    {
        return $this->hasOne(CharityDetail::class);
    }

    // Relationship with Donations (for donors)
    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    // Relationship with Claims (for charities)
    public function claims()
    {
        return $this->hasMany(Claim::class, 'charity_id');
    }

    // Helper methods for role checking
    public function isDonor()
    {
        return $this->role === 'donor';
    }

    public function isCharity()
    {
        return $this->role === 'charity';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }
}
