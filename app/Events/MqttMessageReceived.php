<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MqttMessageReceived implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public $topic;
    public $message;

    public function __construct($topic, $message)
    {
        $this->topic = $topic;
        $this->message = $message;
    }

    public function broadcastOn()
    {
        return new Channel('mqtt.1');
    }
}
