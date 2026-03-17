<?php

use App\Models\Preset;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(\Database\Seeders\PresetSeeder::class);
});

it('seeds exactly 14 presets', function () {
    expect(Preset::count())->toBe(14);
});

it('seeds all initialization presets', function () {
    expect(Preset::where('name', 'System Initialize')->exists())->toBeTrue();
    expect(Preset::where('name', 'System Shutdown')->exists())->toBeTrue();
});

it('seeds all temperature control presets', function () {
    expect(Preset::where('name', 'Heat to 37°C')->exists())->toBeTrue();
    expect(Preset::where('name', 'Cool to 4°C')->exists())->toBeTrue();
    expect(Preset::where('name', 'TEC Disable')->exists())->toBeTrue();
});

it('seeds all fluid handling presets', function () {
    expect(Preset::where('name', 'Aspirate Sample (100µL)')->exists())->toBeTrue();
    expect(Preset::where('name', 'Aspirate Sample (250µL)')->exists())->toBeTrue();
    expect(Preset::where('name', 'Dispense to Waste')->exists())->toBeTrue();
    expect(Preset::where('name', 'Dispense to Collection')->exists())->toBeTrue();
});

it('seeds all mixing presets', function () {
    expect(Preset::where('name', 'Mix High Speed')->exists())->toBeTrue();
    expect(Preset::where('name', 'Mix Low Speed')->exists())->toBeTrue();
    expect(Preset::where('name', 'Stop Stirrer')->exists())->toBeTrue();
});

it('seeds wash cycle preset', function () {
    $preset = Preset::where('name', 'Wash Cycle (3x)')->first();
    expect($preset)->not->toBeNull();
    // 3 wash cycles × 6 commands each + 1 final close = 19 commands
    expect(count($preset->commands))->toBe(19);
});

it('seeds draft preset for testing', function () {
    $draft = Preset::where('name', 'Custom Temperature Ramp')->first();
    expect($draft)->not->toBeNull();
    expect($draft->status)->toBe('Draft');
});

it('all active presets have version 0.32', function () {
    $presets = Preset::where('status', 'Active')->get();
    expect($presets->count())->toBe(13); // 14 total - 1 draft

    foreach ($presets as $preset) {
        expect($preset->version)->toBe('0.32');
    }
});

it('all presets have valid commands structure', function () {
    $presets = Preset::all();

    foreach ($presets as $preset) {
        expect($preset->commands)->toBeArray();
        expect(count($preset->commands))->toBeGreaterThan(0);

        foreach ($preset->commands as $cmd) {
            expect($cmd)->toHaveKeys(['controller', 'action', 'type', 'delay', 'timeout']);
            expect($cmd['controller'])->toBeIn(['tec', 'stirrer', 'microvalve', 'pump']);
        }
    }
});

it('system initialize preset has correct command sequence', function () {
    $preset = Preset::where('name', 'System Initialize')->first();
    $commands = $preset->commands;

    expect(count($commands))->toBe(5);
    expect($commands[0]['controller'])->toBe('pump');
    expect($commands[0]['action'])->toBe('init');
    expect($commands[1]['controller'])->toBe('pump');
    expect($commands[1]['action'])->toBe('home');
    expect($commands[2]['controller'])->toBe('stirrer');
    expect($commands[2]['action'])->toBe('stop');
    expect($commands[3]['controller'])->toBe('microvalve');
    expect($commands[3]['action'])->toBe('close');
    expect($commands[4]['controller'])->toBe('tec');
    expect($commands[4]['action'])->toBe('setpoint');
    expect((float) $commands[4]['value'])->toBe(25.0);
});

it('does not duplicate presets when seeder runs twice', function () {
    // Already seeded in beforeEach, seed again
    $this->seed(\Database\Seeders\PresetSeeder::class);

    expect(Preset::count())->toBe(14);
});

it('heat preset has correct temperature and stirrer', function () {
    $preset = Preset::where('name', 'Heat to 37°C')->first();
    $commands = $preset->commands;

    // Should enable TEC, set 37°C, start stirrer
    expect(count($commands))->toBe(3);
    expect($commands[1]['controller'])->toBe('tec');
    expect($commands[1]['action'])->toBe('setpoint');
    expect((float) $commands[1]['value'])->toBe(37.0);
    expect($commands[2]['controller'])->toBe('stirrer');
    expect($commands[2]['action'])->toBe('speed');
    expect((int) $commands[2]['value'])->toBe(200);
});

it('aspirate presets open valve before and close after', function () {
    $preset100 = Preset::where('name', 'Aspirate Sample (100µL)')->first();
    $commands = $preset100->commands;

    // First command: open valve
    expect($commands[0]['controller'])->toBe('microvalve');
    expect($commands[0]['action'])->toBe('open');

    // Middle: aspirate
    expect($commands[1]['controller'])->toBe('pump');
    expect($commands[1]['action'])->toBe('aspirate');
    expect((float) $commands[1]['value'])->toBe(100.0);

    // Last: close valve
    expect($commands[2]['controller'])->toBe('microvalve');
    expect($commands[2]['action'])->toBe('close');
});

it('shutdown preset stops all systems safely', function () {
    $preset = Preset::where('name', 'System Shutdown')->first();
    $commands = $preset->commands;

    expect(count($commands))->toBe(4);
    // Stop stirrer, disable TEC, close valves, home pump
    expect($commands[0]['controller'])->toBe('stirrer');
    expect($commands[0]['action'])->toBe('stop');
    expect($commands[1]['controller'])->toBe('tec');
    expect($commands[1]['action'])->toBe('enable');
    expect($commands[1]['value'])->toBe(0);
    expect($commands[2]['controller'])->toBe('microvalve');
    expect($commands[2]['action'])->toBe('close');
    expect($commands[3]['controller'])->toBe('pump');
    expect($commands[3]['action'])->toBe('home');
});
