<div class="space-y-4">
    <div>
        <mijnui:breadcrumbs>
            <mijnui:breadcrumbs.item isLast>Account Settings</mijnui:breadcrumbs.item>
        </mijnui:breadcrumbs>
        <h2 class="text-2xl font-semibold">Account Settings</h2>
    </div>

    <mijnui:card>
        <x-alert />
        <mijnui:card.content class="pt-4">
            <form wire:submit.prevent="updateUserSetting" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <mijnui:input label="Username" type="text" wire:model="name" required maxlength="255"
                            placeholder="Your display name" />
                        @error('name')
                            <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <mijnui:input label="New Password" type="password" wire:model="password" viewable
                            placeholder="Leave blank to keep current" autocomplete="new-password" />
                        @error('password')
                            <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-muted-foreground mt-1">Minimum 8 characters. Leave blank to keep your current password.</p>
                    </div>

                    <div>
                        <mijnui:input label="Confirm New Password" type="password" wire:model="password_confirmation"
                            viewable placeholder="Re-enter new password" autocomplete="new-password" />
                        @error('password_confirmation')
                            <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <mijnui:button type="submit" color="primary" has-loading>Update Settings</mijnui:button>
            </form>
        </mijnui:card.content>
    </mijnui:card>
</div>
