# 📡 MQTT Simulation Guide

This guide provides step-by-step instructions to run the hardware simulator and verify the data flow in the Protocol Monitor.

---

## 🛠️ 1. Environment Setup

The simulator requires Python 3 and the `paho-mqtt` library (v2.0 compatible).

```bash
# Navigate to the scripts directory
cd scripts

# Install dependencies
pip install paho-mqtt
```

---

## 🚀 2. Running the Simulator

The simulator acts as the physical hardware. It listens for commands from Laravel and streams mock sensor data.

```bash
# Start the simulator
python mqtt_simulator.py
```

- **Output**: You should see `Subscribed to: adc/controller/adc-001/command/protocol/#`.
- **Idle State**: The script will wait until you interact with the Web UI.

---

## 🌐 3. Web UI Interaction

1. Open your browser to the **Protocol Processing** page.
2. Select or create a protocol.
3. Click the **"Start"** button.
4. **Simulator Response**: The console will show `Received Command: protocol_start` and begin streaming data.
5. **Chart**: The live chart should start plotting Temperature and Volume automatically.

---

## 🔍 4. Manual Monitoring (Optional)

If you want to see the "raw" messages passing through the broker, use `mosquitto_sub` in a separate terminal:

### Monitor Commands (Laravel -> Hardware)

```bash
mosquitto_sub -t "adc/controller/+/command/protocol/#" -v
```

### Monitor Telemetry (Hardware -> DB)

```bash
mosquitto_sub -t "adc/controller/+/status/protocol/stream" -v
```

---

## 🛡️ Troubleshooting

- **Connection Refused**: Ensure your MQTT broker (e.g., Mosquitto) is running on `localhost:1883`.
- **No Data on Chart**: Ensure the `php artisan queue:listen` (or `mqtt:subscribe`) command is running in the background to ingest messages into the database.
- **Python Error**: If you get a `ValueError` regarding callback versions, ensure you are using the latest version of `mqtt_simulator.py` which supports `paho-mqtt` 2.0.
