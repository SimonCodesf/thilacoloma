<!DOCTYPE html>
<html>
<head>
    <title>@yield('title', 'Statamic Site')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('head')
</head>
<body>
    @yield('content')
    @yield('scripts')
</body>
</html>
