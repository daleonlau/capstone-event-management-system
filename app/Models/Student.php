<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $primaryKey = 'student_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'student_id',
        'firstname',
        'lastname',
        'email',
        'yearlevel',
        'course', // Using course name instead of course_id
        'department',
        'user_id',
    ];

    public function organization()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_student', 'student_id', 'event_id')
                    ->using(EventStudent::class)
                    ->withPivot(['status', 'amount_paid', 'user_id'])
                    ->withTimestamps();
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class, 'student_id', 'student_id');
    }
}