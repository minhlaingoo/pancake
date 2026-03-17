<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A chemical analysis protocol consisting of ordered phases with MQTT commands.
 *
 * @property int $id
 * @property string $sample_id Unique sample identifier
 * @property string|null $description
 * @property string $value JSON-encoded protocol setup data (mAb, payload, misc)
 * @property array $phases Ordered list of execution phases with commands
 */
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
