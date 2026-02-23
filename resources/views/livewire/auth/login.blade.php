<div class="min-h-screen flex items-center justify-center bg-background-alt p-4">
    <mijnui:card class="w-full max-w-[420px] shadow-lg border-border animate-in fade-in duration-500">

        <div class="p-6 sm:p-8">
            {{-- Error Alert --}}
            @if (session()->has('error'))
                <mijnui:alert color="error" class="mb-6">
                    <mijnui:alert.description class="pl-0 text-sm font-medium">
                        {{ session('error') }}
                    </mijnui:alert.description>
                </mijnui:alert>
            @endif

            {{-- Card Header --}}
            <div class="flex flex-col items-center mb-8">
                <div class="p-2.5 rounded-2xl bg-primary/5 border border-primary/10 shadow-sm mb-4">
                    @php
                        $logoPath = setting('general')->logoPath ?? null;
                    @endphp
                    <img src="{{ $logoPath ? Storage::url($logoPath) : '/logo.png' }}" class="size-12" />
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-foreground">
                    {{ setting('general')->appName }}
                </h1>
                <p class="text-muted-foreground text-sm mt-1">Sign in to your account</p>
            </div>

            <h2 class="text-lg font-semibold mb-6">Welcome back</h2>

            {{-- Login Form --}}
            <form wire:submit.prevent="login" class="space-y-5">

                {{-- Email --}}
                <div class="space-y-1.5">
                    <mijnui:input type="email" id="email" label="Email" wire:model="email" autofocus
                        placeholder="name@example.com" />
                </div>

                {{-- Password --}}
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between h-4">
                        <label for="password" class="text-sm font-medium">Password</label>
                        <button type="button" class="text-xs font-semibold text-primary hover:underline">
                            Forgot password?
                        </button>
                    </div>
                    <mijnui:input type="password" id="password" wire:model="password" viewable placeholder="••••••••" />
                </div>

                {{-- Terms & Modal --}}
                <div x-data="{ showModal: false }" class="flex items-center px-1">
                    <mijnui:checkbox wire:model.live="agree" class="mt-0.5" />
                    <div class="text-xs text-muted-foreground leading-normal">
                        I agree to the
                        <button type="button" @click="showModal = true"
                            class="inline font-bold text-primary cursor-pointer hover:underline">
                            Rules & Regulations
                        </button>
                    </div>

                    {{-- Plain Modal --}}
                    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
                        @keydown.escape.window="showModal = false">
                        {{-- Backdrop --}}
                        <div x-show="showModal" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0" @click="showModal = false"
                            class="absolute inset-0 bg-black/50"></div>
                        {{-- Content --}}
                        <div x-show="showModal" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            class="relative w-full max-w-md bg-background rounded-xl shadow-xl border border-border overflow-hidden">
                            <div class="px-6 py-4 flex items-center justify-between">
                                <h3 class="text-lg font-bold text-primary">Rules & Regulations</h3>
                                <button type="button" @click="showModal = false"
                                    class="text-muted-foreground hover:text-foreground transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            <hr class="border-border">
                            <div class="px-6 py-4 text-sm text-muted-foreground space-y-3 max-h-[60vh] overflow-y-auto">
                                <p>This software is the property of iProgen and is strictly for authorized use only.
                                    Unauthorized login, access, or any form of operation is strictly prohibited and may
                                    result in disciplinary and legal action.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Submit Button --}}
                <mijnui:button type="submit" class="w-full bg-primary text-white h-11 font-bold shadow-sm"
                    :disabled="!$agree" has-loading>
                    Login
                </mijnui:button>
            </form>

        </div>

    </mijnui:card>
</div>