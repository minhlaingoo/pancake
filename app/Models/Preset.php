<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A reusable set of MQTT commands that can be applied to protocol phases or executed directly on devices.
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property array $commands List of command objects with controller, action, value, type, delay, retry_count, timeout
 * @property string $version
 * @property string $author
 * @property string $status Draft|Active|Archived
 */
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
