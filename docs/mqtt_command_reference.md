# MQTT Topic Requirements Reference

This document outlines the MQTT topic structure and payload requirements extracted from [MQTT Command Reference.txt](file:///c:/Users/HP/projects/project-pancake/docs/MQTT%20Command%20Reference.txt) to assist the **Backend Architect** in designing data pipelines and ingestion strategies.

## Global Pattern

`adc/controller/{device_id}/{type}/{device}/{action}`

- `{device_id}`: e.g., `adc-001`
- `{type}`: `command` (write) or `status` (read)

---

## 1. Temperature Control (TEC)

| Topic Segment | Action        | Requirement/Comment                          | Payload Example         |
| :------------ | :------------ | :------------------------------------------- | :---------------------- |
| `command/tec` | `setpoint`    | Target temperature in °C (Range: **0-50°C**) | `"37.0"`                |
| `command/tec` | `enable`      | Control switch                               | `"1"` (On), `"0"` (Off) |
| `status/tec`  | `temperature` | Published every **2 seconds**                | `"36.5"`                |

## 2. Stirrer

| Topic Segment     | Action  | Requirement/Comment              | Payload Example |
| :---------------- | :------ | :------------------------------- | :-------------- |
| `command/stirrer` | `speed` | Speed in RPM (Range: **0-1000**) | `"500"`         |
| `command/stirrer` | `stop`  | Stops the stirrer                | `""` (Empty)    |

## 3. Microvalves

| Topic Segment        | Action  | Requirement/Comment                               | Payload Example |
| :------------------- | :------ | :------------------------------------------------ | :-------------- |
| `command/microvalve` | `set`   | Format: `"valve_number,state"` (0=closed, 1=open) | `"0,1"`         |
| `command/microvalve` | `open`  | Open specific valve                               | `"3"`           |
| `command/microvalve` | `close` | Close specific valve                              | `"7"`           |

## 4. Pumps (`pump_0`)

| Topic Segment    | Action     | Requirement/Comment                     | Payload Example |
| :--------------- | :--------- | :-------------------------------------- | :-------------- |
| `command/pump_0` | `init`     | Enable and home pump in one command     | `""` (Empty)    |
| `command/pump_0` | `aspirate` | Volume in microliters (**µL**)          | `"1500"`        |
| `command/pump_0` | `dispense` | Volume in microliters (**µL**)          | `"750"`         |
| `command/pump_0` | `home`     | Return to zero position (empty syringe) | `""` (Empty)    |

## 5. Protocol Management

| Topic Segment      | Action  | Requirement/Comment                  | Payload Example                            |
| :----------------- | :------ | :----------------------------------- | :----------------------------------------- |
| `command/protocol` | `start` | Starts a protocol by its Process UID | `{"type": "protocol_start", "uid": "..."}` |
| `command/protocol` | `stop`  | Stops the current protocol execution | `{"type": "protocol_stop", "uid": "..."}`  |

---

> [!TIP]
> **Implementation Strategy for Backend Architect:**
> Following the [mqtt-expert.md](file:///c:/Users/HP/projects/project-pancake/.ai/agents/mqtt-expert.md) guidance, it is recommended to utilize **Wildcard Subscriptions** (e.g., `adc/controller/+/status/#`) for initial ingestion to minimize broker overhead. Data should be parsed and routed using "starts-with" attribute routing in Laravel.
