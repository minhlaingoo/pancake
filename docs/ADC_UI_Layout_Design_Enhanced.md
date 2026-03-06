# **ADC Control & Automation System**

## **UI Layout Design Document (Enhanced with Tables & Diagrams)**

Generated on: 2026-03-03 01:14:23

## **1\. Sequence Builder & Execution (Section 2.2)**

### **1.1 Overall Layout Structure**

\-------------------------------------------------------------  
| Top Bar: Preset | Version | Status | Run Controls        |  
\-------------------------------------------------------------  
| Step List | Step Editor | Live Monitor                   |  
\-------------------------------------------------------------  
| Bottom Panel: Logs / MQTT Traffic / System Events        |  
\-------------------------------------------------------------

### **1.2 Step List Panel Layout (Left Panel)**

| Step No. | Device | Action | Delay (s) |
| :---- | :---- | :---- | :---- |
| 1 | TEC / Valve / Stirrer | Setpoint / Init / Stop | 5 |
| 2 | TEC / Valve / Stirrer | Setpoint / Init / Stop | 5 |
| 3 | TEC / Valve / Stirrer | Setpoint / Init / Stop | 5 |
| 4 | TEC / Valve / Stirrer | Setpoint / Init / Stop | 5 |
| 5 | TEC / Valve / Stirrer | Setpoint / Init / Stop | 5 |

Features:  
• Drag & Drop reordering  
• Duplicate / Delete step  
• Status color indicators (Idle, Running, Completed, Error)  
• Validation warning icon

### **1.3 Step Editor Configuration Table (Center Panel)**

| Field | Description |
| :---- | :---- |
|  |  |
|  |  |
|  |  |
|  |  |
|  |  |
|  |  |
| Device | Dropdown list of hardware components |
| Action | Command to execute |
| Topic (Auto) | Generated MQTT topic (Read-only) |
| Payload | Parameter or JSON command body |
| Delay | Delay after execution (seconds) |
| Retry Count | Number of retry attempts |
| Timeout | Maximum waiting time for response |

### **1.4 Live Execution Monitor (Right Panel)**

Execution Status Example:

Current Step: 12 / 60  
Device: TEC  
Command: setpoint 25.0  
Remaining Delay: 3s  
Status: RUNNING  
Progress: 65%

### **1.5 Execution Flow Diagram**

\[Start\]  
   ↓  
\[Load Preset\]  
   ↓  
\[Validate Steps\]  
   ↓  
\[Execute Step 1\]  
   ↓  
\[Wait Delay / Status\]  
   ↓  
\[Next Step\]  
   ↓  
\[Complete or Emergency Stop\]  
   ↓  
\[End\]

### **1.6 Execution Log Table (Bottom Panel)**

| Timestamp | Step | Device | Command | Status |
| :---- | :---- | :---- | :---- | :---- |
| 17:45:02 | 03 | Rotary Valve 1 | init | OK |

## **2\. Preset Management System (Section 2.3)**

### **2.1 Preset Table Layout**

| Preset Name | Version | Steps | Author | Date | Status |
| :---- | :---- | :---- | :---- | :---- | :---- |
| ADC\_STD\_01 | 1.2 | 48 | Admin | 2026-03-02 | Validated |

### **2.2 Preset Workflow Diagram**

\[Create Preset\]  
   ↓  
\[Add Steps\]  
   ↓  
\[Validate\]  
   ↓  
\[Save Version\]  
   ↓  
\[Clone / Export / Run\]

### **2.3 Version History Table**

| Version | Changes | Date |
| :---- | :---- | :---- |
| 1.1 | Updated TEC delay parameter | 2026-02-28 |

