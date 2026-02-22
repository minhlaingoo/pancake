<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\MqttService;
use Exception;

class MqttComponent extends Component
{
    public $topic;
    public $message;
    public $messages = [];

    protected $mqttService;

    public function mount()
    {
        // Initialize the MQTT service and subscribe to the topic
        $this->topic = "test_chan";
        $this->messages[] = "Component mounted and subscribing to topic: $this->topic";
        $this->mqttService = new MqttService();
        printf("hello");

        // Automatically subscribe to the topic
        // $this->subscribeToTopic();
    }

    public function publishMessage()
    {
        try {
            // Publish message to the topic
            $this->mqttService->publishMessage($this->topic, $this->message);
            session()->flash('message', 'Message Published');
            $this->messages[] = "Message published to topic: $this->topic";
        } catch (Exception $e) {
            $this->messages[] = $e->getMessage();
        }
    }

    public function subscribeToTopic()
    {
        try {
            // Subscribe to the topic when the component is mounted
            $this->mqttService->subscribeToTopic($this->topic);

            // This method will now handle the message callback
            $this->mqttService->setMessageCallback(function ($topic, $message) {
                $this->messages[] = "Received message: $message on topic: $topic";
                $this->emit('messageReceived', $message);  // Optional: Emit event for other components
            });
        } catch (Exception $e) {
            $this->messages[] = $e->getMessage();
        }
    }

    public function addMessage($message)
    {
        try {
            // Add custom message to the array
            $this->messages[] = $message;
            session()->flash('message', 'Message Added');
        } catch (Exception $e) {
            $this->messages[] = $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.mqtt-component');
    }
}
