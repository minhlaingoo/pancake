<mijnui:wrapper>
    <mijnui:sidebar x-data variant="double">

        <!-- --------------------------- Sidebar Header ---------------------------- -->

        <mijnui:sidebar.double>
            <mijnui:sidebar.logo>
                @php
                    $logoPath = setting('general')->logoPath ?? null;
                @endphp
                <img src="{{ $logoPath ? Storage::url($logoPath) : '/logo.png' }}" alt="Logo"
                    class="w-full mx-auto px-2 my-4" />
            </mijnui:sidebar.logo>

            <mijnui:sidebar.button name="dashboard">

                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" height="1em"
                    width="1em" xmlns="http://www.w3.org/2000/svg">
                    <path fill="none" d="M0 0h24v24H0z"></path>
                    <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"></path>
                </svg>
            </mijnui:sidebar.button>

            <!-- User Icon -->
            <mijnui:sidebar.button name="user">
                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 448 512" height="1em"
                    width="1em" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M436 160c6.6 0 12-5.4 12-12v-40c0-6.6-5.4-12-12-12h-20V48c0-26.5-21.5-48-48-48H48C21.5 0 0 21.5 0 48v416c0 26.5 21.5 48 48 48h320c26.5 0 48-21.5 48-48v-48h20c6.6 0 12-5.4 12-12v-40c0-6.6-5.4-12-12-12h-20v-64h20c6.6 0 12-5.4 12-12v-40c0-6.6-5.4-12-12-12h-20v-64h20zm-228-32c35.3 0 64 28.7 64 64s-28.7 64-64 64-64-28.7-64-64 28.7-64 64-64zm112 236.8c0 10.6-10 19.2-22.4 19.2H118.4C106 384 96 375.4 96 364.8v-19.2c0-31.8 30.1-57.6 67.2-57.6h5c12.3 5.1 25.7 8 39.8 8s27.6-2.9 39.8-8h5c37.1 0 67.2 25.8 67.2 57.6v19.2z">
                    </path>
                </svg>
            </mijnui:sidebar.button>

            <!-- Device Icon -->
            <mijnui:sidebar.button name="device">

                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-4">
                    <path fill-rule="evenodd"
                        d="M1.5 9.832v1.793c0 1.036.84 1.875 1.875 1.875h17.25c1.035 0 1.875-.84 1.875-1.875V9.832a3 3 0 0 0-.722-1.952l-3.285-3.832A3 3 0 0 0 16.215 3h-8.43a3 3 0 0 0-2.278 1.048L2.222 7.88A3 3 0 0 0 1.5 9.832ZM7.785 4.5a1.5 1.5 0 0 0-1.139.524L3.881 8.25h3.165a3 3 0 0 1 2.496 1.336l.164.246a1.5 1.5 0 0 0 1.248.668h2.092a1.5 1.5 0 0 0 1.248-.668l.164-.246a3 3 0 0 1 2.496-1.336h3.165l-2.765-3.226a1.5 1.5 0 0 0-1.139-.524h-8.43Z"
                        clip-rule="evenodd" />
                    <path
                        d="M2.813 15c-.725 0-1.313.588-1.313 1.313V18a3 3 0 0 0 3 3h15a3 3 0 0 0 3-3v-1.688c0-.724-.588-1.312-1.313-1.312h-4.233a3 3 0 0 0-2.496 1.336l-.164.246a1.5 1.5 0 0 1-1.248.668h-2.092a1.5 1.5 0 0 1-1.248-.668l-.164-.246A3 3 0 0 0 7.046 15H2.812Z" />
                </svg>

            </mijnui:sidebar.button>

            <!-- Setting Icon -->
            <mijnui:sidebar.button name="setting">

                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-4">
                    <path fill-rule="evenodd"
                        d="M11.078 2.25c-.917 0-1.699.663-1.85 1.567L9.05 4.889c-.02.12-.115.26-.297.348a7.493 7.493 0 0 0-.986.57c-.166.115-.334.126-.45.083L6.3 5.508a1.875 1.875 0 0 0-2.282.819l-.922 1.597a1.875 1.875 0 0 0 .432 2.385l.84.692c.095.078.17.229.154.43a7.598 7.598 0 0 0 0 1.139c.015.2-.059.352-.153.43l-.841.692a1.875 1.875 0 0 0-.432 2.385l.922 1.597a1.875 1.875 0 0 0 2.282.818l1.019-.382c.115-.043.283-.031.45.082.312.214.641.405.985.57.182.088.277.228.297.35l.178 1.071c.151.904.933 1.567 1.85 1.567h1.844c.916 0 1.699-.663 1.85-1.567l.178-1.072c.02-.12.114-.26.297-.349.344-.165.673-.356.985-.57.167-.114.335-.125.45-.082l1.02.382a1.875 1.875 0 0 0 2.28-.819l.923-1.597a1.875 1.875 0 0 0-.432-2.385l-.84-.692c-.095-.078-.17-.229-.154-.43a7.614 7.614 0 0 0 0-1.139c-.016-.2.059-.352.153-.43l.84-.692c.708-.582.891-1.59.433-2.385l-.922-1.597a1.875 1.875 0 0 0-2.282-.818l-1.02.382c-.114.043-.282.031-.449-.083a7.49 7.49 0 0 0-.985-.57c-.183-.087-.277-.227-.297-.348l-.179-1.072a1.875 1.875 0 0 0-1.85-1.567h-1.843ZM12 15.75a3.75 3.75 0 1 0 0-7.5 3.75 3.75 0 0 0 0 7.5Z"
                        clip-rule="evenodd" />
                </svg>

            </mijnui:sidebar.button>


            <mijnui:sidebar.double.footer class="flex flex-col items-center gap-4">
                <!-- Logout Icon -->
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
                <button type="button" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                    class="p-2 rounded-lg hover:bg-accent text-muted-foreground hover:text-red-500 transition-colors focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                    </svg>
                </button>

                <mijnui:badge color="success" size="xs">v1.0.1</mijnui:badge>
            </mijnui:sidebar.double.footer>

            <mijnui:sidebar.double.content value="dashboard" title="Getting Started">
                <mijnui:list class="flex h-full w-full flex-col items-center gap-2 px-4 py-4">
                    <mijnui:list.item href="{{ route('dashboard') }}" active="{{ request()->routeIs('dashboard') }}"
                        wire:navigate>
                        Dashboard</mijnui:list.item>
                </mijnui:list>
            </mijnui:sidebar.double.content>

            <mijnui:sidebar.double.content value="user" title="User Management">
                <mijnui:list class="flex h-full w-full flex-col items-center gap-2 px-4 py-4">
                    <mijnui:list.item href="{{ route('users.index') }}" active="{{ request()->routeIs('users.index') }}"
                        wire:navigate>Users List</mijnui:list.item>
                    <mijnui:list.item href="{{ route('role-permissions.index') }}"
                        active="{{ request()->routeIs('role-permissions.index') }}" wire:navigate>Role List
                    </mijnui:list.item>
                </mijnui:list>
            </mijnui:sidebar.double.content>

            <mijnui:sidebar.double.content value="device" title="Device Management">
                <mijnui:list class="flex h-full w-full flex-col items-center gap-2 px-4 py-4">
                    <mijnui:list.item href="{{ route('devices.index') }}"
                        active="{{ request()->routeIs('devices.index') }}" wire:navigate>Devices List</mijnui:list.item>
                    <mijnui:list.item href="{{ route('protocols.index') }}"
                        active="{{ request()->routeIs('protocols.index') }}" wire:navigate>Protocol List
                    </mijnui:list.item>
                    <mijnui:list.item href="{{ route('presets.index') }}"
                        active="{{ request()->routeIs('presets.index') }}" wire:navigate>Presets List
                    </mijnui:list.item>
                    <mijnui:list.item href="{{ route('protocols.histories') }}"
                        active="{{ request()->routeIs('protocols.histories') }}" wire:navigate>
                        Process History
                    </mijnui:list.item>
                    <mijnui:list.item href="{{ route('device-components.index') }}"
                        active="{{ request()->routeIs('device-components.*') }}" wire:navigate>Device Components List
                    </mijnui:list.item>
                </mijnui:list>
            </mijnui:sidebar.double.content>

            <mijnui:sidebar.double.content value="setting" title="App Setting">
                <mijnui:list class="flex h-full w-full flex-col items-center gap-2 px-4 py-4">
                    @if (auth()->user()->role->name == 'Administrator')
                        <mijnui:list.item href="{{ route('setting') }}" active="{{ request()->routeIs('setting') }}"
                            wire:navigate>
                            App Setting
                        </mijnui:list.item>
                    @endif

                    <mijnui:list.item href="{{ route('user-setting') }}"
                        active="{{ request()->routeIs('user-setting') }}" wire:navigate>
                        User Setting
                    </mijnui:list.item>

                    @if (auth()->user()->role->name == 'Administrator')
                        <mijnui:list.item href="{{ route('broker-setting') }}"
                            active="{{ request()->routeIs('broker-setting') }}" wire:navigate>Broker Setting
                        </mijnui:list.item>
                    @endif

                    @if (checkPermission('activity-log', 'view'))
                        <mijnui:list.item href="{{ route('activity-logs.index') }}"
                            active="{{ request()->routeIs('activity-logs.index') }}" wire:navigate>Activity Logs
                        </mijnui:list.item>
                    @endif
                </mijnui:list>

            </mijnui:sidebar.double.content>
        </mijnui:sidebar.double>

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

    <div class="p-4 pl-0">
        {{ $slot }}
    </div>

</mijnui:wrapper>