<!DOCTYPE html>
<html lang="en" xmlns:mijnui="http://www.w3.org/1999/html">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-background-alt" x-data="{ drawerOpen: false }" x-init="$store.theme.init()">
    <mijnui:header>
        <mijnui:header.navbar>

            <mijnui:icon x-on:click="$store.theme.switchTheme()" aria-label="Toggle dark mode"
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

            <mijnui:icon x-on:click="drawerOpen = true" aria-label="Open notifications"
                class="p-1 size-8 rounded hover:bg-accent cursor-pointer border">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                </svg>
            </mijnui:icon>

            <mijnui:dropdown>
                <mijnui:dropdown.trigger>
                    <mijnui:avatar fallback="{{ auth()->user()->name[0] }}" class="cursor-pointer"></mijnui:avatar>
                </mijnui:dropdown.trigger>
                <mijnui:dropdown.content align="right">

                    <mijnui:dropdown.item href="{{ route('user-profile') }}">
                        {{ __('Profile') }}
                    </mijnui:dropdown.item>
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <mijnui:dropdown.item as="button" type="submit" icon="arrow-right-start-on-rectangle">
                            {{ __('Log Out') }}
                        </mijnui:dropdown.item>
                    </form>
                </mijnui:dropdown.content>
            </mijnui:dropdown>
        </mijnui:header.navbar>
    </mijnui:header>

    {{-- Notification Drawer --}}
    <div x-cloak x-on:click.self="drawerOpen = false"
        x-bind:class="drawerOpen ? 'pointer-events-auto opacity-100' : 'pointer-events-none opacity-0'"
        role="dialog" aria-modal="true" aria-label="Notifications"
        class="transition w-screen h-screen fixed top-0 left-0 z-[10000] bg-gray-900/80">
        <div x-show="drawerOpen" x-transition:enter="transition transform ease-in-out duration-300"
            x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
            x-transition:leave="transition transform ease-in-out duration-300" x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class=" py-2 h-full w-72 absolute top-0 right-0 bg-accent shadow-lg">
            <h3 class="text-lg font-semibold px-4">Notifications</h3>
            <hr>
            <div class="px-4 py-2">
                <p class="text-center text-sm text-muted-foreground">
                    No notifications yet.
                </p>
            </div>
        </div>
    </div>

    {{ $slot }}

    @stack('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('theme', {
                theme: 'light',
                init() {
                    this.theme = localStorage.getItem('theme') || 'light';
                    if (this.theme === 'dark') {
                        document.documentElement.classList.add('dark');
                        this.changeThemeIcon();
                    }
                },
                switchTheme() {
                    document.documentElement.classList.toggle('dark');
                    this.changeThemeIcon();
                    this.theme = this.theme === 'light' ? 'dark' : 'light';
                    localStorage.setItem('theme', this.theme);
                },
                changeThemeIcon() {
                    document.querySelector('.dark-icon').classList.toggle('hidden');
                    document.querySelector('.light-icon').classList.toggle('hidden');
                }
            })
        });
    </script>

    @mijnuiScripts
</body>

</html>