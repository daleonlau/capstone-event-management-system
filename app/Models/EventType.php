<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'requires_payment',
        'requires_document',
    ];

    protected $casts = [
        'requires_payment' => 'boolean',
        'requires_document' => 'boolean',
    ];

    public function events()
    {
        return $this->hasMany(Event::class);
    }
}