# Protocol Processing Logic

This document defines the sequential phase logic for device protocols, controlling four primary hardware components via MQTT.

## Data Model: Protocol & Phases

A Protocol is a collection of ordered Phases. Every phase (including `start`) includes a `duration` in seconds.

### Protocol Schema Example

```json
{
    "protocol_id": "p-001",
    "name": "Standard Analysis",
    "phases": [
        {
            "type": "start",
            "duration": 60,
            "config": {
                "tec": { "action": "enable", "value": 1 },
                "pump": { "action": "init" }
            }
        },
        {
            "type": "process",
            "name": "Incubation",
            "duration": 1800,
            "config": {
                "tec": { "action": "setpoint", "value": 37.0 },
                "stirrer": { "action": "speed", "value": 500 }
            }
        },
        {
            "type": "end",
            "duration": 0,
            "config": {
                "stirrer": { "action": "stop" },
                "pump": { "action": "home" }
            }
        }
    ]
}
```

## Controller Instruction Mapping

| Controller     | Primary Action      | Parameter Example         |
| :------------- | :------------------ | :------------------------ |
| **TEC**        | Control Temperature | `37.0` (°C), `1` (Enable) |
| **Stirrer**    | Control Mixing      | `500` (RPM)               |
| **Microvalve** | Control Valves      | `"3,1"` (Valve, State)    |
| **Pump**       | Fluid Handling      | `1500` (µL)               |

## Execution Lifecycle

1.  **Initialize**: Load Protocol and validate hardware connectivity.
2.  **Phase Execution Loop**:
    - For each Phase in `phases[]`:
        1.  **Dispatch Commands**: Send MQTT Publish messages for all configured controllers in the current phase.
        2.  **Monitor Status**: Subscribe to RX topics to verify hardware response (e.g., Pump move complete).
        3.  **Wait**: Delay for the specified `duration` (seconds).
        4.  **Transition**: Move to the next phase only after both duration expires and mandatory hardware confirmations are received.
3.  **Completion**: Execute `end` phase cleanup and log protocol results.
