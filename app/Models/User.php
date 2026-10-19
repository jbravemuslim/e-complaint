<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isSuperadmin(): bool
    {
        return $this->role === 'superadmin';
    }

    public function isStaff(): bool
    {
        return in_array($this->role, ['admin', 'superadmin'], true);
    }

    public function complains()
    {
        return $this->hasMany(Complain::class, 'user_id');
    }

    public function responses()
    {
        return $this->hasMany(Response::class, 'admin_id');
    }
}
