@php
    $info_cards = [
        [
            'title' => 'Total Users',
            'value' => $totalUser ?? 0,
            'icon' => 'fa-solid fa-users',
        ],
        [
            'title' => 'Total Roles',
            'value' => $totalRole ?? 0,
            'icon' => 'fa-solid fa-layer-group',
        ],
        [
            'title' => 'Total Devices',
            'value' => 0,
            'icon' => 'fa-regular fa-hard-drive',
        ],
        [
            'title' => 'Total Protocols',
            'value' => 0,
            'icon' => 'fa-solid fa-code-pull-request',
        ],
    ];
@endphp

<div class="space-y-4">

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach ($info_cards as $card)
            <mijnui:card>
                <mijnui:card.header>
                    <span
                        class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-foreground text-background text-lg">
                        <i class="{{ $card['icon'] }}"></i>
                    </span>
                </mijnui:card.header>
                <mijnui:card.content>
                    <p class="text-sm font-medium text-foreground">{{ $card['title'] }}</p>
                    <h3 class="text-2xl font-semibold text-foreground">{{ $card['value'] }}</h3>
                </mijnui:card.content>
            </mijnui:card>
        @endforeach
    </div>

    {{-- Current Device Select --}}
    <div>
        <mijnui:card>
            <mijnui:card.content>
                <div class="flex items-center gap-4">
                    <p class="font-medium ">Current Device</p>
                    <mijnui:select class="w-64" wire:model="selectedDevice" placeholder="Select a device">
                        <mijnui:select.option value="device1">Device 1</mijnui:select.option>
                        <mijnui:select.option value="device2">Device 2</mijnui:select.option>
                    </mijnui:select>
                </div>
            </mijnui:card.content>
        </mijnui:card>
    </div>

    {{-- Charts Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        {{-- Chamber Volume List --}}
        <mijnui:card>
            <mijnui:card.header>
                <mijnui:card.title class="text-lg font-semibold text-foreground">Chamber Volume List</mijnui:card.title>
            </mijnui:card.header>
            <mijnui:card.content>
                <livewire:charts.chamber-bar-chart />
            </mijnui:card.content>
        </mijnui:card>

        {{-- Daily Protocol Monitoring --}}
        <mijnui:card>
            <mijnui:card.header>
                <mijnui:card.title class="text-lg font-semibold text-foreground">Daily Protocol Monitoring
                </mijnui:card.title>
            </mijnui:card.header>
            <mijnui:card.content>
                <livewire:charts.daily-protocol-line-chart />
            </mijnui:card.content>
        </mijnui:card>

        {{-- Daily Volume Usage (with Select) --}}
        <mijnui:card>
            <mijnui:card.header>
                <p class="text-lg font-semibold text-foreground">Daily Volume Usage</p>
                <mijnui:select class="w-48" wire:model="selectedChamber" placeholder="Select a chamber">
                    <mijnui:select.option value="Chamber1">Chamber 1</mijnui:select.option>
                    <mijnui:select.option value="Chamber2">Chamber 2</mijnui:select.option>
                </mijnui:select>
            </mijnui:card.header>
            <mijnui:card.content>
                <livewire:charts.daily-chamber-volume-usage-chart />
            </mijnui:card.content>
        </mijnui:card>

        {{-- Sample Pie Chart --}}
        <mijnui:card class="h-96">
            <mijnui:card.header>
                <mijnui:card.title class="text-lg font-semibold text-foreground">Sample Pie Chart</mijnui:card.title>
            </mijnui:card.header>
            <mijnui:card.content>
                <livewire:charts.sample-pie-chart />
            </mijnui:card.content>
        </mijnui:card>
    </div>

    <div>
        <livewire:device-components.log />
    </div>

</div>