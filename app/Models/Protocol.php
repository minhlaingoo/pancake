<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Protocol extends Model
{
    protected $fillable = [
        'sample_id',
        'description',
        'value',
        'phases'
    ];

    protected $casts = [
        'phases' => 'array'
    ];
}
