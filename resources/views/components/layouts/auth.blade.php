<!DOCTYPE html>
<html lang="en" xmlns:mijnui="http://www.w3.org/1999/html">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen">
{{ $slot }}

@mijnuiScripts
</body>
</html>
