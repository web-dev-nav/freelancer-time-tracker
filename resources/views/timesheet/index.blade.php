@extends('layouts.app')

@section('title', 'Professional Timesheet')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/timesheet/main.css') }}">
@endpush

@section('content')
<div id="timesheet-app">
    {{-- Project Selector --}}
    @include('components.timesheet.project-selector')

    {{-- Navigation Tabs --}}
    @include('components.timesheet.nav-tabs')

    {{-- Tab Contents --}}
    @include('components.timesheet.tabs.dashboard')
    @include('components.timesheet.tabs.history')
    @include('components.timesheet.tabs.reports')
    @include('components.timesheet.tabs.projects')
    @include('components.timesheet.tabs.backups')

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
@include('components.timesheet.modals.project')

{{-- Modal Overlay --}}
<div id="modal-overlay" class="modal-overlay"></div>
@endpush

@push('scripts')
    <script type="module" src="{{ asset('js/timesheet/index.js') }}"></script>
@endpush
