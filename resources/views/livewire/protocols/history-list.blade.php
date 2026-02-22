<!-- Table -->
<section id="table" class="">
    <div class="flex justify-between items-center mb-4">
        <div>
            <mijnui:breadcrumbs>
                <mijnui:breadcrumbs.item isLast>Protocol_processes</mijnui:breadcrumbs.item>
            </mijnui:breadcrumbs>
            <h2 class="text-2xl font-semibold">Protocol Process & Permission Table</h2>
        </div>
        @if (checkPermission('protocol_process', 'create'))
            <a href="{{ route('protocol_process-permissions.create') }}" wire:navigate>
                <mijnui:button color="primary">Create
                </mijnui:button>
            </a>
        @endif
    </div>
    <x-alert />
    <div>
        @if(count($protocol_processes))
            <mijnui:table :paginate="$protocol_processes">

                <mijnui:table.columns>
                    <mijnui:table.column>Process Id</mijnui:table.column>
                    <mijnui:table.column>Status</mijnui:table.column>
                    {{-- @if (checkPermission('protocol_process', 'update') || checkPermission('protocol_process',
                    'delete')) --}}
                    <mijnui:table.column>Action</mijnui:table.column>
                    {{-- @endif --}}
                </mijnui:table.columns>

                <mijnui:table.rows>
                    @foreach ($protocol_processes as $protocol_process)
                                <mijnui:table.row class="cursor-pointer">
                                    <mijnui:table.cell>
                                        {{ $protocol_process->uid }}
                                    </mijnui:table.cell>
                                    <mijnui:table.cell>
                                        @if ($protocol_process->ended_at)
                                            <mijnui:badge>Finished</mijnui:badge>
                                        @else
                                            <mijnui:badge>Processing</mijnui:badge>
                                        @endif
                                    </mijnui:table.cell>
                                    {{-- @if (checkPermission('protocol_process', 'update') || checkPermission('protocol_process',
                                    'delete')) --}}
                                    <mijnui:table.cell class="flex items-center text-start">

                                        <mijnui:button wire:navigate href="{{ route('protocols.processing', [
                            'protocol' => $protocol_process->protocol->id,
                            'uid' => $protocol_process->uid,
                        ]) }}">
                                            View
                                        </mijnui:button>

                                    </mijnui:table.cell>
                                </mijnui:table.row>
                    @endforeach
                </mijnui:table.rows>
            </mijnui:table>
        @else
            <p class="text-gray-600">No Protocol Process is here</p>
        @endif
    </div>
</section>