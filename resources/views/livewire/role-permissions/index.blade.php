<!-- Table -->
<section id="table" class="">
    <div class="flex justify-between items-center mb-4">
        <div>
            <mijnui:breadcrumbs>
                <mijnui:breadcrumbs.item isLast>Roles</mijnui:breadcrumbs.item>
            </mijnui:breadcrumbs>
            <h2 class="text-2xl font-semibold">Role & Permission Table</h2>
        </div>
        @if (checkPermission('role', 'create'))
            <a href="{{ route('role-permissions.create') }}" wire:navigate>
                <mijnui:button color="primary">Create
                </mijnui:button>
            </a>
        @endif
    </div>
    <x-alert />
    <div>
        <mijnui:table :paginate="$roles">

            <mijnui:table.columns>
                <mijnui:table.column>Name</mijnui:table.column>
                @if (checkPermission('role', 'update') || checkPermission('role', 'delete'))
                    <mijnui:table.column>Action</mijnui:table.column>
                @endif
            </mijnui:table.columns>

            <mijnui:table.rows>
                @foreach ($roles as $role)
                    <mijnui:table.row>
                        <mijnui:table.cell>
                            {{ $role->name }}
                        </mijnui:table.cell>
                        @if (checkPermission('role', 'update') || checkPermission('role', 'delete'))
                            <mijnui:table.cell class="flex items-center text-start">
                                @if ($role->id == 1)
                                    <mijnui:badge color="success">Default Role</mijnui:badge>
                                @else
                                    <mijnui:dropdown teleport>
                                        <mijnui:dropdown.trigger>
                                            <mijnui:button size="xs">
                                                Action
                                            </mijnui:button>
                                        </mijnui:dropdown.trigger>
                                        <mijnui:dropdown.content>
                                            @if (checkPermission('role', 'update'))
                                                <mijnui:dropdown.item wire:navigate
                                                    href="{{ route('role-permissions.edit', ['role' => $role->id]) }}">
                                                    Edit
                                                </mijnui:dropdown.item>
                                            @endif
                                            @if (checkPermission('role', 'delete'))
                                                <mijnui:dropdown.item
                                                    x-on:click="if(confirm('Are you sure to delete')) $wire.call('delete',{{ $role->id }})">
                                                    Delete
                                                </mijnui:dropdown.item>
                                            @endif
                                        </mijnui:dropdown.content>
                                    </mijnui:dropdown>
                                @endif
                            </mijnui:table.cell>
                        @endif
                    </mijnui:table.row>
                @endforeach
            </mijnui:table.rows>
        </mijnui:table>
    </div>
</section>