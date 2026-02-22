<div>
    <x-alert />

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <mijnui:breadcrumbs>
                <mijnui:breadcrumbs.item wire:navigate href="{{ route('role-permissions.index') }}">Roles
                </mijnui:breadcrumbs.item>
                <mijnui:breadcrumbs.item isLast>Create</mijnui:breadcrumbs.item>
            </mijnui:breadcrumbs>
            <h2 class="text-2xl font-semibold mb-4">Role Create Form</h2>
        </div>
    </div>

    <mijnui:card>
        <form wire:submit.prevent="store">
            <mijnui:card.content>
                <mijnui:input placeholder="e.g. Assistant" label="Role Name" wire:model="role" required />
                <mijnui:separator />
                <div class="mt-3">
                    <mijnui:label>Permissions</mijnui:label>
                    <mijnui:error name="selected_permissions" />
                    <div class="w-full overflow-auto">

                        <table class="w-full table-fixed">
                            @foreach ($fPermissions as $fPermission)
                                <tr class=" hover:bg-gray-50 transition-colors duration-200">
                                    <!-- Permission Group Name -->
                                    <td class="py-4 font-semibold w-32">
                                        {{ ucwords($fPermission->name) }}
                                    </td>

                                    <!-- Permissions List -->
                                    <td class="py-4 pr-4">
                                        <div class="flex flex-wrap items-center gap-4">

                                            <mijnui:checkbox.group class="flex gap-8 text-sm">
                                                @foreach ($fPermission->permissions as $permission)
                                                    <div class="flex-shrink-0">
                                                        <mijnui:checkbox wire:model.live="selected_permissions"
                                                            id="{{ $fPermission->name }}" value="{{ $permission->id }}"
                                                            label="{{ $permission->name }}" />
                                                    </div>
                                                @endforeach
                                            </mijnui:checkbox.group>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </table>

                    </div>

                </div>

            </mijnui:card.content>
            <mijnui:card.footer>
                <mijnui:button type="submit" has-loading color="primary">Create</mijnui:button>
            </mijnui:card.footer>
        </form>
    </mijnui:card>
</div>