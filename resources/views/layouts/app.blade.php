<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Professional Timesheet')</title>
    
    <script src="{{ asset_version('js/theme.js') }}"></script>
    
    <!-- Icons -->
    <link href="{{ asset_version('vendor/fontawesome/css/all.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset_version('vendor/bootstrap/bootstrap.min.css') }}">

    <link rel="stylesheet" href="{{ asset_version('css/app-theme.css') }}">


    
    @stack('styles')
    <link rel="stylesheet" href="{{ asset_version('css/workspace.css') }}">
</head>
<body>
    <a class="skip-link" href="#@yield('main-anchor', 'main-content')">Skip to content</a>
    @php
        $currentUserPayload = auth()->check()
            ? [
                'id' => auth()->user()->id,
                'name' => auth()->user()->name,
                'email' => auth()->user()->email,
                'role' => auth()->user()->role,
            ]
            : null;
    @endphp

    <div class="container-xl py-2 app-shell">
        <header class="app-header">
            <a class="workspace-brand" href="{{ route('timesheet.index') }}"><span class="brand-mark"><i class="fas fa-clock" aria-hidden="true"></i></span> Timesheet <span class="workspace-caption">Workspace</span></a>
            <div class="app-header-controls">
                @auth
                    <span class="app-user-badge">{{ auth()->user()->name }}</span>
                @endauth
                <button id="theme-toggle" type="button" class="btn btn-outline-secondary" aria-label="Switch color theme"><i class="fas fa-moon" aria-hidden="true"></i><span>Dark mode</span></button>
                @auth
                    <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="btn btn-outline-secondary" aria-label="Sign out"><i class="fas fa-sign-out-alt" aria-hidden="true"></i><span class="signout-label">Sign out</span></button></form>
                @endauth
            </div>
        </header>

        <main class="main-content" id="main-content" tabindex="-1">
            @yield('content')
        </main>
    </div>

    <div id="notification-region" class="notification-region" aria-live="polite"></div>

    {{-- Modals Stack (renders outside main-content) --}}
    @stack('modals')

    <script src="{{ asset_version('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <!-- Scripts -->
    <script>
        // Global CSRF token
        window.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Application timezone (configurable via APP_TIMEZONE)
        window.appTimezone = @json(config('app.timezone', 'UTC'));
        window.currentUser = @json($currentUserPayload);
        
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

                const controller = new AbortController();
                const timeout = setTimeout(() => controller.abort(), options.timeout ?? 20000);
                const abort = () => controller.abort();
                options.signal?.addEventListener('abort', abort, { once: true });
                config.signal = controller.signal;
                if (options.signal?.aborted) controller.abort();
                try {
                    const response = await fetch(url, config);
                    if (response.status === 401) {
                        window.location.href = '/login';
                        throw new Error('Authentication required.');
                    }

                    const data = await response.json();
                    
                    if (!response.ok) {
                        throw new Error(data.message || `HTTP error! status: ${response.status}`);
                    }
                    
                    return data;
                } catch (error) {
                    console.error('API request failed:', error);
                    if (controller.signal.aborted) throw new Error('The request timed out or was cancelled. Please try again.');
                    throw error;
                } finally {
                    clearTimeout(timeout);
                    options.signal?.removeEventListener('abort', abort);
                }
            }
        };

        // Global notification helper
        window.notify = {
            show(message, type = 'success') {
                const alert = document.createElement('div');
                alert.className = `alert alert-${type === 'error' ? 'danger' : type}`;
                alert.setAttribute('role', type === 'error' ? 'alert' : 'status');
                alert.innerHTML = `
                    <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
                    ${message}
                `;

                const container = document.getElementById('notification-region');
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
    
    <script src="{{ asset_version('js/dialogs.js') }}" defer></script>
    @include('components.module-map')
    @stack('scripts')
</body>
</html>
