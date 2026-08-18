<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'name',
        'email',
        'password',
        'active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * The legacy application stored passwords as bare MD5 hashes. Those are
     * accepted once and transparently re-hashed with bcrypt on login.
     */
    public function hasLegacyPassword(): bool
    {
        return strlen($this->getAuthPassword()) === 32
            && ctype_xdigit($this->getAuthPassword());
    }

    public function displayName(): string
    {
        return $this->name ?: $this->username;
    }
}
