# Freelancer Time Tracker

A comprehensive Laravel-based time tracking and invoicing application designed for freelancers and independent contractors. Track billable hours, manage multiple projects, generate professional invoices with PDF support, and automate client billing workflows.

<img width="1920" height="911" alt="captureit_7-17-2025_at_23-47-55" src="https://github.com/user-attachments/assets/62759f0f-429d-4c0f-8b35-07a54acb1332" />
<img width="1920" height="911" alt="captureit_7-17-2025_at_23-48-14" src="https://github.com/user-attachments/assets/b91ea2f4-770e-4f5e-98b1-2176cb0ea777" />
<img width="1920" height="1759" alt="captureit_7-17-2025_at_23-48-25" src="https://github.com/user-attachments/assets/e3b07261-8dcb-4f14-99d1-9069ba0fe43a" />
<img width="1920" height="1516" alt="captureit_7-17-2025_at_23-48-45" src="https://github.com/user-attachments/assets/248fa1ba-c14f-4b19-b5d6-58a0c1e859ae" />

## Features

### Time Tracking
- **Real-time Clock In/Out**: Start and stop timer with live tracking
- **Session Management**: Active session monitoring with automatic duration calculation
- **Historical Logging**: Complete time log history with date range filtering
- **Manual Entry**: Create, edit, and delete time logs manually
- **Project Association**: Link time entries to specific projects
- **Dashboard Analytics**: Real-time statistics and reports

### Project Management
- **Multi-Project Support**: Manage unlimited projects with client details
- **Client Information**: Store client name, email, and address
- **Hourly Rates**: Set custom billing rates per project
- **Tax Configuration**: Enable/disable tax calculations (13% HST/GST)
- **Color Coding**: Visual project identification with custom colors
- **Project Status**: Active/Archived project states
- **Project Statistics**: Track total hours, sessions, and earnings per project
- **Project Backups**: Export individual project data as SQL backups

### Invoice Management
- **Automated Invoice Generation**: Create invoices from tracked time logs
- **Professional PDF Invoices**: Generate branded PDF documents with company details
- **Invoice States**: Draft, Sent, Paid, Cancelled, and Overdue statuses
- **Custom Invoice Items**: Add manual line items beyond time logs
- **Tax Calculations**: Automatic tax computation based on project settings
- **Invoice Numbering**: Auto-generated invoice numbers (INV-YYYY-MM-0001 format)
- **Due Date Management**: Set payment terms and track overdue invoices
- **Invoice History**: Complete audit trail of all invoice activities
- **Email Delivery**: Send invoices directly to clients via email
- **Email Tracking**: Track when clients open invoices with pixel tracking
- **Scheduled Sending**: Schedule invoice emails for future delivery
- **Payment Status**: Mark invoices as paid with timestamp tracking
- **Revenue Analytics**: Track paid and pending revenue with breakdowns

### Email & Communication
- **SMTP/SendMail Support**: Configurable email delivery methods
- **Custom Email Settings**: Configure SMTP host, port, encryption, and credentials
- **Email Templates**: HTML-formatted invoice emails with payment instructions
- **Email Testing**: Built-in email configuration testing tool
- **Open Tracking**: Monitor when clients view invoice emails
- **Scheduled Emails**: Queue invoice emails for future delivery

### Export & Backup
- **Excel Export**: Export timesheets to Excel format (Maatwebsite/Excel)
- **PDF Generation**: Professional invoice PDFs (DomPDF)
- **Database Backups**: Automated full database backups every 2 days at 2:00 AM
- **Project Backups**: Individual project backup with SQL export
- **Backup Management**: List, download, and delete backup files
- **30-Day Retention**: Automatic cleanup of old backups

### Settings & Configuration
- **Company Settings**: Configure company name, address, and tax number
- **Payment Instructions**: Set up e-transfer, bank info, and payment methods
- **Email Configuration**: SMTP settings, from address, and mailer selection
- **Application Settings**: Centralized settings management
- **Configuration Testing**: Test email delivery and verify settings

## Tech Stack

- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Blade templates, Tailwind CSS 4, Alpine.js, Vite 6
- **Database**: SQLite (lightweight, zero-configuration)
- **PDF Generation**: DomPDF (barryvdh/laravel-dompdf)
- **Excel Export**: Maatwebsite/Excel 3.1
- **Email**: Laravel Mail with SMTP/Sendmail support
- **Task Scheduling**: Laravel Scheduler (cron-based automation)
- **Development Tools**: Laravel Pail, Tinker, Pint, Sail

## Project Structure

```
app/
├── Console/Commands/
│   ├── BackupDatabase.php           # Full database backup command
│   ├── BackupProjects.php           # Individual project backup
│   ├── SendScheduledInvoiceEmails.php # Scheduled email delivery
│   └── SendScheduledInvoices.php    # Invoice reminder automation
├── Exports/
│   └── TimeLogExport.php            # Excel export functionality
├── Http/Controllers/
│   ├── Controller.php               # Base controller
│   ├── TimeLogController.php        # Time tracking CRUD & analytics
│   ├── ProjectController.php        # Project management
│   ├── InvoiceController.php        # Invoice & billing operations
│   ├── SettingController.php        # Application settings
│   └── DatabaseBackupController.php # Backup management
├── Models/
│   ├── TimeLog.php                  # Time log model with scopes
│   ├── Project.php                  # Project model with stats
│   ├── Invoice.php                  # Invoice model with status tracking
│   ├── InvoiceItem.php              # Invoice line items
│   ├── InvoiceHistory.php           # Invoice audit trail
│   └── Setting.php                  # Key-value settings storage
├── Services/
│   └── InvoiceMailer.php            # Email service for invoices
└── Providers/
    └── AppServiceProvider.php       # Service provider configuration

database/
├── migrations/
│   ├── *_create_time_logs_table.php
│   ├── *_create_projects_table.php
│   ├── *_create_invoices_table.php
│   ├── *_create_invoice_items_table.php
│   ├── *_create_invoice_history_table.php
│   ├── *_create_settings_table.php
│   └── *_add_*.php                  # Various schema updates
├── factories/
│   └── UserFactory.php
└── seeders/
    └── DatabaseSeeder.php

resources/
├── views/
│   ├── layouts/
│   │   └── app.blade.php            # Main layout template
│   ├── timesheet/
│   │   └── index.blade.php          # Timesheet interface
│   ├── invoices/
│   │   └── pdf.blade.php            # Invoice PDF template
│   └── settings/
│       └── index.blade.php          # Settings page
├── css/
│   └── app.css                      # Tailwind CSS styles
└── js/
    ├── app.js                       # Main JavaScript
    └── timesheet/                   # Frontend time tracking logic

routes/
├── web.php                          # Web routes & cron endpoints
├── api.php                          # RESTful API routes
└── console.php                      # Scheduled tasks & artisan commands

storage/
└── app/
    └── backups/                     # Database & project backups

config/                              # Laravel configuration files
public/                              # Public assets and compiled files
tests/                              # Feature and unit tests
docs/                               # Additional documentation
```

## Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js & npm
- SQLite (or MySQL/PostgreSQL if preferred)

### Setup Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd freelancer-time-tracker
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Set up environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure database** (SQLite is pre-configured)
   ```bash
   touch database/database.sqlite
   ```

6. **Run migrations**
   ```bash
   php artisan migrate
   ```

7. **Build frontend assets**
   ```bash
   npm run build
   ```

8. **Set up cron job** (for automated backups and scheduled emails)

   See [CRON_SETUP.md](CRON_SETUP.md) for detailed instructions, or add this to your crontab:
   ```bash
   * * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
   ```

## Development

### Start Development Server

Run all development services concurrently:
```bash
composer run dev
```

