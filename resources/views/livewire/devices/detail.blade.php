<div class="space-y-4">
    <x-alert />

    <!-- Tab Navigation -->
    <div class="flex gap-2 p-1 bg-muted rounded-xl w-fit border shadow-sm">
        <mijnui:button wire:click="setTab('info')" variant="{{ $activeTab === 'info' ? 'default' : 'ghost' }}" size="sm"
            class="px-6">
            <i class="fas fa-info-circle mr-2"></i> Info
        </mijnui:button>

        <mijnui:button wire:click="setTab('operations')"
            variant="{{ $activeTab === 'operations' ? 'default' : 'ghost' }}" size="sm" class="px-6">
            <i class="fas fa-terminal mr-2"></i> Operations
        </mijnui:button>
    </div>

    @if($activeTab === 'info')
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <mijnui:card>
                <mijnui:card.header class="text-lg font-medium">Device Detail</mijnui:card.header>
                <mijnui:card.content class="text-sm space-y-2">
                    <div class="flex items-center">
                        <p class="w-24">Device Name</p>
                        <p>: {{ $device->name }}</p>
                    </div>
                    <div class="flex items-center">
                        <p class="w-24">Device Model</p>
                        <p>: {{ $device->model }}</p>
                    </div>
                    <div class="flex items-center">
                        <p class="w-24">Status</p>
                        <mijnui:badge color="{{ $device->is_active ? 'success' : 'danger' }}" size="xs">
                            {{ $device->is_active ? 'Running' : 'Offline' }}
                        </mijnui:badge>
                    </div>
                </mijnui:card.content>
            </mijnui:card>

            <mijnui:card>
                <mijnui:card.header class="text-lg font-medium">Uptime Monitor</mijnui:card.header>
                <mijnui:card.content class="text-sm space-y-2">
                    <div class="flex items-center">
                        <p class="w-24">Running Time:</p>
                        <p>36 days</p>
                    </div>
                    <div class="flex items-center">
                        <p class="w-24">Last Online:</p>
                        <p>2025-03-21 10:02</p>
                    </div>
                </mijnui:card.content>
            </mijnui:card>

            <mijnui:card>
                <mijnui:card.header class="text-lg font-medium">Battery Status</mijnui:card.header>
                <mijnui:card.content class="text-sm space-y-2">
                    <div class="flex items-center">
                        <p class="w-24">Level:</p>
                        <p>86%</p>
                    </div>
                    <div class="flex items-center">
                        <p class="w-24">Status:</p>
                        <p>Charging</p>
                    </div>
                </mijnui:card.content>
            </mijnui:card>
        </div>
    @endif



    @if($activeTab === 'operations')
        <div class="grid grid-cols-12 gap-4">
            <!-- Left: Controls -->
            <div class="col-span-12 lg:col-span-8 space-y-4">
                <!-- Manual Command Testing -->
                <mijnui:card class="border-2 border-primary/20 bg-primary/5">
                    <mijnui:card.header>
                        <div class="flex items-center gap-2">
                            <div class="h-8 w-8 rounded-lg bg-primary/10 flex items-center justify-center">
                                <i class="fas fa-vial text-primary text-sm"></i>
                            </div>
                            <mijnui:card.title class="text-lg font-bold">Manual Command Testing</mijnui:card.title>
                        </div>
                    </mijnui:card.header>
                    <mijnui:card.content>
                        <div class="space-y-3">
                            @foreach($testCommands as $index => $command)
                                <div
                                    class="flex items-center gap-2 group relative bg-background p-3 rounded-lg border shadow-sm">
                                    <div class="flex-1 grid grid-cols-12 gap-4">
                                        <div class="col-span-4">
                                            <label
                                                class="text-[10px] uppercase font-bold text-muted-foreground block mb-1">Controller</label>
                                            <div
                                                class="flex h-9 w-full items-center rounded-md border border-input bg-muted/50 px-3 py-1 text-sm font-semibold uppercase tracking-wide">
                                                {{ str_replace('_', ' ', $command['controller']) }}
                                            </div>
                                        </div>
                                        <div class="col-span-4">
                                            <label
                                                class="text-[10px] uppercase font-bold text-muted-foreground block mb-1">Action</label>
                                            <select wire:model="testCommands.{{ $index }}.action"
                                                wire:change="actionChanged({{ $index }}, $event.target.value)"
                                                class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm">
                                                <option value="">Select Action...</option>
                                                @if($command['controller'])
                                                    @foreach($controllerCommands[$command['controller']] ?? [] as $cmdPath => $defaultType)
                                                        <option value="{{ $cmdPath }}">{{ Str::headline($cmdPath) }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        <div class="col-span-4 flex items-end gap-2">
                                            <div class="flex-1">
                                                <label
                                                    class="text-[10px] uppercase font-bold text-muted-foreground block mb-1">
                                                    Value (Type: {{ $command['type'] ?? 'undefined' }})
                                                </label>
                                                {{-- Debug: Type = {{ $command['type'] ?? 'null' }} --}}
                                                @if($command['type'] === 'microvalve_select')
                                                    {{-- Microvalve Select --}}
                                                    <select wire:model="testCommands.{{ $index }}.value"
                                                        class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm">
                                                        <option value="">Select Microvalve...</option>
                                                        {{-- Show available microvalves first --}}
                                                        @foreach($availableMicrovalves as $v)
                                                            <option value="{{ $v }}" class="text-green-600">
                                                                Microvalve {{ $v }} (Available)
                                                            </option>
                                                        @endforeach
                                                        {{-- Show unavailable microvalves (0-15 range, excluding available ones) --}}
                                                        @for($v = 0; $v <= 15; $v++)
                                                            @if(!in_array($v, $availableMicrovalves))
                                                                <option value="{{ $v }}" class="text-gray-400" disabled>
                                                                    Microvalve {{ $v }} (Not Present)
                                                                </option>
                                                            @endif
                                                        @endfor
                                                    </select>
                                                @elseif($command['type'] === 'none')
                                                    {{-- None Type (disabled) --}}
                                                    <mijnui:input wire:model="testCommands.{{ $index }}.value" class="h-9" 
                                                        placeholder="No value required" disabled />
                                                @elseif($command['type'] === 'valve_select')
                                                    {{-- Valve Select --}}
                                                    <select wire:model="testCommands.{{ $index }}.value"
                                                        class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm">
                                                        <option value="">Select Valve...</option>
                                                        @for($v = 1; $v <= 16; $v++)
                                                            <option value="{{ $v }}">Valve {{ $v }}</option>
                                                        @endfor
                                                    </select>
                                                @else
                                                    {{-- Default: int, float, string --}}
                                                    @php
                                                        $inputType = match($command['type']) {
                                                            'int' => 'number',
                                                            'float' => 'number',
                                                            default => 'text'
                                                        };
                                                        $inputStep = $command['type'] === 'float' ? '0.01' : '1';
                                                        $inputMin = in_array($command['type'], ['int', 'float']) ? '0' : null;
                                                    @endphp
                                                    <div>
                                                        <input 
                                                            wire:model.blur="testCommands.{{ $index }}.value" 
                                                            type="{{ $inputType }}"
                                                            @if($inputStep !== '1') step="{{ $inputStep }}" @endif
                                                            @if($inputMin) min="{{ $inputMin }}" @endif
                                                            class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm 
                                                                   @error('testCommands.'.$index.'.value') border-red-500 @enderror"
                                                            placeholder="Enter {{ $command['type'] ?? 'value' }}"
                                                        />
                                                        @error('testCommands.'.$index.'.value')
                                                            <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                @endif
                                            </div>
                                            <mijnui:button wire:click="sendSingleTestCommand({{ $index }})" color="primary"
                                                size="icon-sm" title="Run this command">
                                                <i class="fas fa-play"></i>
                                            </mijnui:button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </mijnui:card.content>
                </mijnui:card>

                <!-- Preset Commands -->
                <mijnui:card class="border-2 border-primary/20 bg-primary/5">
                    <mijnui:card.header>
                        <div class="flex items-center gap-2">
                            <div class="h-8 w-8 rounded-lg bg-primary/10 flex items-center justify-center">
                                <i class="fas fa-layer-group text-primary text-sm"></i>
                            </div>
                            <mijnui:card.title class="text-lg font-bold">Preset Commands</mijnui:card.title>
                        </div>
                    </mijnui:card.header>
                    <mijnui:card.content>
                        <div class="flex items-end gap-4">
                            <div class="flex-1">
                                <label class="text-[10px] uppercase font-bold text-muted-foreground block mb-1">Select
                                    Preset</label>
                                <select wire:model.live="selectedPresetId"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm">
                                    <option value="">Select a preset...</option>
                                    @foreach($presets as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <mijnui:button wire:click="sendPreset" color="primary" :disabled="!$selectedPresetId">
                                <i class="fas fa-play mr-2"></i> Run Preset
                            </mijnui:button>
                        </div>
                    </mijnui:card.content>
                </mijnui:card>
            </div>

            <!-- Right: Monitors -->
            <div class="col-span-12 lg:col-span-4 space-y-4">
                <!-- Monitor Panel -->
                <mijnui:card
                    class="h-[300px] flex flex-col bg-zinc-950 text-emerald-500 font-mono text-[10px] overflow-hidden">
                    <mijnui:card.header
                        class="py-2 border-b border-white/10 flex justify-between items-center bg-zinc-900 px-3">
                        <span class="font-bold uppercase tracking-wider text-muted-foreground">Live Monitor</span>
                    </mijnui:card.header>
                    <div class="flex-1 overflow-y-auto p-3 space-y-1 custom-scrollbar">
                        @php $incomingLogs = collect($logs)->filter(fn($l) => ($l['type'] ?? '') !== 'outgoing')->values(); @endphp
                        @forelse($incomingLogs as $log)
                            <div class="flex gap-2">
                                <span class="text-zinc-500">[{{ $log['time'] }}]</span>
                                <span class="text-emerald-400 font-bold">RX <<< /span>
                                        <div class="flex-1 flex flex-col">
                                            <span class="text-zinc-400 break-all">{{ $log['topic'] }}</span>
                                            <span class="text-white">{{ $log['message'] }}</span>
                                        </div>
                            </div>
                        @empty
                            <div class="h-full flex flex-col items-center justify-center opacity-30 italic">
                                <span>Waiting for incoming data...</span>
                            </div>
                        @endforelse
                    </div>
                </mijnui:card>

                <!-- Execution Log -->
                <mijnui:card
                    class="h-[400px] flex flex-col bg-zinc-950 text-emerald-500 font-mono text-[10px] overflow-hidden">
                    <mijnui:card.header
                        class="py-2 border-b border-white/10 flex justify-between items-center bg-zinc-900 px-3">
                        <span class="font-bold uppercase tracking-wider text-muted-foreground">Execution Log</span>
                        <mijnui:button wire:click="clearLogs" variant="ghost" size="icon-sm"
                            class="h-6 w-6 text-muted-foreground hover:text-white">
                            <i class="fas fa-trash-alt text-[8px]"></i>
                        </mijnui:button>
                    </mijnui:card.header>
                    <div class="flex-1 overflow-y-auto p-3 space-y-1 custom-scrollbar">
                        @php $outgoingLogs = collect($logs)->filter(fn($l) => ($l['type'] ?? '') === 'outgoing')->values(); @endphp
                        @forelse($outgoingLogs as $log)
                            <div class="flex gap-2">
                                <span class="text-zinc-500">[{{ $log['time'] }}]</span>
                                <span class="text-blue-400 font-bold">TX >></span>
                                <div class="flex-1 flex flex-col">
                                    <span class="text-zinc-400 break-all">{{ $log['topic'] }}</span>
                                    <span class="text-white">{{ $log['message'] }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="h-full flex flex-col items-center justify-center opacity-30 italic">
                                <span>No commands sent...</span>
                            </div>
                        @endforelse
                    </div>
                </mijnui:card>
            </div>
        </div>
    @endif
</div>