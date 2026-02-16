<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Login')</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://bootswatch.com/5/lumen/bootstrap.css">
    <link rel="stylesheet" href="{{ asset_version('css/app-theme.css') }}">
    @stack('styles')
</head>
<body>
    <div class="container py-4">
        <header class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-clock text-primary"></i>
                <strong>Professional Timesheet</strong>
            </div>
        </header>

        <main class="py-3">
            @yield('content')
        </main>

        <footer class="text-center text-muted mt-4 pt-3 border-top">
            <small>&copy; {{ date('Y') }} Professional Timesheet</small>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    @stack('scripts')
</body>
</html>
