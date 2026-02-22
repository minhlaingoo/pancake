<div>

    @if($logs->count() > 0)
        <mijnui:table>
            <div wire:poll.visible.1s="checkForUpdate"></div>
            <mijnui:table.columns>
                <mijnui:table.column>Log</mijnui:table.column>
                <mijnui:table.column>Device Name</mijnui:table.column>
                <mijnui:table.column>Device Component</mijnui:table.column>
                <mijnui:table.column>Logged At</mijnui:table.column>
                <mijnui:table.column>Action</mijnui:table.column>

            </mijnui:table.columns>

            <mijnui:table.rows>
                @foreach ($logs as $log)
                    <mijnui:table.row>
                        <mijnui:table.cell>{{ $log->value }}</mijnui:table.cell>
                        <mijnui:table.cell>{{ $log->deviceComponent->device->name }}</mijnui:table.cell>
                        <mijnui:table.cell>{{ $log->deviceComponent->name }}</mijnui:table.cell>
                        <mijnui:table.cell>
                            {{-- <p x-data="{ time: '{{ $log->created_at->toIso8601String() }}', display: '' }" x-init="const t = dayjs.utc(time);
                                                                                                    display = t.fromNow();
                                                                                                    setInterval(() => {
                                                                                                        display = t.fromNow();
                                                                                                    }, 1000);"
                                x-text="display"></p>
                            --}}
                            {{$log->created_at->diffForHumans()}}
                        </mijnui:table.cell>
                        <mijnui:table.cell>
                            <mijnui:button color="primary" size="xs">View</mijnui:button>
                        </mijnui:table.cell>
                    </mijnui:table.row>
                @endforeach

            </mijnui:table.rows>
        </mijnui:table>
    @else
        <p class="text-sm text-center">No Logs Found</p>
    @endif
</div>