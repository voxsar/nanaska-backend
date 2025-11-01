<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarkingPrompt extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'prompt_text',
        'is_active',
        'version',
        'parent_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function parent()
    {
        return $this->belongsTo(MarkingPrompt::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(MarkingPrompt::class, 'parent_id');
    }
}
