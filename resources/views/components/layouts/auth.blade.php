<!DOCTYPE html>
<html lang="en" xmlns:mijnui="http://www.w3.org/1999/html">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-background-alt" x-data x-init="$store.theme.init()">
{{ $slot }}

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('theme', {
            theme: 'light',
            init() {
                this.theme = localStorage.getItem('theme') || 'light';
                if (this.theme === 'dark') {
                    document.documentElement.classList.add('dark');
                    document.querySelector('.dark-icon')?.classList.toggle('hidden');
                    document.querySelector('.light-icon')?.classList.toggle('hidden');
                }
            },
            switchTheme() {
                document.documentElement.classList.toggle('dark');
                document.querySelector('.dark-icon')?.classList.toggle('hidden');
                document.querySelector('.light-icon')?.classList.toggle('hidden');
                this.theme = this.theme === 'light' ? 'dark' : 'light';
                localStorage.setItem('theme', this.theme);
            }
        })
    });
</script>

@mijnuiScripts
</body>
</html>
