<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceComponentLog extends Model
{
    protected $fillable = [
        'device_component_id',
        'device_id',
        'value'
    ];

    public function deviceComponent()
    {
        return $this->belongsTo(DeviceComponent::class);
    }
}
