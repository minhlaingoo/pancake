<div>
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <mijnui:breadcrumbs>
                <mijnui:breadcrumbs.item wire:navigate href="{{ route('users.index') }}">Users</mijnui:breadcrumbs.item>
                <mijnui:breadcrumbs.item isLast>Create</mijnui:breadcrumbs.item>
            </mijnui:breadcrumbs>
            <h2 class="text-2xl font-semibold mb-4">User Create Form</h2>
        </div>
    </div>

    <!-- Buttons Section -->
    <mijnui:card>
        <mijnui:card.content class="pt-4">
            <form wire:submit.prevent="store">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <mijnui:input required wire:model="name" icon="fa-solid fa-user" placeholder="e.g. John"
                        label="Your Name" />
                    <mijnui:select wire:model="role_id" label="Role" required>
                        @foreach ($roles as $role)
                            @if ($role->id != 1)
                                <mijnui:select.option value="{{ $role->id }}">{{ $role->name }}
                                </mijnui:select.option>
                            @endif
                        @endforeach
                    </mijnui:select>
                    <div class="md:col-span-2">
                        <mijnui:input required wire:model="email" placeholder="user@example.com" label="Email" />
                    </div>
                    <mijnui:input type="password" viewable required wire:model="password" placeholder="********"
                        label="Password" />
                    <mijnui:input type="password" viewable required wire:model="password_confirmation"
                        placeholder="********" label="Confirm Password" />
                </div>
                <div>
                    <mijnui:checkbox wire:model="is_active" label="Is Active" class="mt-4" />
                </div>

                <mijnui:button type="submit" color="primary" class="mt-3" has-loading>Create</mijnui:button>
            </form>
        </mijnui:card.content>
    </mijnui:card>

</div>
