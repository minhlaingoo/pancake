<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $fillable = [
        'name',
        'model',
        'ip',
        'port',
        'mac',
        'is_active',
        'ntp_server',
        'timezone',
        'ntp_interval',
    ];

    public function deviceComponents()
    {
        return $this->hasMany(DeviceComponent::class, 'device_id');
    }
}
