<div>

    <div>
        <mijnui:breadcrumbs>
            <mijnui:breadcrumbs.item isLast>Device Log</mijnui:breadcrumbs.item>
        </mijnui:breadcrumbs>
        <h2 class="text-2xl font-semibold">Device Log</h2>
    </div>

    @if (count($logs))


        <mijnui:table class="table-fixed">

            <mijnui:table.columns>

                <mijnui:table.column class="w-4">No.</mijnui:table.column>
                <mijnui:table.column class="w-48">Log</mijnui:table.column>
                <mijnui:table.column class="w-24">Device Name</mijnui:table.column>
                <mijnui:table.column class="w-24">Device Component Name</mijnui:table.column>
                <mijnui:table.column class="w-24">Logged at</mijnui:table.column>
            </mijnui:table.columns>

            <mijnui:table.rows>

                @foreach ($logs as $index => $log)
                    <mijnui:table.row>
                        <mijnui:table.cell>{{ $index + 1 }}</mijnui:table.cell>
                        <mijnui:table.cell>{{$log->value}}</mijnui:table.cell>
                        <mijnui:table.cell>{{$log->deviceComponent->device->name}}</mijnui:table.cell>
                        <mijnui:table.cell>{{$log->deviceComponent->name}}</mijnui:table.cell>
                        <mijnui:table.cell>{{$log->created_at->diffForHumans()}}</mijnui:table.cell>
                    </mijnui:table.row>
                @endforeach

            </mijnui:table.rows>

        </mijnui:table>

    @else
        <p class="text-sm text-center mt-4 text-muted-foreground">No logs found for this device.</p>
    @endif
</div>