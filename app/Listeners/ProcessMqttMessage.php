<?php

namespace App\Listeners;

use App\Events\MqttMessageReceived;
use App\Models\MqttMessage;
use App\Models\DeviceComponent;
use App\Models\DeviceComponentLog;
use App\Models\Setting;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class ProcessMqttMessage
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(MqttMessageReceived $event)
    {
        try {
            $topic = $event->topic;
            $message = $event->message;

            $data = json_decode($message);

            // If data is not valid JSON, we skip processing but don't crash the worker
            if (!$data) {
                return;
            }

            // Note: MqttMessage creation is handled in MqttService to avoid duplication

            // Attempt to find device component if identifier is provided
            $deviceComponent = null;
            $deviceComponentId = $data->device_component_id ?? $data->component_id ?? $data->sensor_id ?? null;
            if ($deviceComponentId) {
                $deviceComponent = DeviceComponent::find($deviceComponentId);
            }

            if ($deviceComponent) {
                // Architectural Alignment: State and Telemetry are now handled directly in MqttService
                // for faster real-time response and reliability.
            }

            // --- Protocol Automation: Command Dispatch ---
            if (isset($data->type) && in_array($data->type, ['phase_start', 'protocol_start', 'protocol_end'])) {
                $processId = $data->protocol_process_id ?? null;
                $phaseLabel = $data->label ?? null;

                if ($processId && ($process = \App\Models\ProtocolProcess::where('uid', $processId)->first())) {
                    Log::info("[Automation] Processing {$data->type} for Process: {$processId}");
                    $protocol = $process->protocol;
                    $phases = $protocol->phases ?? [];
                    Log::debug("[Automation] Available Phases: " . json_encode($phases));
                    $matchingPhase = null;

                    if ($data->type === 'protocol_end') {
                        $matchingPhase = collect($phases)->first(fn($p) => ($p['is_end'] ?? false) === true || ($p['label'] ?? '') === 'End Of Protocol');
                        Log::info("[Automation] protocol_end detected. Cleanup phase found: " . ($matchingPhase ? 'Yes' : 'No'));
                    } elseif ($data->type === 'protocol_start' || $data->type === 'phase_start') {
                        $phaseId = $data->phase_id ?? null;
                        if ($phaseId) {
                            $matchingPhase = collect($phases)->first(fn($p) => ($p['id'] ?? '') === $phaseId);
                        }

                        if (!$matchingPhase && $phaseLabel) {
                            $matchingPhase = collect($phases)->first(fn($p) => ($p['label'] ?? '') === $phaseLabel);
                        }

                        // Fallback for protocol_start if no ID/Label matched
                        if (!$matchingPhase && $data->type === 'protocol_start' && !empty($phases)) {
                            $matchingPhase = array_values($phases)[0];
                        }

                        Log::info("[Automation] Matching phase for '{$phaseLabel}' (ID: {$phaseId}): " . ($matchingPhase['label'] ?? 'Not Found'));
                    }

                    if ($matchingPhase) {
                        $topicParts = explode('/', $topic);
                        $deviceId = $topicParts[2] ?? 'adc-001';

                        Log::info("[Automation] Dispatching commands for phase: " . ($matchingPhase['label'] ?? 'Unnamed'));
                        app(\App\Services\MqttService::class)->sendPhaseCommands($deviceId, $matchingPhase);
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error("MQTT Processing Error: " . $e->getMessage());
        }
    }
}
