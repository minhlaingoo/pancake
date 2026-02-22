<div class="space-y-4">
    <div>
        <mijnui:breadcrumbs>
            <mijnui:breadcrumbs.item isLast>User Setting</mijnui:breadcrumbs.item>
        </mijnui:breadcrumbs>
        <h2 class="text-2xl font-semibold">User Setting</h2>
    </div>

    <mijnui:card>
        <x-alert />
        <mijnui:card.content class="pt-4">
            <form wire:submit.prevent="updateUserSetting" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <mijnui:input label="Username" type="text" wire:model="name" />
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <mijnui:input label="New Password (optional)" type="password" wire:model="password" viewable />
                        @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <mijnui:input label="Confirm New Password" type="password" wire:model="password_confirmation"
                            viewable />
                        @error('password_confirmation')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <mijnui:button color="primary" has-loading>Update Settings</mijnui:button>
            </form>
        </mijnui:card.content>
    </mijnui:card>
</div>