# MQTT Backend Architecture: Command & Telemetry Mapping

For an expert-level Laravel setup, the backend acts as a **Command Dispatcher (TX)** and a **Telemetry Aggregator (RX)**. Below is the technical mapping for your specific project requirements.

| Component         | Server Action  |               Topic Pattern (StartWith)               |       MqttService Method       |                                    Payload / Logic                                    |        Process         |     Apply To      |
| :---------------- | :------------: | :---------------------------------------------------: | :----------------------------: | :-----------------------------------------------------------------------------------: | :--------------------: | :---------------: |
| **Global Status** | Subscribe (RX) |         `adc/controller/{deviceId}/status/#`          | `subscribeToDeviceStatus($id)` |                        JSON: `{"sensor_id": id, "value": val}`                        |        Polling         |      Device       |
| **TEC**           |  Publish (TX)  |   `adc/controller/{deviceId}/command/tec/setpoint`    |  `tecSetSetpoint($id, $temp)`  |                            Raw: `37.0` (0-50°C validated).                            | Before, During Process | Protocol, Process |
| **TEC**           |  Publish (TX)  |    `adc/controller/{deviceId}/command/tec/enable`     |    `tecEnable($id, $bool)`     |                              Raw: `1` (On) or `0` (Off).                              | Before, After Process  |  Device, Process  |
| **Stirrer**       |  Publish (TX)  |   `adc/controller/{deviceId}/command/stirrer/speed`   |  `stirrerSetSpeed($id, $rpm)`  |                          Raw: `500` (0-1000 RPM validated).                           | Before, During Process | Protocol, Process |
| **Stirrer**       |  Publish (TX)  |   `adc/controller/{deviceId}/command/stirrer/stop`    |       `stirrerStop($id)`       |                             Sends empty payload to stop.                              | During, After Process  |  Device, Process  |
| **Microvalve**    |  Publish (TX)  |  `adc/controller/{deviceId}/command/microvalve/set`   |  `microvalveSet($id, $v, $s)`  |                         Raw: `"valve,state"` (e.g., `"3,1"`).                         | Before, During Process | Protocol, Process |
| **Microvalve**    |  Publish (TX)  |  `adc/controller/{deviceId}/command/microvalve/open`  |   `microvalveOpen($id, $v)`    |                                       Raw: `3`.                                       |     During Process     | Protocol, Process |
| **Microvalve**    |  Publish (TX)  | `adc/controller/{deviceId}/command/microvalve/close`  |   `microvalveClose($id, $v)`   |                                       Raw: `7`.                                       |     During Process     |  Device, Process  |
| **Pump**          |  Publish (TX)  |   `adc/controller/{deviceId}/command/pump_{n}/init`   |      `pumpInit($id, $n)`       |                                 Sends empty payload.                                  |     Before Process     |      Device       |
| **Pump**          |  Publish (TX)  | `adc/controller/{deviceId}/command/pump_{n}/aspirate` | `pumpAspirate($id, $vol, $n)`  |                              Raw: `1500` (Volume in µL).                              |     During Process     | Protocol, Process |
| **Pump**          |  Publish (TX)  | `adc/controller/{deviceId}/command/pump_{n}/dispense` | `pumpDispense($id, $vol, $n)`  |                              Raw: `750` (Volume in µL).                               |     During Process     | Protocol, Process |
| **Pump**          |  Publish (TX)  |   `adc/controller/{deviceId}/command/pump_{n}/home`   |      `pumpHome($id, $n)`       |                       Return to zero position (empty syringe).                        |     After Process      |  Device, Process  |
| **NTP Update**    |  Publish (TX)  |                     `ntp/update`                      |      `ntpUpdate($device)`      | JSON: `{"ntp_server": "...", "timezone": "...", "ntp_interval": 3600, "time": "..."}` |     Before Process     |      Device       |

### Device Settings Data Mapping

The following fields from the `Device` model are used in MQTT payloads:

| Field          | Description                                  | Target Command |
| :------------- | :------------------------------------------- | :------------- |
| `model`        | Unique hardware identifier for topic routing | All Commands   |
| `ntp_server`   | NTP Server URL for time sync                 | `ntp/update`   |
| `timezone`     | Device timezone (e.g. Asia/Ho_Chi_Minh)      | `ntp/update`   |
| `ntp_interval` | Sync interval in seconds                     | `ntp/update`   |

### Expert Implementation Strategy

#### 1. Dynamic Topic Resolution

Use a helper method in your `MqttService` to handle the `...` (root) and `{n}` (index) placeholders.

- **Pattern:** `adc/controller/{deviceId}/command/{component}/{subpath}`

#### 2. Payload Serialization

- **Microvalve:** Ensure the string `"valve,state"` is not JSON-encoded if the firmware expects raw CSV.
- **Pumps:** Use float validation for `$vol` to ensure high precision in the backend before transmission.

#### 3. Reactive UI via Livewire/Alpine

Since you are using `wire:navigate`, your **Server Actions** should trigger these methods, which then broadcast to a private **Laravel Reverb** channel. This allows your Blade components to show "Success" or "Updating" states in real-time.

---

**Next Step:** Would you like the code for the `MqttService` methods that specifically handle the `pump_{n}` dynamic topic generation?
