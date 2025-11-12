<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Professional Timesheet')</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://bootswatch.com/5/lumen/bootstrap.css">
    <link rel="stylesheet" href="https://bootswatch.com/_vendor/bootstrap-icons/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://bootswatch.com/_vendor/prismjs/themes/prism-okaidia.css">
    <link rel="stylesheet" href="https://bootswatch.com/_assets/css/custom.min.css">
    <link rel="stylesheet" href="{{ asset('css/app-theme.css') }}?v={{ time() }}">


    
    @stack('styles')
</head>
<body>
    <div class="container-xl py-3">
        <header class="app-header">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                <div>
                    <h1 class="app-title mb-1">
                        <i class="fas fa-clock me-2"></i>
                        Professional Timesheet
                    </h1>
                    <p class="app-subtitle mb-0">Simple, reliable time tracking for professionals</p>
                </div>
                <button id="theme-toggle" type="button" class="btn btn-outline-secondary d-inline-flex align-items-center">
                    <i class="bi bi-moon-stars-fill me-2"></i>
                    <span>Dark Mode</span>
                </button>
            </div>
        </header>

        <main class="main-content">
            @yield('content')
        </main>
    </div>

    {{-- Modals Stack (renders outside main-content) --}}
    @stack('modals')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkH+6W2kQja1GaGLjm+rXhN3kLBYjgJv1bVbWcv16O3EO8PhB0uT" crossorigin="anonymous"></script>
    <!-- Scripts -->
    <script>
        (function initThemeToggle() {
            const themeToggle = document.getElementById('theme-toggle');
            const body = document.body;
            const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            const storedTheme = localStorage.getItem('app-theme');

            function applyTheme(theme) {
                const useDark = theme === 'dark';
                body.classList.toggle('dark-mode', useDark);

                if (themeToggle) {
                    const icon = themeToggle.querySelector('i');
                    const label = themeToggle.querySelector('span');
                    if (icon && label) {
                        if (useDark) {
                            icon.classList.remove('bi-moon-stars-fill');
                            icon.classList.add('bi-sun-fill');
                            label.textContent = 'Light Mode';
                        } else {
                            icon.classList.remove('bi-sun-fill');
                            icon.classList.add('bi-moon-stars-fill');
                            label.textContent = 'Dark Mode';
                        }
                    }
                }
            }

            const initialTheme = storedTheme || (prefersDark ? 'dark' : 'light');
            applyTheme(initialTheme);
            if (storedTheme !== initialTheme) {
                localStorage.setItem('app-theme', initialTheme);
            }

            if (themeToggle) {
                themeToggle.addEventListener('click', () => {
                    const nextTheme = body.classList.contains('dark-mode') ? 'light' : 'dark';
                    applyTheme(nextTheme);
                    localStorage.setItem('app-theme', nextTheme);
                });
            }
        })();

        // Global CSRF token
        window.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Application timezone (configurable via APP_TIMEZONE)
        window.appTimezone = @json(config('app.timezone', 'UTC'));
        
        // Global API helper
        window.api = {
            async request(url, options = {}) {
                const defaultOptions = {
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': window.csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                };

                const config = {
                    ...defaultOptions,
                    ...options,
                    headers: {
                        ...defaultOptions.headers,
                        ...options.headers
                    }
                };

                try {
                    const response = await fetch(url, config);
                    const data = await response.json();
                    
                    if (!response.ok) {
                        throw new Error(data.message || `HTTP error! status: ${response.status}`);
                    }
                    
                    return data;
                } catch (error) {
                    console.error('API request failed:', error);
                    throw error;
                }
            }
        };

        // Global notification helper
        window.notify = {
            show(message, type = 'success') {
                const alert = document.createElement('div');
                alert.className = `alert alert-${type}`;
                alert.innerHTML = `
                    <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
                    ${message}
                `;

                const container = document.querySelector('.main-content');
                container.insertBefore(alert, container.firstChild);

                setTimeout(() => {
                    alert.remove();
                }, 5000);
            },

            success(message) {
                this.show(message, 'success');
            },

            error(message) {
                this.show(message, 'error');
            },

            warning(message) {
                this.show(message, 'warning');
            },

            info(message) {
                this.show(message, 'warning'); // Use warning style for info
            }
        };

        // Global utility functions (will be overridden by utils.js module with timezone-aware versions)
        window.utils = window.utils || {
            formatTime(minutes) {
                const hours = Math.floor(minutes / 60);
                const mins = minutes % 60;
                return `${hours}:${mins.toString().padStart(2, '0')}`;
            }
        };
    </script>
    
    @stack('scripts')
</body>
</html>
