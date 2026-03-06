<div class="flex flex-col h-[calc(100vh-120px)] overflow-hidden space-y-4">
    <!-- Top Bar -->
    <div class="flex items-center justify-between bg-card p-4 rounded-xl border shadow-sm">
        <div class="flex items-center gap-6">
            <div class="flex flex-col">
                <div class="flex items-center gap-2">
                    <input type="text" wire:model="name"
                        class="bg-transparent font-bold text-lg border-none focus:ring-0 p-0 w-48"
                        placeholder="New Preset Name">
                    @error('name') <span class="text-destructive text-[10px]">{{ $message }}</span> @enderror
                    <span class="text-muted-foreground text-xs italic">v</span>
                    <input type="text" wire:model="version"
                        class="bg-transparent text-sm border-none focus:ring-0 p-0 w-12 text-muted-foreground"
                        placeholder="1.0">
                    @error('version') <span class="text-destructive text-[10px]">{{ $message }}</span> @enderror
                </div>
                <span class="text-[10px] text-muted-foreground uppercase font-bold tracking-wider">Author:
                    {{ $author ?? 'System' }}</span>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2 bg-muted/30 px-3 py-1.5 rounded-lg border">
                <span class="text-[10px] uppercase font-bold text-muted-foreground">Status</span>
                <select wire:model="status"
                    class="bg-transparent text-sm border-none focus:ring-0 p-0 font-semibold appearance-none cursor-pointer">
                    <option value="Draft">Draft</option>
                    <option value="Validated">Validated</option>
                    <option value="Error">Error</option>
                </select>
            </div>

            <div class="h-8 w-px bg-border"></div>

            <div class="flex items-center gap-2">
                <mijnui:button wire:click="save" color="primary" size="sm" class="gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                        <polyline points="17 21 17 13 7 13 7 21" />
                        <path d="M7 3v5h8" />
                    </svg>
                    Save
                </mijnui:button>
            </div>
        </div>
    </div>

    <!-- Main Layout -->
    <div class="flex-1 grid grid-cols-12 gap-4 overflow-hidden">
        <!-- Left Panel: Step List -->
        <div class="col-span-3 bg-card rounded-xl border flex flex-col overflow-hidden shadow-sm">
            <div class="p-3 border-b bg-muted/20 flex justify-between items-center">
                <h3 class="text-xs font-bold uppercase tracking-wider text-muted-foreground">Step List</h3>
                <mijnui:button wire:click="addCommand" variant="ghost" size="icon-sm" class="h-6 w-6">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                </mijnui:button>
            </div>
            <div class="flex-1 overflow-y-auto p-2 space-y-1">
                @foreach($commands as $index => $command)
                    <div wire:click="selectStep({{ $index }})"
                        class="group flex items-center gap-3 p-2 rounded-lg cursor-pointer transition-all border {{ $selectedStepIndex === $index ? 'bg-primary/10 border-primary shadow-sm' : 'hover:bg-muted/50 border-transparent' }}">
                        <div
                            class="shrink-0 w-6 h-6 rounded bg-muted flex items-center justify-center text-[10px] font-bold {{ $selectedStepIndex === $index ? 'bg-primary text-primary-foreground' : 'text-muted-foreground' }}">
                            {{ $index + 1 }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[11px] font-bold truncate uppercase">
                                {{ ($command['controller'] ?? '') ?: 'Undefined' }}
                            </p>
                            <p class="text-[9px] text-muted-foreground truncate">{{ ($command['action'] ?? '') ?: 'No action' }}
                            </p>
                        </div>
                        <div class="text-[10px] font-mono text-muted-foreground bg-muted/50 px-1 rounded">
                            {{ $command['delay'] ?? 5 }}s
                        </div>
                        <button wire:click.stop="removeCommand({{ $index }})"
                            class="opacity-0 group-hover:opacity-100 p-1 hover:text-destructive transition-opacity">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 6h18" />
                                <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                                <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                            </svg>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Center Panel: Step Editor -->
        <div class="col-span-9 bg-card rounded-xl border flex flex-col overflow-hidden shadow-sm">
            <div class="p-3 border-b bg-muted/20">
                <h3 class="text-xs font-bold uppercase tracking-wider text-muted-foreground">Step Editor - Step
                    {{ $selectedStepIndex + 1 }}
                </h3>
            </div>
            <div class="flex-1 overflow-y-auto p-6">
                @if(isset($commands[$selectedStepIndex]))
                    <div class="space-y-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-[10px] uppercase font-bold text-muted-foreground">Device /
                                    Controller</label>
                                <select wire:model.live="commands.{{ $selectedStepIndex }}.controller"
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
                                    <option value="">Select Device...</option>
                                    <option value="tec">TEC</option>
                                    <option value="stirrer">Stirrer</option>
                                    <option value="microvalve">Microvalve</option>
                                    <option value="pump">Pump</option>
                                </select>
                                @error("commands.{$selectedStepIndex}.controller") <span
                                class="text-destructive text-[10px]">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-1.5">
                                <label class="text-[10px] uppercase font-bold text-muted-foreground">Action /
                                    Command</label>
                                <select wire:model.live="commands.{{ $selectedStepIndex }}.action"
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
                                    <option value="">Select Action...</option>
                                    @if($commands[$selectedStepIndex]['controller'])
                                        @foreach($controllerCommands[$commands[$selectedStepIndex]['controller']] ?? [] as $cmdPath => $type)
                                            <option value="{{ $cmdPath }}">{{ Str::headline($cmdPath) }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @error("commands.{$selectedStepIndex}.action") <span
                                class="text-destructive text-[10px]">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="p-4 bg-muted/20 rounded-lg border border-dashed text-center">
                            <span class="text-[9px] uppercase font-bold text-muted-foreground">MQTT Topic</span>
                            <code
                                class="block mt-1 text-xs text-primary">adc/controller/{{ ($commands[$selectedStepIndex]['controller'] ?? '') ?: 'device' }}/{{ ($commands[$selectedStepIndex]['action'] ?? '') ?: 'command' }}</code>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[10px] uppercase font-bold text-muted-foreground">
                                Payload / Value <span
                                    class="text-[9px] lowercase italic opacity-70">({{ $commands[$selectedStepIndex]['type'] ?? 'string' }})</span>
                            </label>
                            @if(($commands[$selectedStepIndex]['type'] ?? 'string') === 'bool')
                                <div class="flex items-center h-10 gap-3">
                                    <input type="checkbox" wire:model="commands.{{ $selectedStepIndex }}.value"
                                        class="h-4 w-4 rounded border-gray-300" />
                                    <span class="text-sm text-muted-foreground">Enabled</span>
                                </div>
                            @elseif(in_array(($commands[$selectedStepIndex]['type'] ?? 'string'), ['int', 'float']))
                                <mijnui:input type="number" wire:model="commands.{{ $selectedStepIndex }}.value"
                                    step="{{ ($commands[$selectedStepIndex]['type'] ?? 'string') === 'float' ? '0.01' : '1' }}" />
                            @else
                                <mijnui:input type="text" wire:model="commands.{{ $selectedStepIndex }}.value"
                                    placeholder="Enter value..." />
                            @endif
                        </div>

                        <div class="h-px bg-border my-2"></div>

                        <div class="grid grid-cols-3 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-[10px] uppercase font-bold text-muted-foreground">Delay (s)</label>
                                <mijnui:input type="number" wire:model="commands.{{ $selectedStepIndex }}.delay" />
                            </div>
                            <div class="space-y-1.5 max-w-[100px]">
                                <label class="text-[10px] uppercase font-bold text-muted-foreground">Retry Count</label>
                                <mijnui:input type="number" wire:model="commands.{{ $selectedStepIndex }}.retry_count" />
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[10px] uppercase font-bold text-muted-foreground">Timeout (s)</label>
                                <mijnui:input type="number" wire:model="commands.{{ $selectedStepIndex }}.timeout" />
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[10px] uppercase font-bold text-muted-foreground">Description</label>
                            <mijnui:textarea wire:model="description" placeholder="Brief notes for this preset..."
                                class="h-24" />
                            @error('description') <span class="text-destructive text-[10px]">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                @else
                    <div class="h-full flex flex-col items-center justify-center text-muted-foreground space-y-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"
                            class="opacity-20">
                            <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z" />
                            <polyline points="14 2 14 8 20 8" />
                        </svg>
                        <p class="text-sm">Select a step from the list to edit its configuration</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
</div>