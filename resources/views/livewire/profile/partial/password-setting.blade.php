<div class="flex gap-8">
    <x-alert />
    <div class="flex flex-col w-1/2 ">
        <mijnui:label class="font-bold" size="lg">Passwords</mijnui:label>
        <mijnui:label>User can change password in this section
        </mijnui:label>
    </div>
    <mijnui:card class="px-4">
        <mijnui:card.header class="font-semibold">Password </mijnui:card.header>
        <mijnui:card.content class="space-y-2">
            <mijnui:input wire:model="old_password" label="Old Password" />
            <mijnui:input wire:model="password" label="New Password" />
            <mijnui:input wire:model="password_confirmation" label="Password Confirmation" />

            <mijnui:button wire:click="updatePassword" class="!mt-4" size="sm" color="primary"
                wire:loading.disabled>
                <span wire:loading>Updating</span>
                <span wire:loading.remove>Update</span>
            </mijnui:button>

        </mijnui:card.content>
    </mijnui:card>
</div>
