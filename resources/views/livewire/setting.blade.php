<div class="space-y-4"> 

    <div>
        <mijnui:breadcrumbs>
            <mijnui:breadcrumbs.item isLast>App setting</mijnui:breadcrumbs.item>
        </mijnui:breadcrumbs>
        <h2 class="text-2xl font-semibold">App Setting</h2>
    </div>
    <mijnui:card>
        <x-alert />
        <mijnui:card.content class="pt-4">
            <form wire:submit.prevent="updateAppSetting" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                    {{-- //* span 2 --}}
                    <div class="md:col-span-3">
                        <mijnui:input label="App Name" type="text" wire:model="appName" />
                    </div>

                    <div class="md:col-span-2">
                        <mijnui:textarea label="App Description" placeholder="Type application's description"
                            wire:model="appDescription" />
                    </div>


                    {{-- //! logo --}}
                    <div class="max-w-64">
                        <mijnui:label>App Logo</mijnui:label>
                        <div class="w-full">
                            {{-- Hidden file input --}}
                            <input type="file" wire:model="logo" id="logoInput" class="hidden" accept="image/*">

                            {{-- Styled Upload Box --}}
                            <div class="w-full  border-2 border-dashed border-gray-300 rounded-xl p-1 text-center cursor-pointer"
                                onclick="document.getElementById('logoInput').click()">
                                <p class="text-gray-600">Click to upload logo (JPG, PNG, WEBP, SVG - max 1MB)</p>

                                @if ($logo)
                                    <div class="mt-1">
                                        <p class="text-green-600 font-semibold">Preview:</p>
                                        <img src="{{ $logo->temporaryUrl() }}"
                                            class="mx-auto w-32 h-32 object-contain mt-2 rounded" />
                                    </div>
                                @else
                                    <div class="mt-8">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-gray-400"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 15a4 4 0 014-4h10a4 4 0 014 4M7 10V6a4 4 0 014-4h2a4 4 0 014 4v4" />
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            {{-- Show validation error --}}
                            @error('logo')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <mijnui:button color="primary" has-loading>Update</mijnui:button>
            </form>
        </mijnui:card.content>

        <script>
            window.addEventListener('refresh-page', () => {
                window.location.reload();
            });
        </script>
    </mijnui:card>
</div>