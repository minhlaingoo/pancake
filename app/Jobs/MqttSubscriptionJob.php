<?php

namespace App\Jobs;

use App\Services\MqttService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class MqttSubscriptionJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    protected $topic;

    public function __construct($topic)
    {
        $this->topic = $topic;
    }

    public function handle(MqttService $mqttService)
    {
        $mqttService->subscribeToTopic($this->topic);
    }
}
