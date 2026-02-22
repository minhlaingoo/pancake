<?php

namespace App\Services;

use App\Events\MqttMessageReceived;
use App\Models\Device;
use App\Models\MqttMessage;
use App\Models\Setting;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use Exception;
use Illuminate\Support\Facades\Log;

class MqttService
{
    protected $broker_setting;
    protected $mqttClient;
    protected ConnectionSettings $connectionSettings;
    protected $clean_session;
    protected $isConnected = false;
    protected $maxReconnectAttempts = 5;
    protected $reconnectDelay = 1000000; // 1 second in microseconds

    public function __construct()
    {
        // Beehive MQTT public broker
        $this->broker_setting = Setting::where('category', 'broker')->first();

        if ($this->broker_setting) {
            $broker_value = json_decode($this->broker_setting->value);
            $rawUrl = $broker_value->url ?? 'localhost';
            $port = $broker_value->port ?? 1883;
            $this->clean_session = $broker_value->clean_session ?? true;
            $mqtt_version = $broker_value->protocol_version ?? MqttClient::MQTT_3_1;
        } else {
            Log::warning('MQTT broker settings not found in database. Using defaults.');
            $rawUrl = 'localhost';
            $port = 1883;
            $this->clean_session = true;
            $mqtt_version = MqttClient::MQTT_3_1;
        }

        // Strip protocol prefix (mqtt://, tcp://, etc.) — MqttClient expects a bare hostname
        $server = preg_replace('#^(mqtts?|tcp|ssl|wss?)://#i', '', $rawUrl);
        $clientId = "Pancake_client_id" . rand(5, 100);

        // Create MQTT Client instance
        $this->mqttClient = new MqttClient($server, $port, $clientId, $mqtt_version);

        // Create connection settings
        $this->connectionSettings = $this->getConnectionSettings();
    }

    private function getConnectionSettings($broker_value = null)
    {
        if ($broker_value === null) {
            if (!$this->broker_setting) {
                return new ConnectionSettings();
            }
            $broker_value = json_decode($this->broker_setting->value);
        } elseif (is_array($broker_value)) {
            $broker_value = (object) $broker_value;
        }

        // Authentication Setting
        $username = ($broker_value->auth_type ?? 'none') == 'none' ? null : ($broker_value->username ?? null);
        $password = ($broker_value->auth_type ?? 'none') == 'none' ? null : ($broker_value->password ?? null);

        $connectionSettings = new ConnectionSettings();

        // Each setter returns a new instance, so we need to capture it
        $connectionSettings = $connectionSettings
            ->setUsername($username)
            ->setPassword($password)
            ->setKeepAliveInterval($broker_value->keep_alive_interval ?? 120)
            ->setLastWillTopic('emqx/test/last-will')
            ->setLastWillMessage('client disconnect')
            ->setLastWillQualityOfService(1);

        // Configure TLS if enabled
        if (($broker_value->auth_type ?? 'none') == 'tls') {
            $connectionSettings = $connectionSettings
                ->setUseTls(true)
                ->setTlsVerifyPeer(false)
                ->setTlsVerifyPeerName(false)
                ->setTlsSelfSignedAllowed(false);

            // Set certificates with default names - Aligned with BrokerSetting.php
            $connectionSettings = $connectionSettings
                ->setTlsCertificateAuthorityFile(storage_path('app/private/certs/ca.crt'));
            $connectionSettings = $connectionSettings
                ->setTlsClientCertificateFile(storage_path('app/private/certs/client.crt'));
            $connectionSettings = $connectionSettings
                ->setTlsClientCertificateKeyFile(storage_path('app/private/certs/client.key'));
        }

        return $connectionSettings;
    }

