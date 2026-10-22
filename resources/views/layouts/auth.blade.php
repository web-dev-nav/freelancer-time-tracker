<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Login')</title>

    <script src="{{ asset_version('js/theme.js') }}"></script>
    <link href="{{ asset_version('vendor/fontawesome/css/all.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset_version('vendor/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset_version('css/app-theme.css') }}">
    @stack('styles')
    <link rel="stylesheet" href="{{ asset_version('css/workspace.css') }}">
</head>
<body>
    <div class="container py-4">
        <header class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-clock text-primary"></i>
                <strong>Professional Timesheet</strong>
            </div>
            <button id="theme-toggle" type="button" class="btn btn-outline-secondary"><i class="fas fa-moon" aria-hidden="true"></i><span>Dark mode</span></button>
        </header>

        <main class="py-3">
            @yield('content')
        </main>

        <footer class="text-center text-muted mt-4 pt-3 border-top">
            <small>&copy; {{ date('Y') }} Professional Timesheet</small>
        </footer>
    </div>

    <script src="{{ asset_version('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
    @stack('scripts')
</body>
</html>
