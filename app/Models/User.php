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

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isOperator(): bool
    {
        return $this->role === 'operator';
    }

    /**
     * Automatically hash password when setting it (if not already hashed)
     * This works alongside Laravel's 'hashed' cast
     */
    public function setPasswordAttribute($value)
    {
        if (empty($value)) {
            return;
        }

        // Check if password is already hashed
        // Laravel's bcrypt hashes start with $2y$ and are 60 characters long
        $isHashed = strlen($value) >= 60 && str_starts_with($value, '$2y$');

        if (!$isHashed) {
            // Password is plain text, hash it
            $this->attributes['password'] = bcrypt($value);
        } else {
            // Password is already hashed, use as is
            $this->attributes['password'] = $value;
        }
    }
}
