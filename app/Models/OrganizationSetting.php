<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganizationSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'assigned_departments',
        'assigned_courses',
    ];

    protected $casts = [
        'assigned_departments' => 'array',
        'assigned_courses' => 'array',
    ];

    public function organization()
    {
        return $this->belongsTo(User::class, 'organization_id');
    }

    public function getDepartmentsListAttribute()
    {
        return Department::whereIn('id', $this->assigned_departments ?? [])->get();
    }

    public function getCoursesListAttribute()
    {
        return Course::whereIn('id', $this->assigned_courses ?? [])->get();
    }
}