<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MqttMessage extends Model
{
    protected $fillable = [
        'topic',
        'message'
    ];

    protected $casts = [
        'message' => 'array'
    ];
}
