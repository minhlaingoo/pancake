<section id="table" class="space-y-4">

    <div>

        <!-- Protocol Section -->
        <section class="mb-8">
            <div class="inline-flex items-end gap-4 my-4">
                <h2 class="text-2xl font-semibold ">Protocol List</h2>
                @if (checkPermission('protocol', 'create'))
                    <a href="{{ route('protocols.create') }}"
                        class="text-sm border-b-2 border-border border-dotted hover:border-solid leading-0"
                        wire:navigate>Custom Protocol</a>
                @endif
            </div>

            <div class="flex flex-wrap gap-6">

                @foreach ($predefinedProtocols as $protocol)
                    <mijnui:card class="max-w-96 overflow-hidden">
                        <img src="{{ $protocol['img_url'] }}" alt="{{ $protocol['name'] }}" class="object-cover aspect-2/1">

                        <mijnui:card.header>
                            <mijnui:card.title class="text-lg">{{ $protocol['title'] }}</mijnui:card.title>
                            <mijnui:card.description class="text-xs">
                                {{ $protocol['description'] }}
                            </mijnui:card.description>
                        </mijnui:card.header>

                        <mijnui:card.footer>
                            @if (checkPermission('protocol', 'create'))
                                <a wire:navigate href="{{ route('protocols.create') }}">
                                    <mijnui:button class="w-full">Set Protocol</mijnui:button>
                                </a>
                            @endif
                        </mijnui:card.footer>
                    </mijnui:card>
                @endforeach

            </div>
        </section>

        <!-- Phase Library Section -->
        <section>
            <h2 class="text-2xl font-semibold my-4">Phase Library</h2>

            <div class="flex gap-4">
                <a wire:navigate href="{{ route('phase.initialization-cycle-setup') }}">
                    <mijnui:button color="primary" class="ring-1 hover:ring-2 ring-blue-600 hover:bg-blue-500">
                        Initialization Procedure</mijnui:button>
                </a>
                <a wire:navigate href="{{ route('phase.storage-cycle-setup') }}">
                    <mijnui:button color="primary" class="ring-1 hover:ring-2 ring-blue-600 hover:bg-blue-500">System
                        Storage</mijnui:button>
                </a>
                <a wire:navigate href="{{ route('phase.system-cleaning-setup') }}">
                    <mijnui:button color="primary" class="ring-1 hover:ring-2 ring-blue-600 hover:bg-blue-500">System
                        Clean
                    </mijnui:button>
                </a>
                {{-- <mijnui:button color="primary" class="ring-1 hover:ring-2 ring-blue-600 hover:bg-blue-500">Column
                    Performance</mijnui:button> --}}
            </div>
        </section>

    </div>

    @if(count($protocols))
        <div>
            <mijnui:table :paginate="$protocols" class="overflow-none">

                <mijnui:table.columns>
                    <mijnui:table.column>Sample Id</mijnui:table.column>
                    <mijnui:table.column>Phases</mijnui:table.column>
                    {{-- @if (checkPermission('protocol', 'update') || checkPermission('protocol', 'delete')) --}}
                    <mijnui:table.column>Action</mijnui:table.column>
                    {{-- @endif --}}
                </mijnui:table.columns>

                <mijnui:table.rows>
                    @foreach ($protocols as $protocol)
                        <mijnui:table.row>
                            <mijnui:table.cell>
                                {{ $protocol->sample_id }}
                            </mijnui:table.cell>
                            <mijnui:table.cell>
                                <mijnui:badge>
                                    {{ count($protocol->phases) }}
                                </mijnui:badge>
                            </mijnui:table.cell>
                            {{-- @if (checkPermission('protocol', 'update') || checkPermission('protocol', 'delete')) --}}
                            <mijnui:table.cell class="flex items-center text-start gap-2">

                                <a wire:navigate href="{{ route('protocols.edit', ['sample_id' => $protocol->sample_id]) }}">
                                    <mijnui:button>
                                        Edit
                                    </mijnui:button>
                                </a>
                                <a wire:navigate
                                    href="{{ route('protocols.final-lab', ['sample_id' => $protocol->sample_id]) }}">
                                    <mijnui:button>
                                        Final Lab
                                    </mijnui:button>
                                </a>
                                <mijnui:button wire:click="createProcess({{$protocol->id}})">
                                    Create Process
                                </mijnui:button>
                                <mijnui:button color="danger"
                                    x-on:click="if(confirm('Are you sure to delete')) $wire.call('delete',{{ $protocol->id }})">
                                    Delete
                                </mijnui:button>

                            </mijnui:table.cell>
                        </mijnui:table.row>
                    @endforeach
                </mijnui:table.rows>
            </mijnui:table>
        </div>
    @else
        <div class="text-center py-10 text-gray-500">
            No Protocols Found.
        </div>
    @endif

    <x-alert />

</section>