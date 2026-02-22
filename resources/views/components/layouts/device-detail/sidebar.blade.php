<mijnui:wrapper>
    <mijnui:sidebar>

        <mijnui:list>
            <mijnui:list.item href="{{ route('devices.detail', ['id' => request()->route('id')]) }}"
                active="{{ request()->routeIs('devices.detail') }}" wire:navigate>
                Dashboard
            </mijnui:list.item>

            <mijnui:list.item href="{{ route('devices.logs', ['id' => request()->route('id')]) }}"
                active="{{ request()->routeIs('devices.logs') }}" wire:navigate>
                Logs
            </mijnui:list.item>

            @if (checkPermission('device', 'update'))
                <mijnui:list.item href="{{ route('devices.setting', ['id' => request()->route('id')]) }}"
                    active="{{ request()->routeIs('devices.setting') }}" wire:navigate>
                    Settings
                </mijnui:list.item>
            @endif

            <mijnui:list.item href="{{ route('devices.index') }}" class="self-end" wire:navigate>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-4">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                </svg>

                Main Menu
            </mijnui:list.item>

        </mijnui:list>
    </mijnui:sidebar>

    <mijnui:header>
        <mijnui:header.navbar>

            <mijnui:icon x-on:click="$store.theme.switchTheme()"
                class="hover:bg-accent p-1 size-8 rounded  cursor-pointer border">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-6 dark-icon hidden">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
                </svg>

                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-6 light-icon ">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                </svg>
            </mijnui:icon>

            <mijnui:icon x-on:click="drawerOpen = true"
                class="p-1 size-8 rounded hover:bg-accent cursor-pointer border">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                </svg>
            </mijnui:icon>

        </mijnui:header.navbar>
    </mijnui:header>

    <div class="">
        {{ $slot }}
    </div>

</mijnui:wrapper>