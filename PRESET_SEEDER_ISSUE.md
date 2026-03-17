# 🐛 Preset Seeder Issue - Wrong Structure

## Problem Identified
- **Expected:** 14 different presets from Excel file v0.32
- **Actual:** Only 5 manual presets created  
- **User Report:** Only one initialization preset with 62 steps being generated

## Root Cause
The PresetSeeder.php I created:
1. ❌ **Hardcoded only 5 basic presets** instead of reading Excel data
2. ❌ **Doesn't parse the actual Excel file** `Device_Flow Setup_V0.32 (initialization only).xlsx` 
3. ❌ **Creates manually defined commands** instead of using the real flow data
4. ❌ **Missing 9 other preset batches** that should exist

## Current Seeder Structure (WRONG)
```php
$presets = [
    'System Initialize' (11 commands),
    'Aspirate Sample (100µL) - Pump 0' (3 commands), 
    'Aspirate Sample (250µL) - Pump 1' (3 commands),
    'Rotary Valve Position Test' (2 commands),
    'System Shutdown' (9 commands),
];
// Total: Only 5 presets, ~28 commands total
```

## Expected Structure (CORRECT)
Should parse Excel file and create:
```php
$presets = [
    'Initialization' (62+ steps),
    'Sample Preparation' (?? steps),
    'Wash Cycle 1' (?? steps),
    'Wash Cycle 2' (?? steps),
    // ... 10 more presets
];
// Total: 14 presets with proper step counts
```

## Files to Check
- `docs/Device_Flow Setup_V0.32 (initialization only).xlsx` - Source data
- `docs/Device Flow Setup_V0.31.csv` - Older version (only initialization)
- `database/seeders/PresetSeeder.php` - Current broken seeder

## Next Steps
1. Parse the Excel file properly 
2. Extract all 14 preset batches
3. Create separate presets for each batch
4. Use the actual command sequences from Excel
5. Apply OC device naming (pump_0/1, rotary_valve_1/2, microvalve 0-15)

## Questions for User
1. What are the names of all 14 preset batches?
2. Should I create a CSV export of the Excel file?
3. How are the 62+ steps divided into the different presets?

**Status: 🔴 BLOCKED - Need Excel data structure clarification**