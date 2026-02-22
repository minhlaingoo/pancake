<div class="space-y-4">

    <div>
        <mijnui:breadcrumbs>
            <mijnui:breadcrumbs.item isLast>Activity Logs</mijnui:breadcrumbs.item>
        </mijnui:breadcrumbs>
        <h2 class="text-2xl font-semibold">Activity Logs</h2>
    </div>

    @if(count($logs))
        <mijnui:table :paginate="$logs" class="table-fixed">

            <mijnui:table.columns>
                <mijnui:table.column class="w-28">Log Name</mijnui:table.column>
                <mijnui:table.column class="w-24">Event</mijnui:table.column>
                <mijnui:table.column class="w-48">Description</mijnui:table.column>
                <mijnui:table.column class="w-24">Status</mijnui:table.column>
                <mijnui:table.column class="w-48">Created At</mijnui:table.column>
                <mijnui:table.column class="w-28">Created By</mijnui:table.column>
                <mijnui:table.column class="w-24">Action</mijnui:table.column>

            </mijnui:table.columns>

            <mijnui:table.rows>
                @foreach ($logs as $log)
                    <mijnui:table.row>
                        <mijnui:table.cell>
                            {{ $log->log_name }}
                        </mijnui:table.cell>
                        <mijnui:table.cell>
                            {{ $log->event }}
                        </mijnui:table.cell>
                        <mijnui:table.cell>
                            {{ $log->description }}
                        </mijnui:table.cell>
                        <mijnui:table.cell>
                            <mijnui:badge size="xs"
                                color="{{ $log->status == 'success' ? 'success' : ($log->status == 'fail' ? 'danger' : 'warning') }}"
                                rounded="lg" outline>
                                {{ $log->status }}
                            </mijnui:badge>
                        </mijnui:table.cell>
                        <mijnui:table.cell>
                            {{ $log->created_at->format('d M Y H:i') }}
                        </mijnui:table.cell>
                        <mijnui:table.cell>
                            {{ $log->createdUser->name ?? 'unknown' }}
                        </mijnui:table.cell>

                        <mijnui:table.cell>
                            <a wire:navigate href="{{ route('activity-logs.detail', ['id' => $log->id]) }}">
                                <mijnui:button size="xs" color="primary">
                                    View
                                </mijnui:button>
                            </a>
                        </mijnui:table.cell>
                    </mijnui:table.row>
                @endforeach
            </mijnui:table.rows>
        </mijnui:table>
    @else
        <p class="text-gray-600">No Activity Log is here</p>
    @endif

</div>