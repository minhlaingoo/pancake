<div>
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <mijnui:breadcrumbs>
                <mijnui:breadcrumbs.item wire:navigate href="{{ route('users.index') }}">Users</mijnui:breadcrumbs.item>
                <mijnui:breadcrumbs.item isLast>Edit</mijnui:breadcrumbs.item>
            </mijnui:breadcrumbs>
            <h2 class="text-2xl font-semibold mb-4">User Edit Form</h2>
        </div>
    </div>


    <!-- Buttons Section -->
    <mijnui:card>
        <mijnui:card.content>

            <form wire:submit.prevent="update" >

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <mijnui:input wire:model="name" icon="fa-solid fa-user" placeholder="e.g. John" label="Your Name" />
                    <mijnui:select wire:model="role_id" label="Role">
                        @foreach ($roles as $role)
                            <mijnui:select.option value="{{ $role->id }}">{{ $role->name }}</mijnui:select.option>
                        @endforeach
                    </mijnui:select>
                    <div class="md:col-span-2">
                        <mijnui:input type="email" wire:model="email" placeholder="user@example.com" label="Email" />
                    </div>
                    <mijnui:input wire:model="password" viewable placeholder="********" label="Password"
                        type="password" />
                    <mijnui:input wire:model="password_confirmation" viewable placeholder="********"
                        label="Confirm Password" type="password" />
                </div>
                <div>
                    <mijnui:checkbox wire:model="is_active" label="Is Active" class="mt-4"/>
                </div>
                
                <mijnui:button color="primary" class="mt-3" has-loading>Update</mijnui:button>
            </form>
        </mijnui:card.content>
    </mijnui:card>

</div>
