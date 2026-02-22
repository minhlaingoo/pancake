<!-- Table -->
<section id="table" class="">
    <x-alert />
    <div class="flex justify-between items-center mb-4">
        <div>
            <mijnui:breadcrumbs>
                <mijnui:breadcrumbs.item isLast>Users</mijnui:breadcrumbs.item>
            </mijnui:breadcrumbs>
            <h2 class="text-2xl font-semibold">User Table</h2>
        </div>

        @if (checkPermission('user', 'create'))
            <a href="{{ route('users.create') }}" wire:navigate>
                <mijnui:button color="primary">
                    Create
                </mijnui:button>
            </a>
        @endif
    </div>

    <div class="w-full overflow-x-auto">
        <mijnui:table>
            <mijnui:table.columns>
                <mijnui:table.column class="w-32">Name</mijnui:table.column>
                <mijnui:table.column class="w-32">Role</mijnui:table.column>
                <mijnui:table.column class="w-48">Email</mijnui:table.column>
                <mijnui:table.column class="w-48">Joined</mijnui:table.column>
                <mijnui:table.column class="w-24">Active</mijnui:table.column>
                @if (checkPermission('user', 'update') || checkPermission('user', 'delete'))
                    <mijnui:table.column class="w-24">Action</mijnui:table.column>
                @endif
            </mijnui:table.columns>

            <mijnui:table.rows>
                @foreach ($users as $user)
                    <mijnui:table.row>
                        <mijnui:table.cell>
                            {{ $user->name }}
                        </mijnui:table.cell>
                        <mijnui:table.cell>
                            {{ $user->role->name ?? '' }}
                        </mijnui:table.cell>
                        <mijnui:table.cell>
                            {{ $user->email }}
                        </mijnui:table.cell>
                        <mijnui:table.cell>
                            {{ $user->created_at->format('d M Y H:i') }}
                        </mijnui:table.cell>
                        <mijnui:table.cell>
                            <mijnui:badge color="{{ $user->is_active ? 'success' : 'danger' }}">
                                {{ $user->is_active ? 'active' : 'inactive' }}
                            </mijnui:badge>
                        </mijnui:table.cell>
                        @if (checkPermission('user', 'update') || checkPermission('user', 'delete'))
                            <mijnui:table.cell>
                                <mijnui:dropdown teleport>
                                    <mijnui:dropdown.trigger>
                                        <mijnui:button>
                                            Action
                                        </mijnui:button>
                                    </mijnui:dropdown.trigger>
                                    <mijnui:dropdown.content>
                                        @if (checkPermission('user', 'update'))
                                            <mijnui:dropdown.item wire:navigate
                                                href="{{ route('users.edit', ['id' => $user->id]) }}">
                                                Edit
                                            </mijnui:dropdown.item>
                                        @endif
                                        @if (checkPermission('user', 'delete'))
                                            <mijnui:dropdown.item
                                                x-on:click="if(confirm('Are you sure to delete')) $wire.call('delete', {{ $user->id }})">
                                                Delete
                                            </mijnui:dropdown.item>
                                        @endif
                                    </mijnui:dropdown.content>
                                </mijnui:dropdown>
                            </mijnui:table.cell>
                        @endif
                    </mijnui:table.row>
                @endforeach
            </mijnui:table.rows>
        </mijnui:table>
    </div>
</section>