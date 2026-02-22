<div class="grid grid-cols-2 gap-4 text-sm text-foreground mb-4">
    <div class="flex flex-col gap-1">
        <mijnui:label class="text-[10px] uppercase">Duration (s)</mijnui:label>
        <mijnui:input type="number" wire:model="phases.{{ $index }}.duration" class="h-8" />
    </div>
    <div class="flex flex-col gap-1">
        <mijnui:label class="text-[10px] uppercase">Loop</mijnui:label>
        <mijnui:input type="number" wire:model="phases.{{ $index }}.loop" class="h-8" />
    </div>
</div>

<div class="mt-4 pt-4 border-t">
    <div class="flex justify-between items-center mb-2">
        <h4 class="font-medium text-sm">Commands</h4>
        <mijnui:button wire:click="addCommand({{ $index }})" size="sm" variant="outline">
            + Add Command
        </mijnui:button>
    </div>

    <div class="space-y-3">
        @foreach($phase['commands'] ?? [] as $cmdIndex => $command)
            <div
                class="flex items-center gap-2 group relative bg-muted/30 p-2 rounded-md border border-transparent hover:border-border transition-colors">
                <div class="flex-1 grid grid-cols-12 gap-4">
                    <div class="col-span-4">
                        <label
                            class="text-[10px] font-semibold uppercase text-muted-foreground mb-1 block">Controller</label>
                        <select wire:model.live="phases.{{ $index }}.commands.{{ $cmdIndex }}.controller"
                            class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50">
                            <option value="">Select Controller...</option>
                            <option value="tec">TEC</option>
                            <option value="stirrer">Stirrer</option>
                            <option value="microvalve">Microvalve</option>
                            <option value="pump">Pump</option>
                        </select>
                    </div>
                    <div class="col-span-4">
                        <label class="text-[10px] font-semibold uppercase text-muted-foreground mb-1 block">Action</label>
                        <select wire:model.live="phases.{{ $index }}.commands.{{ $cmdIndex }}.action"
                            class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50">
                            <option value="">Select Action...</option>
                            @if($command['controller'])
                                @foreach($controllerCommands[$command['controller']] ?? [] as $cmdPath => $defaultType)
                                    <option value="{{ $cmdPath }}">{{ Str::headline($cmdPath) }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-span-4 flex items-end gap-2">
                        <div class="flex-1">
                            <label class="text-[10px] font-semibold uppercase text-muted-foreground mb-1 block">
                                Value <span
                                    class="text-[9px] lowercase italic opacity-70">({{ $command['type'] ?? 'string' }})</span>
                            </label>

                            @if(($command['type'] ?? 'string') === 'bool')
                                <div class="flex items-center h-9">
                                    <input type="checkbox" wire:model="phases.{{ $index }}.commands.{{ $cmdIndex }}.value"
                                        class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary" />
                                    <span class="ml-2 text-xs text-muted-foreground">Enabled</span>
                                </div>
                            @elseif(in_array(($command['type'] ?? 'string'), ['int', 'float']))
                                <input type="number" wire:model="phases.{{ $index }}.commands.{{ $cmdIndex }}.value"
                                    step="{{ ($command['type'] ?? 'string') === 'float' ? '0.01' : '1' }}"
                                    placeholder="{{ ($command['type'] ?? 'string') === 'float' ? '0.00' : '0' }}"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50" />
                            @else
                                <input type="text" wire:model="phases.{{ $index }}.commands.{{ $cmdIndex }}.value"
                                    placeholder="Payload"
                                    class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50" />
                            @endif
                        </div>
                    </div>
                </div>
                <button wire:click="removeCommand({{ $index }}, {{ $cmdIndex }})"
                    class="h-9 w-9 flex items-center justify-center text-muted-foreground hover:text-destructive transition-colors rounded-md hover:bg-destructive/10"
                    title="Remove Command">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 6h18" />
                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                        <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                        <line x1="10" y1="11" x2="10" y2="17" />
                        <line x1="14" y1="11" x2="14" y2="17" />
                    </svg>
                </button>
            </div>
        @endforeach
    </div>
</div>