<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TelemetryUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $deviceComponentId;
    public $value;
    public $status;
    public $deviceId;

    /**
     * Create a new event instance.
     */
    public function __construct($deviceComponentId, $value, $status, $deviceId)
    {
        $this->deviceComponentId = $deviceComponentId;
        $this->value = $value;
        $this->status = $status;
        $this->deviceId = $deviceId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('telemetry'),
            new Channel("device.{$this->deviceId}"),
        ];
    }
}
