@foreach(['variables', 'layout', 'navigation', 'backups', 'dashboard', 'forms', 'history', 'invoices', 'modals', 'project-selector', 'projects', 'reports', 'settings', 'tracker', 'utilities'] as $stylesheet)
    <link rel="stylesheet" href="{{ asset_version('css/timesheet/'.$stylesheet.'.css') }}">
@endforeach
