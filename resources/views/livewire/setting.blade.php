<div class="space-y-4">

    <div>
        <mijnui:breadcrumbs>
            <mijnui:breadcrumbs.item isLast>App Settings</mijnui:breadcrumbs.item>
        </mijnui:breadcrumbs>
        <h2 class="text-2xl font-semibold">App Settings</h2>
    </div>
    <mijnui:card>
        <x-alert />
        <mijnui:card.content class="pt-4">
            <form wire:submit.prevent="updateAppSetting" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                    <div class="md:col-span-3">
                        <mijnui:input label="App Name" type="text" wire:model="appName" required maxlength="64"
                            placeholder="e.g. My IoT Dashboard" />
                        @error('appName')
                            <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <mijnui:textarea label="App Description" placeholder="Briefly describe what this application does"
                            wire:model="appDescription" maxlength="1024" />
                        @error('appDescription')
                            <p class="text-destructive text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="max-w-64">
                        <mijnui:label>App Logo</mijnui:label>
                        <div class="w-full" wire:loading.class="opacity-50 pointer-events-none" wire:target="logo">
                            {{-- Hidden file input --}}
                            <input type="file" wire:model="logo" id="logoInput" class="hidden"
                                accept="image/jpeg,image/png,image/webp,image/svg+xml">

                            {{-- Styled Upload Box --}}
                            <label for="logoInput"
                                class="block w-full border-2 border-dashed border-border rounded-xl p-4 text-center cursor-pointer hover:border-primary/50 hover:bg-muted/30 transition-colors">

                                {{-- Upload loading indicator --}}
                                <div wire:loading wire:target="logo" class="py-4">
                                    <svg class="animate-spin h-8 w-8 mx-auto text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <p class="text-sm text-muted-foreground mt-2">Uploading...</p>
                                </div>

                                <div wire:loading.remove wire:target="logo">
                                    @if ($logo)
                                        <div class="py-2">
                                            <p class="text-success text-sm font-semibold mb-2">New logo</p>
                                            <img src="{{ $logo->temporaryUrl() }}" alt="Logo preview"
                                                class="mx-auto w-32 h-32 object-contain rounded" />
                                        </div>
                                    @elseif($logoPath)
                                        <div class="py-2">
                                            <p class="text-muted-foreground text-sm font-semibold mb-2">Current logo</p>
                                            <img src="{{ Storage::url($logoPath) }}" alt="Current application logo"
                                                class="mx-auto w-32 h-32 object-contain rounded" />
                                        </div>
                                    @else
                                        <div class="py-6">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-muted-foreground"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                    <p class="text-muted-foreground text-xs mt-1">Click to upload (JPG, PNG, WEBP, SVG — max 1MB)</p>
                                </div>
                            </label>

                            @error('logo')
                                <p class="text-destructive text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <mijnui:button type="submit" color="primary" has-loading>Save Settings</mijnui:button>
            </form>
        </mijnui:card.content>

        <script>
            window.addEventListener('refresh-page', () => {
                window.location.reload();
            });
        </script>
    </mijnui:card>
</div>
