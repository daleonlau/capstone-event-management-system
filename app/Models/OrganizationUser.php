<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

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
        'blocked_at',
        'block_reason',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'blocked_at' => 'datetime',
    ];

    public function organization()
    {
        return $this->belongsTo(User::class, 'organization_id');
    }

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

    public function isBlocked()
    {
        return !is_null($this->blocked_at);
    }

    public function block($reason = null)
    {
        $this->update([
            'blocked_at' => Carbon::now(),
            'block_reason' => $reason,
        ]);
    }

    public function unblock()
    {
        $this->update([
            'blocked_at' => null,
            'block_reason' => null,
        ]);
    }
}