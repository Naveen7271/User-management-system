<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
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
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Check if user has a specific role
     */
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    /**
     * Check if user is SuperAdmin
     */
    public function isSuperAdmin()
    {
        return $this->hasRole('SuperAdmin');
    }

    /**
     * Check if user is Admin
     */
    public function isAdmin()
    {
        return $this->hasRole('Admin');
    }

    /**
     * Check if user is regular User
     */
    public function isUser()
    {
        return $this->hasRole('User');
    }

    /**
     * Check if user can manage other users
     */
    public function canManageUsers()
    {
        return $this->isSuperAdmin() || $this->isAdmin();
    }

    /**
     * Check if user can delete other users
     */
    public function canDeleteUsers()
    {
        return $this->isSuperAdmin();
    }
}
