<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class DeviceService
{
    /**
     * Check if a device is reachable via a specific port and detect its model.
     *
     * @param string $ip
     * @param int $port
     * @param int $timeout Seconds
     * @return array|bool Returns ['model' => string, 'name' => string] if successful, false otherwise.
     */
    public function checkConnection(string $ip, int $port, int $timeout = 2): array|bool
    {
        $connection = @fsockopen($ip, $port, $errno, $errstr, $timeout);

        if (is_resource($connection)) {
            fclose($connection);

            // Simulated model detection logic
            $detectedModel = "Detected-" . strtoupper(substr(md5($ip . $port), 0, 6));

            return [
                'model' => $detectedModel,
                'name' => $detectedModel,
            ];
        }

        Log::warning("Device connection failed to {$ip}:{$port} - Error: {$errstr} ({$errno})");
        return false;
    }
}
