<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use PhpMqtt\Client\MqttClient;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(DefaultRolePermissionSeeder::class);
        $this->call(DeviceSeeder::class);
        $this->call(PresetSeeder::class);

        // User::factory()->create();
        $adminRole = \App\Models\Role::where('name', 'Administrator')->first();
        
        if (!User::where('email', 'admin@iprogen.com')->exists()) {
            $user = User::factory()->create([
                'name' => 'Admin PanCake',
                'email' => 'admin@iprogen.com',
                'role_id' => $adminRole?->id,
            ]);
        } else {
            // Ensure existing admin user has Administrator role
            $user = User::where('email', 'admin@iprogen.com')->first();
            if ($adminRole && $user->role_id !== $adminRole->id) {
                $user->update(['role_id' => $adminRole->id]);
            }
        }

        $settingFactory = new \Database\Factories\SettingFactory();
        $settingFactory->createSettingIfNotExist('general', [
            'appName' => 'PanCake',
            'appDescription' => 'A simple and elegant pancake management system.',
        ]);

        $settingFactory->updateSetting('general', 'term_and_policy', 'This system is the property of PanCake ee company. Unauthorized access or use is strictly prohibited and may result in legal action. Activities are monitored and recorded. By logging in, you acknowledge and agree to comply with company policies and security guidelines.
        ');

        $settingFactory->createSettingIfNotExist('broker', [

            'url' => 'localhost',
            'port' => 1883,
            'protocol_version' => MqttClient::MQTT_3_1,
            'client_id' => 'client_' . uniqid(),
            'keep_alive_interval' => 60,
            'clean_session' => true,
            'auth_type' => 'none', // or 'basic' / 'tls'
            'username' => null,
            'password' => null,
            'can_publish' => true,
            'enable_log' => false,
            'subscribe_topic' => '#',
            'subscribe_qos' => 0,
            'subscribe_retain' => false,

            // TLS options
            'tls_enabled' => false,
            'tls_verify_peer' => true,
            'tls_verify_peer_name' => true,
            'tls_self_signed_allowed' => false,


        ]);
    }
}
