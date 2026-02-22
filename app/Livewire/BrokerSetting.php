<?php

namespace App\Livewire;

use App\Models\Setting;
use App\Services\MqttService;
use Exception;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;
use PhpMqtt\Client\MqttClient;

class BrokerSetting extends Component
{
    use WithFileUploads;

    public $broker_url;
    public $broker_port;
    public $broker_protocol_version;
    public $broker_client_id;
    public $broker_keep_alive_interval;
    public $broker_clean_session;

    public $broker_auth_type;
    public $broker_username;
    public $broker_password;

    public $broker_can_publish;

    public $publish_topic;
    public $publish_message;

    public $subscribe_topic;
    public $subscribe_qos;
    public $subscribe_retain;

    public $enable_log;

    public $tls_enabled;

    public $client_cert;
    public $client_key;
    public $ca_cert;

    protected $rules = [
        'client_cert' => 'nullable|file',
        'client_key' => 'nullable|file|mimes:key,pem',
        'ca_cert' => 'nullable|file'
    ];

    public function mount()
    {
        $broker_setting = Setting::where('category', 'broker')->first();
        $settings = json_decode($broker_setting->value, true); // Convert to associative array

        // Basic settings
        $this->broker_url = $settings['url'] ?? 'localhost';
        $this->broker_port = $settings['port'] ?? 1883;
        $this->broker_protocol_version = $settings['protocol_version'] ?? MqttClient::MQTT_3_1_1;
        $this->broker_client_id = $settings['client_id'] ?? '';
        $this->broker_keep_alive_interval = $settings['keep_alive_interval'] ?? 60;
        $this->broker_clean_session = $settings['clean_session'] ?? true;

        // Authentication settings
        $this->broker_auth_type = $settings['auth_type'] ?? 'none';
        $this->broker_username = $settings['username'] ?? '';
        $this->broker_password = $settings['password'] ?? '';

        // Publishing settings
        $this->broker_can_publish = $settings['can_publish'] ?? false;
        $this->publish_topic = $settings['publish_topic'] ?? '';
        $this->publish_message = $settings['publish_message'] ?? '';

        // Subscription settings
        $this->subscribe_topic = $settings['subscribe_topic'] ?? '';
        $this->subscribe_qos = $settings['subscribe_qos'] ?? 0;
        $this->subscribe_retain = $settings['subscribe_retain'] ?? false;

        // TLS settings
        $this->tls_enabled = $settings['tls_enabled'] ?? false;

        // Logging settings
        $this->enable_log = $settings['enable_log'] ?? false;
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'ca_cert') {
            $this->validateOnly($propertyName, [
                'ca_cert' => [
                    'required',
                    'file',
                    function ($attribute, $value, $fail) {
                        if (!$value->getContent()) {
                            $fail('The certificate file appears to be empty.');
                            return;
                        }

                        // Check if file content starts with certificate markers
                        $content = $value->get();
                        if (!str_contains($content, '-----BEGIN CERTIFICATE-----')) {
                            $fail('The file does not appear to be a valid certificate.');
                        }
                    },
                ]
            ]);
        }
    }

    public function save()
    {
        try {
            if ($this->broker_auth_type === 'tls') {
                $this->validate();

                // Create certificates directory if it doesn't exist
                if (!file_exists(storage_path('app/private/certs'))) {
                    mkdir(storage_path('app/private/certs'), 0755, true);
                }

                // Store certificate files with logging
                if ($this->client_cert) {
                    $path = $this->client_cert->storeAs('certs', 'client.crt', 'local');
                }

                if ($this->client_key) {
                    $path = $this->client_key->storeAs('certs', 'client.key', 'local');
                }

                if ($this->ca_cert) {
                    $path = $this->ca_cert->storeAs('certs', 'ca.crt', 'local');
                }
            }

            $broker_setting = Setting::where('category', 'broker')->first();
            $value = [
                'url' => $this->broker_url,
                'port' => $this->broker_port,
                'protocol_version' => $this->broker_protocol_version,
                'client_id' => $this->broker_client_id,
                'keep_alive_interval' => $this->broker_keep_alive_interval,
                'clean_session' => $this->broker_clean_session,
                'auth_type' => $this->broker_auth_type,
                'username' => $this->broker_username,
                'password' => $this->broker_password,
                'can_publish' => $this->broker_can_publish,
                'enable_log' => $this->enable_log,
                'subscribe_topic' => $this->subscribe_topic,
                'subscribe_qos' => $this->subscribe_qos,
                'subscribe_retain' => $this->subscribe_retain,
                'tls_enabled' => $this->broker_auth_type === 'tls',
                'tls_verify_peer' => true,
                'tls_verify_peer_name' => true,
                'tls_self_signed_allowed' => false,
                'cert_paths' => [
                    'ca_cert' => storage_path('app/private/certs/ca.crt'),
                    'client_cert' => storage_path('app/private/certs/client.crt'),
                    'client_key' => storage_path('app/private/certs/client.key'),
                ]
            ];

            // Verify connection before saving
            $mqttService = app(MqttService::class);
            if (!$mqttService->testConnection($value)) {
                session()->flash('error', 'Could not connect to the MQTT broker with the provided settings. Please verify details and try again.');
                return;
            }

            $broker_setting->value = json_encode($value);
            $broker_setting->save();

            // Verify files were stored
            $this->verifyStoredCertificates();

            session()->flash('message', 'Settings and certificates updated successfully');
            return to_route('broker-setting');
        } catch (\Exception $e) {
            Log::error('Failed to save broker settings', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'Failed to save settings: ' . $e->getMessage());
        }
    }

    private function verifyStoredCertificates()
    {
        if ($this->broker_auth_type === 'tls') {
            $certPaths = [
                'ca.crt' => storage_path('app/private/certs/ca.crt'),
                'client.crt' => storage_path('app/private/certs/client.crt'),
                'client.key' => storage_path('app/private/certs/client.key')
            ];

            foreach ($certPaths as $name => $path) {
                if (!file_exists($path)) {
                    Log::warning("Certificate file not found: {$name}");
                    continue;
                }
                Log::info("Certificate file verified: {$name}", [
                    'path' => $path,
                    'size' => filesize($path)
                ]);
            }
        }
    }

    public function render()
    {
        return view(
            'livewire.broker-setting',
            [
                'broker_auth_types' => config('mqtt_broker.auth_type'),
            ]
        );
    }
}
