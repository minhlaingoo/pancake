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

    <div class="w-full overflow-x-auto">
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
                            <mijnui:table.cell>{{ $device->updated_at?->diffForHumans() ?? '-' }}</mijnui:table.cell>

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
            <p class="text-muted-foreground">No devices found.</p>
        @endif

    </div>
</section>