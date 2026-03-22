<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrganizationUser extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'organization_users';

    protected $fillable = [
        'organization_id',
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

    public function organization()
    {
        return $this->belongsTo(User::class, 'organization_id');
    }

    /**
     * Get the organization name directly
     */
    public function getOrganizationNameAttribute()
    {
        return $this->organization ? $this->organization->name : 'N/A';
    }

    public function eventApprovals()
    {
        return $this->hasMany(EventApproval::class, 'approved_by');
    }

    public function isAdviser()
    {
        return $this->role === 'adviser';
    }

    public function isPresident()
    {
        return $this->role === 'president';
    }

    public function isTreasurer()
    {
        return $this->role === 'treasurer';
    }
}