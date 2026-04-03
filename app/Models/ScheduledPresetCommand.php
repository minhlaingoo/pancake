<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduledPresetCommand extends Model
{
    protected $fillable = [
        'batch_id',
        'device_id',
        'preset_id',
        'command_index',
        'command_data',
        'execute_at',
        'status',
    ];

    protected $casts = [
        'command_data' => 'array',
        'execute_at' => 'datetime',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function preset()
    {
        return $this->belongsTo(Preset::class);
    }
}
