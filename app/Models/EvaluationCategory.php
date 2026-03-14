<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EvaluationCategory extends Model
{
    protected $fillable = [
        'evaluation_id',
        'category_name',
        'order',
    ];

    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(EvaluationQuestion::class, 'category_id')->orderBy('order');
    }
}