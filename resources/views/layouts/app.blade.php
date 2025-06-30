
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
    
    <!-- Styles -->
    <style>
        :root {
            --primary-color: #8b5cf6;
            --primary-dark: #7c3aed;
            --secondary-color: #374151;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --dark-color: #111827;
            --darker-color: #0f172a;
            --light-color: #1f2937;
            --lighter-color: #374151;
            --border-color: #4b5563;
            --text-primary: #f9fafb;
            --text-secondary: #d1d5db;
            --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.3), 0 1px 2px -1px rgb(0 0 0 / 0.2);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.4), 0 4px 6px -4px rgb(0 0 0 / 0.3);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            min-height: 100vh;
            color: var(--text-primary);
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .app-header {
            background: rgba(31, 41, 55, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: var(--shadow-lg);
            border: 1px solid rgba(75, 85, 99, 0.5);
        }

        .app-title {
            font-size: 2rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 8px;
        }

        .app-subtitle {
            color: var(--text-secondary);
            font-size: 1rem;
        }

        .main-content {
            background: rgba(31, 41, 55, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            box-shadow: var(--shadow-lg);
            border: 1px solid rgba(75, 85, 99, 0.5);
            overflow: hidden;
        }

        /* Utility Classes */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            white-space: nowrap;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow);
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success-color), #059669);
            color: white;
        }

        .btn-warning {
            background: linear-gradient(135deg, var(--warning-color), #d97706);
            color: white;
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger-color), #dc2626);
            color: white;
        }

        .btn-secondary {
            background: var(--secondary-color);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .alert {
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid;
            animation: slideIn 0.3s ease;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border-color: var(--success-color);
            color: #6ee7b7;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border-color: var(--danger-color);
            color: #fca5a5;
        }

        .alert-warning {
            background: rgba(245, 158, 11, 0.1);
            border-color: var(--warning-color);
            color: #fcd34d;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-weight: 500;
            margin-bottom: 8px;
            color: var(--text-primary);
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
            background: var(--dark-color);
            color: var(--text-primary);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.2);
        }

        .card {
            background: var(--light-color);
            border-radius: 12px;
            padding: 24px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border-color);
            margin-bottom: 24px;
        }

        .card-header {
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border-color);
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .loading {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--text-secondary);
        }

        .spinner {
            width: 16px;
            height: 16px;
            border: 2px solid var(--border-color);
            border-top: 2px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hidden {
            display: none !important;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .mb-0 { margin-bottom: 0; }
        .mb-2 { margin-bottom: 8px; }
        .mb-4 { margin-bottom: 16px; }
        .mb-6 { margin-bottom: 24px; }

        .mt-4 { margin-top: 16px; }
        .mt-6 { margin-top: 24px; }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 16px;
            }
            
            .app-header {
                padding: 20px;
            }
            
            .app-title {
                font-size: 1.5rem;
            }
            
            .btn {
                padding: 10px 16px;
                font-size: 13px;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <div class="container">
        <header class="app-header">
            <h1 class="app-title">
                <i class="fas fa-clock"></i>
                Professional Timesheet
            </h1>
            <p class="app-subtitle">Simple, reliable time tracking for professionals</p>
        </header>

        <main class="main-content">
            @yield('content')
        </main>
    </div>

    <!-- Scripts -->
    <script>
        // Global CSRF token
        window.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
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
            }
        };

        // Global utility functions
        window.utils = {
            formatTime(minutes) {
                const hours = Math.floor(minutes / 60);
                const mins = minutes % 60;
                return `${hours}:${mins.toString().padStart(2, '0')}`;
            },
            
            formatDate(dateString) {
                return new Date(dateString).toLocaleDateString('en-US', {
                    weekday: 'short',
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
            },
            
            getCurrentDateTime() {
                const now = new Date();
                return {
                    date: now.toISOString().split('T')[0],
                    time: now.toTimeString().slice(0, 5)
                };
            }
        };
    </script>
    
    @stack('scripts')
</body>
</html>