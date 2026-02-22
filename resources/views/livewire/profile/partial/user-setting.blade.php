<div class="flex gap-8">
    <x-alert />
    <div class="flex flex-col w-1/2 ">
        <mijnui:label class="font-bold" size="lg">User Information</mijnui:label>
        <mijnui:label>This section shows all the information of the user and their role.
        </mijnui:label>
    </div>
    <mijnui:card class="px-4">
        <mijnui:card.header class="font-semibold">User Information</mijnui:card.header>
        <mijnui:card.content class="space-y-2 text-sm">

            {{-- Design with inputs only --}}
            <div class="flex items-end gap-2">
                <div class="flex-1">
                    <mijnui:input label="Username" wire:model="name" />
                </div>
                <mijnui:button wire:click="updateName" class="!mt-4" size="sm" color="primary"
                    wire:loading.disabled>
                    <span wire:loading>Updating</span>
                    <span wire:loading.remove>Update</span>
                </mijnui:button>
            </div>
            <mijnui:input label="Email" value="{{ $user->email }}" disabled />
            <mijnui:input label="Role" value="{{ $user->role->name }}" disabled />

            {{-- Design with text row only  --}}
            {{-- <div class="flex items-center">
                <p class="w-16 font-medium">Name:</p>
                <p>{{ $user->name }}</p>
            </div>
            <div class="flex items-center">
                <p class="w-16 font-medium">Email:</p>
                <p>{{ $user->email }}</p>
            </div>
            <div class="flex items-center">
                <p class="w-16 font-medium">Role:</p>
                <p>{{ $user->role->name }}</p>
            </div> --}}
        </mijnui:card.content>
    </mijnui:card>
</div>
