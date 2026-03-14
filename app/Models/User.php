<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function events()
    {
        return $this->hasMany(Event::class, 'user_id');
    }

    public function organizationSettings()
    {
        return $this->hasOne(OrganizationSetting::class, 'organization_id');
    }

    public function organizationUsers()
    {
        return $this->hasMany(OrganizationUser::class, 'organization_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'user_id');
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }
}