<div class="space-y-4">
    <div>
        <mijnui:breadcrumbs>
            <mijnui:breadcrumbs.item isLast>Device Setting</mijnui:breadcrumbs.item>
        </mijnui:breadcrumbs>
        <h2 class="text-2xl font-semibold">Device Setting</h2>
    </div>
    <x-alert />
    <form wire:submit="update">
        <mijnui:card>
            <mijnui:card.content class="pt-4">
                <div class="space-y-4">
                    <div class="flex items-center gap-4">
                        <mijnui:label>Device Status</mijnui:label>
                        <mijnui:switch wire:model="is_active" />
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <mijnui:input wire:model="name" label="Device Name" placeholder="e.g. ChemLab" required />
                        <mijnui:input wire:model="model" label="Model Name" placeholder="e.g. IoT3201" required />
                        <mijnui:input wire:model="ip" label="Device Ip/Domain" placeholder="e.g. 163.21.62.133"
                            required />
                        <mijnui:input wire:model="port" type="number" label="Device Port" placeholder="e.g. 8080"
                            required />
                    </div>

                    <mijnui:button type="submit" color="primary" wire:target="update" has-loading>Update</mijnui:button>
                </div>
            </mijnui:card.content>

        </mijnui:card>
    </form>

    @if ($device->is_active)
        {{-- NTP Configuration --}}
        <mijnui:card>
            <mijnui:card.header>
                <mijnui:card.title class="text-lg font-semibold">NTP Configuration</mijnui:card.title>
            </mijnui:card.header>
            <mijnui:card.content>
                <form wire:submit="update" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <mijnui:input wire:model="ntp_server" label="NTP Server" placeholder="e.g. pool.ntp.org" />
                        <mijnui:input wire:model="timezone" label="Timezone" placeholder="e.g. Asia/Ho_Chi_Minh" />
                        <mijnui:input wire:model="ntp_interval" type="number" label="Sync Interval (seconds)"
                            placeholder="e.g. 3600" />
                    </div>
                    <div class="flex items-center gap-2">
                        <mijnui:button type="submit" color="primary" wire:target="update" has-loading>
                            Save NTP Settings
                        </mijnui:button>
                        <mijnui:button type="button" color="secondary" wire:click="syncNtp" wire:target="syncNtp"
                            has-loading>
                            Sync NTP Now
                        </mijnui:button>
                    </div>
                </form>
            </mijnui:card.content>
        </mijnui:card>

        {{-- Microvalve Configuration --}}
        <mijnui:card class="border-2 border-blue-200 bg-blue-50/50">
            <mijnui:card.header>
                <mijnui:card.title class="text-lg font-semibold flex items-center gap-2">
                    <i class="fas fa-valve text-blue-500"></i>
                    Microvalve Configuration
                </mijnui:card.title>
                <mijnui:card.description>
                    Configure the number and range of available microvalves for this device. 
                    <strong>Default:</strong> 6 microvalves starting from index 0 (Microvalves 0-5)
                </mijnui:card.description>
            </mijnui:card.header>
            <mijnui:card.content class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-semibold">Number of Microvalves</label>
                        <input 
                            type="number" 
                            wire:model.live="microvalveCount" 
                            min="1" 
                            max="16" 
                            class="w-full mt-1 px-3 py-2 border border-input rounded-md bg-background"
                            placeholder="6">
                        <p class="text-xs text-muted-foreground mt-1">Range: 1-16 microvalves (Default: 6)</p>
                    </div>
                    <div>
                        <label class="text-sm font-semibold">Starting Index</label>
                        <input 
                            type="number" 
                            wire:model.live="microvalveStart" 
                            min="0" 
                            max="15"
                            class="w-full mt-1 px-3 py-2 border border-input rounded-md bg-background"
                            placeholder="0">
                        <p class="text-xs text-muted-foreground mt-1">Range: 0-15 (Default: 0 for microvalves 0-5)</p>
                    </div>
                </div>

                <!-- Live Preview -->
                <div>
                    <label class="text-sm font-semibold">Available Microvalves (Live Preview)</label>
                    <div class="mt-1 p-3 bg-background border border-input rounded-md min-h-[50px]">
                        @if(empty($availableMicrovalves))
                            <span class="text-muted-foreground italic">No microvalves configured</span>
                        @else
                            <div class="flex flex-wrap gap-2">
                                @foreach($availableMicrovalves as $mv)
                                    <span class="inline-flex items-center px-2 py-1 rounded bg-green-100 text-green-800 text-sm">
                                        <i class="fas fa-valve mr-1"></i>
                                        {{ $mv }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <p class="text-xs text-muted-foreground mt-1">
                        @if(!empty($availableMicrovalves))
                            Range: {{ min($availableMicrovalves) }} to {{ max($availableMicrovalves) }} 
                            ({{ count($availableMicrovalves) }} total)
                        @endif
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-2 pt-2">
                    <mijnui:button wire:click="saveMicrovalveConfiguration" color="primary">
                        <i class="fas fa-save mr-2"></i>
                        Save Microvalve Config
                    </mijnui:button>
                    <mijnui:button wire:click="resetMicrovalvesToDefault" variant="outline">
                        <i class="fas fa-undo mr-2"></i>
                        Reset to Default
                    </mijnui:button>
                </div>
            </mijnui:card.content>
        </mijnui:card>

        {{-- Device Actions (MQTT Commands) --}}
        <mijnui:card>
            <mijnui:card.header>
                <mijnui:card.title class="text-lg font-semibold">Device Actions</mijnui:card.title>
            </mijnui:card.header>
            <mijnui:card.content>
                @if (empty($model))
                    <p class="text-sm text-neutral-500">
                        Set the <strong>Model Name</strong> above and save to enable device actions.
                    </p>
                @else
                    <div class="space-y-4">
                        {{-- TEC Enable / Disable --}}
                        <div
                            class="flex items-center justify-between rounded-lg border border-neutral-200 dark:border-neutral-700 p-4">
                            <div>
                                <p class="font-medium">TEC Controller</p>
                                <p class="text-sm text-neutral-500">Enable or disable the temperature controller.</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <mijnui:button size="sm" color="primary" wire:click="enableTec(true)" wire:target="enableTec"
                                    has-loading>
                                    Enable
                                </mijnui:button>
                                <mijnui:button size="sm" color="danger" wire:click="enableTec(false)" wire:target="enableTec"
                                    has-loading>
                                    Disable
                                </mijnui:button>
                            </div>
                        </div>

                        {{-- Pump Init --}}
                        <div
                            class="flex items-center justify-between rounded-lg border border-neutral-200 dark:border-neutral-700 p-4">
                            <div>
                                <p class="font-medium">Pump Initialization</p>
                                <p class="text-sm text-neutral-500">Initialize and home the syringe pump.</p>
                            </div>
                            <mijnui:button size="sm" color="secondary" wire:click="initPump(0)" wire:target="initPump"
                                has-loading>
                                Init Pump 0
                            </mijnui:button>
                        </div>

                        {{-- Stirrer Stop --}}
                        <div
                            class="flex items-center justify-between rounded-lg border border-neutral-200 dark:border-neutral-700 p-4">
                            <div>
                                <p class="font-medium">Stirrer Emergency Stop</p>
                                <p class="text-sm text-neutral-500">Immediately stop the stirrer motor.</p>
                            </div>
                            <mijnui:button size="sm" color="danger" wire:click="stopStirrer" wire:target="stopStirrer"
                                has-loading>
                                Stop Stirrer
                            </mijnui:button>
                        </div>
                    </div>
                @endif
            </mijnui:card.content>
        </mijnui:card>

        {{-- Manual MQTT Command --}}
        <mijnui:card>
            <mijnui:card.header>
                <mijnui:card.title class="text-lg font-semibold">Manual MQTT Command</mijnui:card.title>
            </mijnui:card.header>
            <mijnui:card.content>
                <form wire:submit="sendManualCommand" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <mijnui:input wire:model="manual_component" label="Component" placeholder="e.g. tec, pump_0"
                            required />
                        <mijnui:input wire:model="manual_action" label="Action" placeholder="e.g. enable, speed" required />
                        <mijnui:input wire:model="manual_payload" label="Payload" placeholder="e.g. 1, 500" />
                    </div>
                    <mijnui:button type="submit" color="secondary" wire:target="sendManualCommand" has-loading>
                        Send Manual Command
                    </mijnui:button>
                </form>
            </mijnui:card.content>
        </mijnui:card>

    @endif
</div>