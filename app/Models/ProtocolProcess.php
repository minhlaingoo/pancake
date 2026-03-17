<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

/**
 * A single execution instance of a Protocol, tracking start/end and linked MQTT telemetry.
 *
 * @property int $id
 * @property int $protocol_id
 * @property string $uid Unique process identifier used in MQTT payloads
 * @property \Carbon\Carbon|null $ended_at
 */
class ProtocolProcess extends Model
{
    protected $fillable = [
        'protocol_id',
        'uid',
        'ended_at'
    ];

    protected $casts = [
        'ended_at' => 'datetime',
    ];

    /**
     * Get the parent protocol definition.
     */
    public function protocol(): BelongsTo
    {
        return $this->belongsTo(Protocol::class);
    }

    /**
     * Retrieve all MQTT messages associated with this process execution.
     *
     * @return Collection<int, MqttMessage>
     */
    public function getData(): Collection
    {
        return MqttMessage::query()
            ->whereJsonContains('message->protocol_process_id', $this->uid)
            ->get();
    }
}
