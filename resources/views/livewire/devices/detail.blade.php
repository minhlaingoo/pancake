<div class="space-y-2">
    <x-alert />
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
        <mijnui:card class="col-span-1 md:col-span-2 lg:col-span-1">
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
                    <p class="w-24">Device Status</p>
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
                    <p class="w-24">Uptime:</p>
                    <p>ChemiLab</p>
                </div>
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
                    <p class="w-24">Battery Level:</p>
                    <p>86%</p>
                </div>
                <div class="flex items-center">
                    <p class="w-24">Status:</p>
                    <p>Charging</p>
                </div>
            </mijnui:card.content>
        </mijnui:card>
    </div>

    <mijnui:card class="border-2 border-primary/20 bg-primary/5">
        <mijnui:card.header>
            <div class="flex justify-between items-center w-full">
                <div class="flex items-center gap-2">
                    <div class="h-8 w-8 rounded-lg bg-primary/10 flex items-center justify-center">
                        <i class="fas fa-vial text-primary text-sm"></i>
                    </div>
                    <mijnui:card.title class="text-lg font-bold">Manual Command Testing</mijnui:card.title>
                </div>
                <mijnui:button wire:click="addTestCommand" size="sm" variant="outline">
                    + Add Command
                </mijnui:button>
            </div>
        </mijnui:card.header>
        <mijnui:card.content>
            @if(empty($testCommands))
                <div class="text-center py-6 border-2 border-dashed rounded-lg bg-background/50">
                    <p class="text-muted-foreground text-sm">No test commands added yet.</p>
                </div>
            @else
                <form wire:submit.prevent="sendTestCommands" class="space-y-3">
                    @foreach($testCommands as $index => $command)
                        <div
                            class="flex items-center gap-2 group relative bg-background p-3 rounded-lg border shadow-sm transition-all hover:border-primary/50">
                            <div class="flex-1 grid grid-cols-12 gap-4">
                                <div class="col-span-4">
                                    <label
                                        class="text-[10px] font-semibold uppercase text-muted-foreground mb-1 block">Controller</label>
                                    <select wire:model.live="testCommands.{{ $index }}.controller"
                                        class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                                        <option value="">Select Controller...</option>
                                        <option value="tec">TEC</option>
                                        <option value="stirrer">Stirrer</option>
                                        <option value="microvalve">Microvalve</option>
                                        <option value="pump">Pump</option>
                                    </select>
                                    @error("testCommands.{$index}.controller") <span class="text-[10px] text-destructive">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-span-4">
                                    <label
                                        class="text-[10px] font-semibold uppercase text-muted-foreground mb-1 block">Action</label>
                                    <select wire:model.live="testCommands.{{ $index }}.action"
                                        class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" required>
                                        <option value="">Select Action...</option>
                                        @if($command['controller'])
                                            @foreach($controllerCommands[$command['controller']] ?? [] as $cmdPath => $defaultType)
                                                <option value="{{ $cmdPath }}">{{ Str::headline($cmdPath) }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @error("testCommands.{$index}.action") <span class="text-[10px] text-destructive">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-span-4 flex items-end gap-2">
                                    <div class="flex-1">
                                        <label class="text-[10px] font-semibold uppercase text-muted-foreground mb-1 block">
                                            Value <span
                                                class="text-[9px] lowercase italic opacity-70">({{ $command['type'] ?? 'string' }})</span>
                                        </label>
                                        @if(($command['type'] ?? 'string') === 'bool')
                                            <div class="flex items-center h-9">
                                                <input type="checkbox" wire:model="testCommands.{{ $index }}.value"
                                                    class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary"
                                                    required />
                                                <span class="ml-2 text-xs text-muted-foreground">Enabled</span>
                                            </div>
                                        @elseif(($command['type'] ?? 'string') === 'valve_select')
                                            <select wire:model="testCommands.{{ $index }}.value"
                                                class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                                                required>
                                                <option value="">Select Valve...</option>
                                                @foreach(range(1, 7) as $valve)
                                                    <option value="{{ $valve }}">Valve {{ $valve }}</option>
                                                @endforeach
                                            </select>
                                        @elseif(in_array(($command['type'] ?? 'string'), ['int', 'float']))
                                            <input type="number" wire:model="testCommands.{{ $index }}.value"
                                                step="{{ ($command['type'] ?? 'string') === 'float' ? '0.01' : '1' }}"
                                                class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors" required />
                                        @else
                                            <input type="text" wire:model="testCommands.{{ $index }}.value" placeholder="Payload"
                                                class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm shadow-sm transition-colors" required />
                                        @endif
                                        @error("testCommands.{$index}.value") <span class="text-[10px] text-destructive">{{ $message }}</span> @enderror
                                    </div>
                                    <button wire:click="removeTestCommand({{ $index }})"
                                        class="h-9 w-9 flex items-center justify-center text-muted-foreground hover:text-destructive transition-colors rounded-md hover:bg-destructive/10">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="pt-4 flex justify-end">
                        <mijnui:button type="submit" color="primary" class="shadow-lg shadow-primary/20">
                            <i class="fas fa-paper-plane mr-2"></i> Fire Commands
                        </mijnui:button>
                    </div>
                </form>
@endif
        </mijnui:card.content>
    </mijnui:card>

    <hr>
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-xl font-semibold">Component List</h3>
        @if (checkPermission('device', 'create'))
            <a href="{{ route('devices.deviceComponents', ['id' => $device->id]) }}">
                <mijnui:button color="primary">
                    + Add Component
                </mijnui:button>
            </a>
        @endif
    </div>

    @if(count($deviceComponents))
        <div>
            <mijnui:table>

                <mijnui:table.columns>

                    <mijnui:table.column>ID.</mijnui:table.column>
                    <mijnui:table.column>Name</mijnui:table.column>
                    <mijnui:table.column>Component Type</mijnui:table.column>
                    <mijnui:table.column>Component Unit</mijnui:table.column>
                    @if (checkPermission('device', 'update'))
                        <mijnui:table.column>Action</mijnui:table.column>
                    @endif
                </mijnui:table.columns>

                <mijnui:table.rows>

                    @foreach ($deviceComponents as $index => $deviceComponent)
                        <mijnui:table.row>
                            <mijnui:table.cell>{{ $deviceComponent->id }}</mijnui:table.cell>
                            <mijnui:table.cell>{{ $deviceComponent->name }}</mijnui:table.cell>
                            <mijnui:table.cell>{{ $deviceComponent->type }}</mijnui:table.cell>
                            <mijnui:table.cell>{{ $deviceComponent->unit }}</mijnui:table.cell>
                            @if (checkPermission('device', 'update'))
                                <mijnui:table.cell>
                                    <div class="flex gap-1 items-center">
                                        <a
                                            href="{{ route('deviceComponents.edit', ['id' => $device->id, 'deviceComponent' => $deviceComponent->id]) }}">
                                            <mijnui:button color="primary">Edit</mijnui:button>
                                        </a>
                                        <div x-cloak x-data="{ open: false, component_id: null }">
                                            <!-- Component Delete Modal -->
                                            <div x-show="open" x-transition
                                                class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
                                                @click.self="open = false">
                                                <div class="bg-white dark:bg-zinc-800 rounded shadow-lg p-6 w-full max-w-md">
                                                    <h2 class="text-lg font-semibold mb-4">Delete Component</h2>
                                                    <p class="mb-6">Are you sure you want to delete this component?</p>
                                                    <div class="flex justify-end gap-2">
                                                        <mijnui:button type="button" outline @click="open = false">
                                                            Cancel
                                                        </mijnui:button>

                                                        <mijnui:button type="button" color="danger" has-loading
                                                            wire:click="deleteComponent(component_id)" wire:loading.attr="disabled"
                                                            wire:target="deleteComponent" x-on:component-delete="open=false">
                                                            Delete
                                                        </mijnui:button>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Trigger Button -->
                                            <mijnui:button color="danger"
                                                @click="open = true; component_id = {{ $deviceComponent->id }}">
                                                Delete
                                            </mijnui:button>
                                        </div>

                                    </div>
                                </mijnui:table.cell>
                            @endif
                        </mijnui:table.row>
                    @endforeach

                </mijnui:table.rows>

            </mijnui:table>
        </div>
    @else
        <div class="text-center py-8">
            <p class="text-gray-500">No components found for this device.</p>
        </div>
    @endif
</div>