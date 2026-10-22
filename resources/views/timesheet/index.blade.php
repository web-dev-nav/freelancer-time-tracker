@extends('layouts.app')

@section('title', 'Professional Timesheet')
@section('main-anchor', 'workspace-title')

@push('styles')
    @include('components.timesheet-styles')
    <link rel="stylesheet" href="{{ asset_version('vendor/quill/quill.snow.css') }}">
@endpush

@section('content')
<div id="timesheet-app">
    @include('components.timesheet.nav-tabs')
    <div class="workspace-content">
        <div class="workspace-heading"><div><p class="eyebrow">YOUR WORKSPACE</p><h1 id="workspace-title" tabindex="-1">Overview</h1><p id="workspace-description">A clear view of your time. A little more room to focus.</p></div><span class="workspace-date" id="workspace-date"></span></div>
        @include('components.timesheet.project-selector')
    {{-- Tab Contents --}}
    @include('components.timesheet.tabs.dashboard')
    @include('components.timesheet.tabs.history')
    @include('components.timesheet.tabs.reports')
    @if(auth()->user()?->isAuthor())
        @include('components.timesheet.tabs.projects')
    @endif
    @include('components.timesheet.tabs.invoices')
    @if(auth()->user()?->isAuthor())
        @include('components.timesheet.tabs.backups')
    @endif

    </div>

    {{-- Hidden form for clock-in (used by Quick Clock In) --}}
    <form id="clock-in-form" style="display: none;">
        <input type="date" id="clock-in-date">
        <input type="time" id="clock-in-time">
    </form>
</div>
@endsection

{{-- Modals (outside main-content wrapper) --}}
@push('modals')
@include('components.timesheet.modals.clock-out')
@include('components.timesheet.modals.edit-log')
@include('components.timesheet.modals.view-details')
@if(auth()->user()?->isAuthor())
    @include('components.timesheet.modals.project')
@endif
@include('components.timesheet.modals.create-invoice')
@include('components.timesheet.modals.send-invoice')
@include('components.timesheet.modals.edit-invoice')
@include('components.timesheet.modals.add-invoice-item')
@include('components.timesheet.modals.invoice-history')
@if(auth()->user()?->isAuthor())
    @include('components.timesheet.modals.settings')
@endif

{{-- Modal Overlay --}}
<div id="modal-overlay" class="modal-overlay"></div>
@endpush

@push('scripts')
    <script src="{{ asset_version('vendor/quill/quill.js') }}"></script>
    <script>
        setTimeout(() => {
            if (window.timesheetInitialized) return;
            const panel = document.getElementById('project-load-error');
            if (panel) { panel.hidden = false; panel.querySelector('span').textContent = 'The workspace could not start. Reload to fetch the latest application files.'; }
            const selector = document.getElementById('project-selector');
            if (selector) selector.replaceChildren(new Option('Workspace unavailable — retry below', ''));
            const recent = document.getElementById('recent-work-list');
            if (recent) recent.textContent = 'Recent work could not load because the workspace did not start.';
        }, 15000);
    </script>
    <script type="module" src="{{ asset_version('js/timesheet/index.js') }}"></script>
@endpush
