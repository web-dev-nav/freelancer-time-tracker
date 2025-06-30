# Freelancer Time Tracker

A Laravel-based time tracking application for freelancers to log hours, track projects, and export timesheets.

## Features

- Time logging with start/stop functionality
- Project-based time tracking
- Export timesheets to various formats (Excel support via Maatwebsite/Excel)
- Clean, responsive UI with Tailwind CSS
- SQLite database for lightweight deployment

## Tech Stack

- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Blade templates, Tailwind CSS, Vite
- **Database**: SQLite
- **Export**: Maatwebsite/Excel package

## Project Structure

```
app/
├── Exports/
│   └── TimeLogExport.php          # Excel export functionality
├── Http/Controllers/
│   ├── Controller.php             # Base controller
│   └── TimeLogController.php      # Time log CRUD operations
├── Models/
│   └── TimeLog.php               # Time log model
└── Providers/
    └── AppServiceProvider.php    # Service provider configuration

database/
├── migrations/
│   ├── 2025_06_23_190318_create_time_logs_table.php
│   └── 2025_06_23_191616_create_sessions_table.php
├── factories/
│   └── UserFactory.php
└── seeders/
    └── DatabaseSeeder.php

resources/
├── views/
│   ├── layouts/
│   │   └── app.blade.php         # Main layout template
│   └── timesheet/
│       └── index.blade.php       # Timesheet interface
├── css/
│   └── app.css                   # Tailwind CSS styles
└── js/
    ├── app.js                    # Main JavaScript
    └── bootstrap.js              # Bootstrap configuration

routes/
├── web.php                       # Web routes
└── console.php                   # Console commands

config/                           # Laravel configuration files
public/                           # Public assets
storage/                          # File storage and logs
tests/                           # Feature and unit tests
```

## Installation

1. Clone the repository
2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Install Node.js dependencies:
   ```bash
   npm install
   ```

4. Set up environment:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. Run migrations:
   ```bash
   php artisan migrate
   ```

## Development

Start the development server:
```bash
composer run dev
```

This runs multiple services concurrently:
- Laravel development server
- Queue worker
- Log viewer (Pail)
- Vite asset bundler

## Testing

Run the test suite:
```bash
composer run test
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
