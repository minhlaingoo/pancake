<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Represents a physical IoT device (e.g., ADC controller) connected via MQTT.
 *
 * @property int $id
 * @property string $name
 * @property string $model The device model identifier used in MQTT topic routing
 * @property string $ip
 * @property int $port
 * @property string|null $mac
 * @property bool $is_active
 * @property string|null $ntp_server
 * @property string|null $timezone
 * @property int|null $ntp_interval NTP sync interval in seconds
 */
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
        'configuration',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'configuration' => 'array',
    ];

    /**
     * Get all hardware components belonging to this device.
     */
    public function deviceComponents(): HasMany
    {
        return $this->hasMany(DeviceComponent::class, 'device_id');
    }

    /**
     * Get a configuration value with default fallback.
     */
    public function getConfig(string $key, $default = null)
    {
        return data_get($this->configuration, $key, $default);
    }

    /**
     * Set a configuration value.
     */
    public function setConfig(string $key, $value): void
    {
        $config = $this->configuration ?? [];
        data_set($config, $key, $value);
        $this->configuration = $config;
        $this->save();
    }

    /**
     * Get available microvalves based on configuration.
     */
    public function getAvailableMicrovalves(): array
    {
        $count = $this->getConfig('microvalves.count', 6); // Default: 0-5
        $start = $this->getConfig('microvalves.start', 0);
        
        return range($start, $start + $count - 1);
    }

    /**
     * Get default device configuration schema.
     */
    public static function getDefaultConfiguration(): array
    {
        return [
            'microvalves' => [
                'count' => 6,      // Number of microvalves
                'start' => 0,      // Starting index (usually 0)
                'description' => 'Microvalves 0-5 are present'
            ],
            'pumps' => [
                'count' => 2,      // pump_0, pump_1
                'start' => 0,
                'description' => 'Two syringe pumps'
            ],
            'rotary_valves' => [
                'count' => 2,      // rotary_valve_1, rotary_valve_2  
                'start' => 1,      // Usually start from 1
                'description' => 'Two rotary valves'
            ],
            'temperature_control' => [
                'enabled' => true,
                'default_setpoint' => 25.0,
                'min_temp' => 4.0,
                'max_temp' => 95.0,
            ],
            'stirrer' => [
                'enabled' => true,
                'max_speed' => 1000,
                'default_speed' => 300,
            ]
        ];
    }
}
