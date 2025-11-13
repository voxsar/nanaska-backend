<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessModel extends Model
{
    use HasFactory;

    protected $table = 'business_models';

    protected $fillable = [
        'name',
        'description',
        'analysis_prompt',
    ];
}