This starts:
- **Laravel development server** (http://localhost:8000)
- **Queue worker** (for background jobs)
- **Log viewer (Pail)** (real-time log monitoring)
- **Vite asset bundler** (hot module replacement)

### Individual Services

Run services separately:
```bash
# Laravel server only
php artisan serve

# Frontend assets only
npm run dev

# Queue worker only
php artisan queue:listen

# Log viewer only
php artisan pail
```

## API Documentation

The application provides a RESTful API with the following endpoints:

### Time Tracking API
- `POST /api/timesheet/clock-in` - Start a new time session
- `POST /api/timesheet/clock-out` - End active session
- `GET /api/timesheet/active-session` - Get current active session
- `GET /api/timesheet/history` - Get time log history
- `GET /api/timesheet/dashboard-stats` - Get analytics
- `GET /api/timesheet/export-excel` - Export to Excel

### Project API
- `GET /api/projects` - List all projects
- `POST /api/projects` - Create new project
- `PUT /api/projects/{id}` - Update project
- `GET /api/projects/{id}/stats` - Get project statistics
- `GET /api/projects/{id}/backup` - Backup project data

### Invoice API
- `GET /api/invoices` - List invoices with filters
- `POST /api/invoices` - Create invoice from time logs
- `POST /api/invoices/{id}/send-email` - Send invoice via email
- `POST /api/invoices/{id}/mark-as-paid` - Mark invoice as paid
- `GET /api/invoices/{id}/pdf/download` - Download PDF
- `GET /api/invoices/stats` - Get revenue statistics

### Settings API
- `GET /api/settings` - Get all settings
- `POST /api/settings` - Update settings
- `POST /api/settings/test-email` - Test email configuration

## Automated Tasks

The application includes scheduled tasks that run automatically:

- **Database Backups**: Every 2 days at 2:00 AM
- **Scheduled Invoice Emails**: Checked every minute
- **Backup Cleanup**: Removes backups older than 30 days

View scheduled tasks:
```bash
php artisan schedule:list
```

Run scheduler manually:
```bash
php artisan schedule:run
```

## Artisan Commands

### Backup Commands
```bash
# Create full database backup
php artisan backup:database

# Backup specific project
php artisan backup:projects --project-id=1
```

### Invoice Commands
```bash
# Send scheduled invoice emails
php artisan invoices:send-scheduled

# Send invoice reminders
php artisan invoices:send-reminders
```

## Testing

Run the test suite:
```bash
composer run test
# or
php artisan test
```

Run with coverage:
```bash
php artisan test --coverage
```

## Configuration

### Email Setup

Configure email settings in `.env` or via the Settings page in the application:

```env
# Default mailer configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="Your Company Name"
```

Alternatively, use the Settings page to configure SMTP without editing `.env`.

### Invoice Settings

Configure invoice details via the Settings page:
- Company name and address
- Tax number (HST/GST)
- Payment instructions (e-transfer, bank info)
- Email templates

### Database Configuration

Default configuration uses SQLite. To use MySQL/PostgreSQL, update `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=time_tracker
DB_USERNAME=root
DB_PASSWORD=
```

## Deployment

### Production Checklist

1. **Set environment to production**
   ```env
   APP_ENV=production
   APP_DEBUG=false
   ```

2. **Optimize application**
   ```bash
   composer install --optimize-autoloader --no-dev
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   php artisan event:cache
   ```

3. **Build production assets**
   ```bash
   npm run build
   ```

4. **Set up cron job** (see [CRON_SETUP.md](CRON_SETUP.md))

5. **Configure web server** (Apache/Nginx)
   - Point document root to `/public` directory
   - Enable `.htaccess` (Apache) or configure redirects (Nginx)

6. **Set proper permissions**
   ```bash
   chmod -R 755 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache
   ```

7. **Configure SSL/HTTPS** (recommended for production)

### Cron Job Setup

The application requires a cron job for scheduled tasks:

**Linux/Unix/macOS:**
```bash
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

**Windows Task Scheduler:**
See [CRON_SETUP.md](CRON_SETUP.md) for detailed Windows setup instructions.

### Hostinger/Shared Hosting

For URL-based cron jobs (common on shared hosting):

1. Generate a secure token:
   ```bash
   php -r "echo substr(md5(config('app.key')), 0, 16);"
   ```

2. Set up a cron job to call:
   ```
   https://yourdomain.com/cron/run/{your-token}
   ```

## Usage Guide

### Getting Started

1. **Access the application** at `http://localhost:8000`

2. **Create a project**
   - Navigate to Projects
   - Click "New Project"
   - Enter client details, hourly rate, and tax settings

3. **Track time**
   - Go to Timesheet
   - Select a project
   - Click "Clock In" to start tracking
   - Click "Clock Out" when done
   - Add work description

4. **Generate invoices**
   - Navigate to Invoices
   - Click "New Invoice"
   - Select unbilled time logs or add custom items
   - Review and send to client

5. **Configure settings**
   - Go to Settings
   - Set up company information
   - Configure email delivery
   - Test email configuration

### Common Workflows

#### Creating an Invoice from Time Logs

1. Track time entries for a project
2. Navigate to Invoices → New Invoice
3. Select the project
4. Choose unbilled time logs to include
5. Review invoice details and items
6. Set invoice date and due date
7. Add custom notes or payment instructions
8. Save as draft or send directly to client

#### Sending Invoices via Email

1. Open an invoice (draft or sent status)
2. Click "Send Email"
3. Verify client email address
4. Customize email subject and message
5. Optionally schedule for future delivery
6. Send immediately or schedule

#### Tracking Invoice Opens

When you send an invoice via email:
- A tracking pixel is automatically embedded
- You'll see when the client opens the email
- View open count and timestamp in invoice history
- Track IP address and user agent

#### Managing Projects

**Active Projects**: Currently billable projects
**Archived Projects**: Completed or inactive projects (can be reactivated)

Projects with time logs cannot be deleted, only archived.

## Backup & Recovery

### Automatic Backups

- **Frequency**: Every 2 days at 2:00 AM
- **Location**: `storage/app/backups/`
- **Format**: SQL dump files
- **Retention**: 30 days (automatic cleanup)

### Manual Backups

**Full database backup:**
```bash
php artisan backup:database
```

**Individual project backup:**
```bash
php artisan backup:projects --project-id=1
```

**Download via UI:**
- Navigate to Settings → Backups
- Click "Create Backup" for on-demand backup
- Download or delete backups as needed

### Restore from Backup

**SQLite:**
```bash
cp storage/app/backups/database-*.sql database/database.sqlite
```

**MySQL/PostgreSQL:**
```bash
mysql -u username -p database_name < storage/app/backups/database-*.sql
```

## Troubleshooting

### Email Not Sending

1. **Test email configuration**
   ```bash
   php artisan settings:test-email your-email@example.com
   ```
   Or use the "Test Email" button in Settings.

2. **Check email logs**
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. **Verify SMTP settings** match your email provider's requirements

4. **For Gmail**: Use App Passwords instead of regular password

### Cron Jobs Not Running

1. **Verify cron service is running:**
   ```bash
   sudo service cron status
   ```

2. **Check crontab is set:**
   ```bash
   crontab -l
   ```

3. **Test scheduler manually:**
   ```bash
   php artisan schedule:run
   ```

4. **View cron logs:**
   ```bash
   grep CRON /var/log/syslog
   ```

### Permission Issues

```bash
# Fix storage permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Or use current user
chown -R $USER:$USER storage bootstrap/cache
```

### Database Connection Failed

1. **Clear configuration cache:**
   ```bash
   php artisan config:clear
   ```

2. **Verify database file exists (SQLite):**
   ```bash
   ls -la database/database.sqlite
   ```

3. **Create database file if missing:**
   ```bash
   touch database/database.sqlite
   php artisan migrate
   ```

## Contributing

Contributions are welcome! Please follow these guidelines:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## Security

If you discover any security-related issues, please email security@yourdomain.com instead of using the issue tracker.

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

- **Documentation**: See [CRON_SETUP.md](CRON_SETUP.md) for cron job setup
- **Issues**: Report bugs via GitHub Issues
- **Laravel Documentation**: https://laravel.com/docs

## Credits

Built with:
- [Laravel](https://laravel.com) - The PHP Framework
- [Tailwind CSS](https://tailwindcss.com) - Utility-first CSS framework
- [Alpine.js](https://alpinejs.dev) - Lightweight JavaScript framework
- [DomPDF](https://github.com/barryvdh/laravel-dompdf) - PDF generation
- [Maatwebsite Excel](https://laravel-excel.com) - Excel import/export
