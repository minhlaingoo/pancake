<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Preset extends Model
{
    protected $fillable = [
        'name',
        'description',
        'commands',
        'version',
        'author',
        'status',
    ];

    protected $casts = [
        'commands' => 'array',
    ];
}
