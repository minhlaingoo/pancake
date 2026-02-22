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
                    <img src="/logo.png" class="size-12" />
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
                <div class="flex items-start gap-2.5 px-1">
                    <mijnui:checkbox wire:model.live="agree" class="mt-0.5" />
                    <div class="text-xs text-muted-foreground leading-normal">
                        I agree to the
                        <mijnui:modal>
                            <mijnui:modal.trigger
                                class="inline font-bold text-foreground cursor-pointer hover:underline">
                                rules & policy
                            </mijnui:modal.trigger>
                            <mijnui:modal.content class="max-w-md">
                                <mijnui:modal.header title="Rules & Policy">
                                    <h3 class="text-lg font-bold">Rules & Policy</h3>
                                </mijnui:modal.header>
                                <hr>
                                <mijnui:modal.body class="text-sm text-muted-foreground space-y-3">
                                    <p>This system is the property of <b>{{ setting('general')->appName }}</b>.
                                        Unauthorized access is prohibited and may lead to legal action.</p>
                                    <p>By logging in, you agree to comply with all security protocols and company
                                        policies.</p>
                                </mijnui:modal.body>
                            </mijnui:modal.content>
                        </mijnui:modal>
                        of the terminal.
                    </div>
                </div>

                {{-- Submit Button --}}
                <mijnui:button type="submit" class="w-full bg-primary text-white h-11 font-bold shadow-sm"
                    :disabled="!$agree" has-loading>
                    Login
                </mijnui:button>
            </form>

            {{-- Test Credentials --}}
            <div class="mt-8 pt-6 border-t border-dashed">
                <div
                    class="bg-muted/50 rounded-lg p-3 text-[11px] text-muted-foreground font-medium flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-flask-vial opacity-50"></i>
                        <span>Test: testing@example.com</span>
                    </div>
                    <span class="font-mono bg-background px-1.5 py-0.5 rounded border">testing1234</span>
                </div>
            </div>
        </div>

    </mijnui:card>
</div>