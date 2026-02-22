<div class="p-6 space-y-6 bg-slate-50 dark:bg-slate-950 min-h-screen">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">Instrument Control Center</h1>
            <p class="text-slate-500 dark:text-slate-400">High-precision MQTT hardware management</p>
        </div>
        <div class="flex space-x-2">
            @foreach($devices as $device)
                <button wire:click="$set('deviceId', {{ $device->id }})"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ $deviceId == $device->id ? 'bg-primary-600 text-white shadow-lg shadow-primary-500/30' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                    {{ $device->name }}
                </button>
            @endforeach
        </div>
    </div>

    @if($activeDevice)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            {{-- TEC Control Card --}}
            <div class="relative group">
                <div
                    class="absolute -inset-0.5 bg-linear-to-r from-blue-500 to-cyan-500 rounded-2xl blur opacity-25 group-hover:opacity-50 transition duration-1000 group-hover:duration-200">
                </div>
                <div
                    class="relative p-6 bg-white dark:bg-slate-900 ring-1 ring-slate-200 dark:ring-slate-800 rounded-2xl leading-none flex flex-col space-y-4">
                    <div class="flex justify-between items-start">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                                <i class="fa-solid fa-temperature-half text-blue-600"></i>
                            </div>
                            <h3 class="font-semibold text-slate-900 dark:text-white text-lg">Temperature (TEC)</h3>
                        </div>
                        <span
                            class="px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">Online</span>
                    </div>

                    {{-- Real-time Gauge --}}
                    <div x-data="{ 
                                    temp: {{ $activeDevice->deviceComponents->where('type', 'tec')->first()?->last_value ?? 0 }}, 
                                    status: '{{ $activeDevice->deviceComponents->where('type', 'tec')->first()?->status ?? 'unknown' }}'
                                }" x-init="
                                    @if(config('broadcasting.default') != 'log')
                                        Echo.channel('device.{{ $activeDevice->id }}')
                                            .listen('TelemetryUpdated', (e) => {
                                                if(e.deviceComponentId == {{ $activeDevice->deviceComponents->where('type', 'tec')->first()?->id ?? 0 }}) {
                                                    this.temp = e.value;
                                                    this.status = e.status;
                                                }
                                            });
                                    @endif
                                " class="flex flex-col items-center py-4">
                        <div class="text-5xl font-bold tracking-tighter text-slate-900 dark:text-white"
                            x-text="parseFloat(temp).toFixed(1) + '°C'"></div>
                        <div class="text-sm text-slate-500 mt-1 uppercase tracking-widest font-medium">Real-time Telemetry
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="space-y-2">
                            <label class="text-xs font-semibold text-slate-400 uppercase">Target Setpoint</label>
                            <div class="flex space-x-2">
                                <input type="number" wire:model="setpoint" step="0.1"
                                    class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                                <button wire:click="tecSetSetpoint"
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold shadow-lg shadow-blue-500/30 transition-all active:scale-95">Set</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Stirrer Control Card --}}
            <div class="relative group">
                <div
                    class="absolute -inset-0.5 bg-linear-to-r from-purple-500 to-indigo-500 rounded-2xl blur opacity-25 group-hover:opacity-50 transition duration-1000 group-hover:duration-200">
                </div>
                <div
                    class="relative p-6 bg-white dark:bg-slate-900 ring-1 ring-slate-200 dark:ring-slate-800 rounded-2xl leading-none flex flex-col space-y-4">
                    <div class="flex justify-between items-start">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-purple-50 dark:bg-purple-900/30 rounded-lg">
                                <i class="fa-solid fa-rotate text-purple-600"></i>
                            </div>
                            <h3 class="font-semibold text-slate-900 dark:text-white text-lg">Stirrer Control</h3>
                        </div>
                        <span
                            class="px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">Ready</span>
                    </div>

                    <div x-data="{ 
                                    rpm: {{ $activeDevice->deviceComponents->where('type', 'stirrer')->first()?->last_value ?? 0 }} 
                                }" x-init="
                                    @if(config('broadcasting.default') != 'log')
                                        Echo.channel('device.{{ $activeDevice->id }}')
                                            .listen('TelemetryUpdated', (e) => {
                                                if(e.deviceComponentId == {{ $activeDevice->deviceComponents->where('type', 'stirrer')->first()?->id ?? 0 }}) {
                                                    this.rpm = e.value;
                                                }
                                            });
                                    @endif
                                " class="flex flex-col items-center py-4">
                        <div class="text-5xl font-bold tracking-tighter text-slate-900 dark:text-white flex items-baseline">
                            <span x-text="rpm"></span>
                            <span class="text-xl ml-1 text-slate-400">RPM</span>
                        </div>
                        <div class="text-sm text-slate-500 mt-1 uppercase tracking-widest font-medium">Stirring Speed</div>
                    </div>

                    <div class="space-y-4">
                        <div class="space-y-2">
                            <label class="text-xs font-semibold text-slate-400 uppercase">Target Velocity</label>
                            <input type="range" wire:model="stirrerSpeed" min="0" max="1000"
                                class="w-full h-2 bg-slate-200 dark:bg-slate-800 rounded-lg appearance-none cursor-pointer accent-purple-600">
                            <div class="flex justify-between text-[10px] text-slate-400 font-bold uppercase">
                                <span>0 RPM</span>
                                <span>{{ $stirrerSpeed }} RPM</span>
                                <span>1000 RPM</span>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <button wire:click="stirrerStart"
                                class="w-full py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl font-semibold shadow-lg shadow-purple-500/30 transition-all active:scale-95">Pulse
                                Start</button>
                            <button wire:click="stirrerStop"
                                class="w-full py-2 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-xl font-semibold transition-all active:scale-95">Hard
                                Stop</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Device Health/Status --}}
            <div class="relative group lg:col-span-1">
                <div
                    class="absolute -inset-0.5 bg-linear-to-r from-emerald-500 to-teal-500 rounded-2xl blur opacity-25 group-hover:opacity-50 transition duration-1000 group-hover:duration-200">
                </div>
                <div
                    class="relative p-6 bg-white dark:bg-slate-900 ring-1 ring-slate-200 dark:ring-slate-800 rounded-2xl leading-none flex flex-col space-y-4 h-full">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-emerald-50 dark:bg-emerald-900/30 rounded-lg">
                            <i class="fa-solid fa-microchip text-emerald-600"></i>
                        </div>
                        <h3 class="font-semibold text-slate-900 dark:text-white text-lg">Hardware Network</h3>
                    </div>

                    <div class="flex-1 space-y-3 mt-4">
                        <div
                            class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-100 dark:border-slate-800">
                            <span class="text-sm font-medium text-slate-500">MAC Address</span>
                            <span
                                class="text-sm font-mono font-bold text-slate-900 dark:text-white">{{ $activeDevice->mac ?? 'N/A' }}</span>
                        </div>
                        <div
                            class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-100 dark:border-slate-800">
                            <span class="text-sm font-medium text-slate-500">IP Endpoint</span>
                            <span
                                class="text-sm font-mono font-bold text-slate-900 dark:text-white">{{ $activeDevice->ip ?? '0.0.0.0' }}</span>
                        </div>
                        <div
                            class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-100 dark:border-slate-800">
                            <span class="text-sm font-medium text-slate-500">MQTT Root</span>
                            <span
                                class="text-sm font-mono font-bold text-slate-900 dark:text-white">{{ $activeDevice->topic ?? 'adc/' }}</span>
                        </div>
                    </div>

                    <div class="pt-4 mt-auto">
                        <div class="flex items-center space-x-2 text-emerald-500">
                            <div class="w-3 h-3 bg-emerald-500 rounded-full animate-pulse"></div>
                            <span class="text-xs font-bold uppercase tracking-widest">Active Connection</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    @else
        <div
            class="flex flex-col items-center justify-center p-20 bg-white dark:bg-slate-900 rounded-3xl border-2 border-dashed border-slate-200 dark:border-slate-800">
            <i class="fa-solid fa-ghost text-6xl text-slate-300 dark:text-slate-700 mb-4"></i>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white">No Devices Registered</h2>
            <p class="text-slate-500">Connect a controller to begin telemetry ingestion.</p>
        </div>
    @endif
</div>