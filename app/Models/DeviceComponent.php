<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceComponent extends Model
{
    protected $fillable = [
        'device_id',
        'type',
        'unit',
        'name',
        'last_value',
        'status',
        'is_sensor'
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