    /**
     * Test connection with provided settings without updating the class state.
     * 
     * @param array $settings
     * @return bool
     */
    public function testConnection(array $settings): bool
    {
        $rawUrl = $settings['url'] ?? 'localhost';
        $port = (int) ($settings['port'] ?? 1883);
        $mqtt_version = ($settings['protocol_version'] ?? MqttClient::MQTT_3_1);
        $clean_session = (bool) ($settings['clean_session'] ?? true);

        // Strip protocol prefix
        $server = preg_replace('#^(mqtts?|tcp|ssl|wss?)://#i', '', $rawUrl);
        $clientId = "Test_client_" . rand(1000, 9999);

        try {
            $client = new MqttClient($server, $port, $clientId, $mqtt_version);
            $connectionSettings = $this->getConnectionSettings($settings);

            // Connect with a short timeout if possible (not directly supported by PhpMqtt/Client easily without custom socket)
            // But we'll try standard connect
            $client->connect($connectionSettings, $clean_session);
            $client->disconnect();

            return true;
        } catch (Exception $e) {
            Log::warning("MQTT Connection test failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Connect to the MQTT broker with retry logic.
     * 
     * @return bool
     * @throws Exception
     */
    public function connectToMQTT(): bool
    {

        if ($this->mqttClient->isConnected()) {
            $this->isConnected = true;
            return true;
        }
        $attempt = 0;
        $delay = $this->reconnectDelay;

        while ($attempt < $this->maxReconnectAttempts) {
            Log::info("Attempting to connect to MQTT broker... (Attempt {$attempt})");
            try {
                $this->mqttClient->connect($this->connectionSettings, $this->clean_session);
                $this->isConnected = true;
                Log::info('Connected to MQTT broker successfully');
                return true;
            } catch (Exception $e) {
                $attempt++;
                Log::warning("Connection attempt {$attempt} failed: " . $e->getMessage());

                if ($attempt < $this->maxReconnectAttempts) {
                    usleep($delay);
                    $delay *= 2;
                } else {
                    Log::error('Max reconnection attempts reached');
                    $this->isConnected = false;
                    throw $e;
                }
            }
        }
        return false;
    }

    /**
     * Internal helper to standardize topic structure and publish commands.
     */
    public function deviceCommand(string $model, string $device, string $action, string $payload): void
    {
        $topic = "adc/controller/{$model}/command/{$device}/{$action}";
        $this->publishMessage($topic, $payload);
    }

    /**
     * Publish a raw message to a topic.
     */
    public function publishMessage(string $topic, string $message): void
    {
        try {
            if (!$this->mqttClient->isConnected()) {
                $this->isConnected = false;
                $this->connectToMQTT();
            }

            $this->mqttClient->publish($topic, $message, MqttClient::QOS_AT_LEAST_ONCE);
            Log::info("Message Published to {$topic}: {$message}");
        } catch (Exception $e) {
            Log::error("Error publishing message to {$topic}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Temperature Control (TEC)
     */
    public function tecSetSetpoint(string $model, float $temp): void
    {
        // Range validation: 0-50°C
        $temp = max(0.0, min(50.0, $temp));
        $this->deviceCommand($model, 'tec', 'setpoint', (string) $temp);
    }

    public function tecEnable(string $model, bool $state): void
    {
        $this->deviceCommand($model, 'tec', 'enable', $state ? '1' : '0');
    }

    /**
     * Stirrer Control
     */
    public function stirrerSetSpeed(string $model, int $rpm): void
    {
        // Range validation: 0-1000 RPM
        $rpm = max(0, min(1000, $rpm));
        $this->deviceCommand($model, 'stirrer', 'speed', (string) $rpm);
    }

    public function stirrerStop(string $model): void
    {
        $this->deviceCommand($model, 'stirrer', 'stop', '');
    }

    /**
     * Microvalve Control
     */
    public function microvalveSet(string $model, int $valve, bool $state): void
    {
        $payload = "{$valve}," . ($state ? '1' : '0');
        $this->deviceCommand($model, 'microvalve', 'set', $payload);
    }

    public function microvalveOpen(string $model, int $valve): void
    {
        $this->deviceCommand($model, 'microvalve', 'open', (string) $valve);
    }

    public function microvalveClose(string $model, int $valve): void
    {
        $this->deviceCommand($model, 'microvalve', 'close', (string) $valve);
    }

    /**
     * Pump Control
     */
    public function pumpInit(string $model, int $pumpIndex = 0): void
    {
        $this->deviceCommand($model, "pump_{$pumpIndex}", 'init', '');
    }

    public function pumpAspirate(string $model, int $volume, int $pumpIndex = 0): void
    {
        $this->deviceCommand($model, "pump_{$pumpIndex}", 'aspirate', (string) $volume);
    }

    public function pumpDispense(string $model, int $volume, int $pumpIndex = 0): void
    {
        $this->deviceCommand($model, "pump_{$pumpIndex}", 'dispense', (string) $volume);
    }

    public function pumpHome(string $model, int $pumpIndex = 0): void
    {
        $this->deviceCommand($model, "pump_{$pumpIndex}", 'home', '');
    }

    /**
     * Subscribe to all status updates for a specific device (or all devices if '+' is used).
     * Follows the "Wildcard Subscription" strategy from mqtt-expert.md.
     */
    public function subscribeToDeviceStatus(string $model = '+'): void
    {
        $topic = "adc/controller/{$model}/status/#";
        $this->subscribeToTopic([$topic, MqttClient::QOS_AT_LEAST_ONCE]);
    }

    public function subscribeToTopic(...$topics)
    {
        try {
            if (!$this->isConnected) {
                $this->connectToMQTT();
            }


            foreach ($topics as $topic) {
                $topic_name = $topic[0];
                $qos = $topic[1];

                $this->mqttClient->subscribe($topic_name, function ($topic, $message, $retained) {
                    MqttMessageReceived::dispatch($topic, $message);
                    Log::info("Message received on {$topic}: {$message}");
                }, $qos);
            }

            while (true) {
                try {
                    if (!$this->mqttClient->isConnected()) {
                        Log::warning('Connection lost, attempting to reconnect...');
                        $this->isConnected = false;
                        $this->connectToMQTT();

                        // Resubscribe to topics after reconnection
                        foreach ($topics as $topic) {
                            $this->mqttClient->subscribe($topic[0], function ($topic, $message, $retained) {
                                MqttMessageReceived::dispatch($topic, $message);
                            }, $topic[1]);
                        }
                    }

                    $this->mqttClient->loop(true);
                    usleep(100000); // Sleep for 100ms
                } catch (Exception $e) {
                    $this->isConnected = false;
                }
            }
        } catch (Exception $e) {
            Log::error('Fatal error in subscription: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Publish a command to a specific device using the Device model.
     */
    public function publishToDevice(Device $device, string $component, string $action, string $payload): void
    {
        $model = $device->model;
        if (empty($model)) {
            Log::error("Cannot publish command: Device {$device->id} has no model name.");
            return;
        }
        $this->deviceCommand($model, $component, $action, $payload);
    }

    public function ntpUpdate(Device $device)
    {
        $payload = json_encode([
            'ntp_server' => $device->ntp_server ?? config('app.url'),
            'timezone' => $device->timezone ?? 'Asia/Ho_Chi_Minh',
            'ntp_interval' => (int) ($device->ntp_interval ?? 3600),
            'time' => now()->utc()->toISOString()
        ]);

        $this->publishMessage("ntp/update", $payload);
        Log::info("NTP Update sent for device {$device->model}");
    }

    /**
     * Send all commands defined in a protocol phase.
     */
    public function sendPhaseCommands(string $deviceId, array $phase): void
    {
        $commands = $phase['commands'] ?? [];
        if (empty($commands)) {
            Log::info("No commands to send for phase: " . ($phase['label'] ?? 'Unknown'));
            return;
        }

        Log::info("Sending commands for phase: " . ($phase['label'] ?? 'Unknown') . " (Device: {$deviceId})");

        foreach ($commands as $command) {
            $controller = $command['controller'] ?? null;
            $action = $command['action'] ?? null;
            $value = $command['value'] ?? '';
            $type = $command['type'] ?? 'string';

            if (!$controller || !$action) {
                Log::warning("Skipping incomplete command in phase: " . json_encode($command));
                continue;
            }

            // Format payload based on type
            $payload = (string) $value;
            if ($type === 'bool' || is_bool($value)) {
                $payload = $value ? '1' : '0';
            }

            // Route to: adc/controller/{deviceId}/command/{controller}/{action}
            $topic = "adc/controller/{$deviceId}/command/{$controller}/{$action}";

            try {
                $this->publishMessage($topic, $payload);
            } catch (Exception $e) {
                Log::error("Failed to send phase command to {$topic}: " . $e->getMessage());
            }
        }
    }

    private function reconnect()
    {
        try {
            if ($this->mqttClient->isConnected()) {
                $this->mqttClient->disconnect();
            }
            $this->mqttClient->connect($this->connectionSettings, false);
            $this->isConnected = true;
        } catch (Exception $e) {
            Log::error("Reconnection failed: " . $e->getMessage());
        }
    }
}
