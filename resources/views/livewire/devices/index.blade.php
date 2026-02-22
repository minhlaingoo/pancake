<!-- Table -->
<section id="table" class="">
    <x-alert />
    <div class="flex justify-between items-center mb-4">
        <div>
            <mijnui:breadcrumbs>
                <mijnui:breadcrumbs.item isLast>Devices</mijnui:breadcrumbs.item>
            </mijnui:breadcrumbs>
            <h2 class="text-2xl font-semibold">Devices Table</h2>

            @if(!setting('broker')->enable_log)
                <p class="text-xs text-danger">The logging feature is off, please turn on in
                    <a wire:navigate class="underline" href="{{ route('broker-setting') }}">
                        broker setting
                    </a>
                    for component logging
                </p>
            @endif
        </div>
        @if (checkPermission('user', 'create'))
            <a href="{{ route('devices.create') }}" wire:navigate>
                <mijnui:button color="primary">Create</mijnui:button>
            </a>
        @endif
    </div>

    <div>
        @if (count($devices))

            <mijnui:table class="table-fixed">

                <mijnui:table.columns>
                    <mijnui:table.column class="w-32">Name</mijnui:table.column>
                    <mijnui:table.column class="w-24">Model</mijnui:table.column>
                    <mijnui:table.column class="w-28">Topic</mijnui:table.column>
                    <mijnui:table.column class="w-24">Status</mijnui:table.column>
                    <mijnui:table.column class="w-48">Last Active</mijnui:table.column>
                    <mijnui:table.column class="w-32">Action</mijnui:table.column>
                </mijnui:table.columns>

                <mijnui:table.rows>
                    @foreach ($devices as $device)
                        <mijnui:table.row>
                            <mijnui:table.cell>{{ $device->name }}</mijnui:table.cell>
                            <mijnui:table.cell>{{ $device->model }}</mijnui:table.cell>
                            <mijnui:table.cell>{{ $device->topic ?? '-' }}</mijnui:table.cell>
                            <mijnui:table.cell>
                                <mijnui:badge color="{{ $device->is_active ? 'success' : 'danger' }}" size="xs">
                                    {{ $device->is_active ? 'Running' : 'Offline' }}
                                </mijnui:badge>
                            </mijnui:table.cell>
                            <mijnui:table.cell>{{ now()->subDay() }}</mijnui:table.cell>

                            <mijnui:table.cell>
                                <a href="{{ route('devices.detail', ['id' => $device->id]) }}" wire:navigate>
                                    <mijnui:button size="sm" color="primary">View Detail</mijnui:button>
                                </a>
                            </mijnui:table.cell>
                        </mijnui:table.row>
                    @endforeach

                </mijnui:table.rows>
            </mijnui:table>
        @else
            <p class="text-gray-600">No Device is here</p>
        @endif

        {{-- <div class="grid grid-cols-4 xl:grid-cols-6 gap-2">
            @forelse ($devices as $device)
            <div class="max-w-sm w-full bg-white rounded-xl shadow-lg shadow-black/30 hover:shadow-xl transition">
                <!-- Device Image -->
                <div class="h-48 w-full bg-gray-200 flex items-center justify-center">
                    <img src="https://img.freepik.com/free-vector/cute-monitor-playing-skateboard-cartoon-vector-icon-illustration-technology-sport-icon-isolated_138676-13494.jpg?semt=ais_hybrid&w=740"
                        alt="IoT Device" class="h-full w-full object-cover">
                </div>

                <!-- Card Content -->
                <div class="p-5">
                    <!-- Title and Status -->
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="text-xl font-bold text-gray-800">{{$device->name}}</h3>
                        <div class="flex items-center">
                            <span class="h-3 w-3 rounded-full bg-green-500 mr-2"></span>
                            <span class="text-sm font-medium text-gray-600">{{$device->is_active ? 'online' :
                                'offline'}}</span>
                        </div>
                    </div>

                    <!-- Last Online Time -->
                    <div class="mb-3">
                        <p class="text-gray-500 text-sm">Last online:</p>
                        <p class="text-gray-700 font-medium">May 22, 2025 - 4:15 PM</p>
                    </div>

                    <!-- MQTT Topic -->
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <p class="text-gray-500 text-sm mb-1">MQTT Topic:</p>
                        <p class="text-gray-800 font-mono text-sm break-all">{{$device->topic ?? "(not set yet!)"}}</p>
                    </div>

                    <!-- Action Button -->
                    <button
                        class="mt-4 w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg font-medium transition-colors duration-300">
                        View Details
                    </button>
                </div>
            </div>
            {{-- <div
                class="bg-white rounded-lg shadow-md p-3 flex max-w-xs w-full hover:shadow-lg transition-shadow duration-200">
                <!-- Device Image (Left) -->
                <div class="w-16 h-16 rounded-md overflow-hidden shrink-0">
                    <img src="https://img.freepik.com/free-vector/cute-monitor-playing-skateboard-cartoon-vector-icon-illustration-technology-sport-icon-isolated_138676-13494.jpg?semt=ais_hybrid&w=740"
                        alt="IoT Device" class="h-full w-full object-cover">
                </div>

                <!-- Device Info (Right) -->
                <div class="ml-3 flex-1 min-w-0">
                    <!-- Title and Status in one row -->
                    <div class="flex items-center justify-between mb-1">
                        <h3 class="font-bold text-gray-800 truncate">Temperature Component</h3>
                        <div class="flex items-center shrink-0">
                            <span class="h-2.5 w-2.5 rounded-full bg-green-500 mr-1"></span>
                            <span class="text-xs font-medium text-gray-600">Online</span>
                        </div>
                    </div>

                    <!-- Last Online Time -->
                    <div class="flex items-center text-xs text-gray-500 mb-1">
                        <span class="truncate">{{now()->format('h:m A d-M-Y')}}</span>
                    </div>

                    <!-- MQTT Topic -->
                    <div class="bg-gray-50 rounded px-2 py-1">
                        <p class="text-xs font-mono text-gray-700 truncate">Topic : {{$device->topic}}</p>
                    </div>
                </div>
            </div> --}}
            {{-- @empty
            <p>No Device is here</p>
            @endforelse
        </div> --}}

    </div>
</section>