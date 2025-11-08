@extends('layouts.app')

@section('title', 'Settings')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/timesheet/main.css') }}">
    <style>
        .settings-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px 20px;
        }

        .settings-header {
            margin-bottom: 30px;
        }

        .settings-header h1 {
            font-size: 28px;
            font-weight: 600;
            color: #1f2937;
            margin: 0 0 8px 0;
        }

        .settings-header p {
            color: #6b7280;
            font-size: 14px;
            margin: 0;
        }

        .settings-tabs {
            display: flex;
            gap: 8px;
            border-bottom: 2px solid #e5e7eb;
            margin-bottom: 30px;
            overflow-x: auto;
        }

        .settings-tab {
            padding: 12px 24px;
            background: none;
            border: none;
            border-bottom: 3px solid transparent;
            color: #6b7280;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
            margin-bottom: -2px;
        }

        .settings-tab:hover {
            color: #374151;
            background: #f9fafb;
        }

        .settings-tab.active {
            color: #8b5cf6;
            border-bottom-color: #8b5cf6;
        }

        .settings-tab i {
            margin-right: 8px;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
            animation: fadeIn 0.3s;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .settings-section {
            background: white;
            border-radius: 8px;
            padding: 24px;
            margin-bottom: 24px;
            border: 1px solid #e5e7eb;
        }

        .section-header {
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 1px solid #e5e7eb;
        }

        .section-header h2 {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
            margin: 0 0 4px 0;
        }

        .section-header p {
            color: #6b7280;
            font-size: 13px;
            margin: 0;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: #f3f4f6;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            color: #374151;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
            margin-bottom: 20px;
        }

        .back-button:hover {
            background: #e5e7eb;
            color: #1f2937;
        }

        .save-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-top: 2px solid #e5e7eb;
            padding: 16px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
            box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .save-bar-content {
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .save-bar-message {
            display: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
        }

        .save-bar-message.success {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #10b981;
        }

        .save-bar-message.error {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #ef4444;
        }

        @media (max-width: 768px) {
            .settings-container {
                padding: 20px 15px;
            }

            .settings-tabs {
                gap: 4px;
            }

            .settings-tab {
                padding: 10px 16px;
                font-size: 13px;
            }

            .settings-section {
                padding: 16px;
            }
        }
    </style>
@endpush

@section('content')
<div class="settings-container">
    <a href="{{ route('timesheet.index') }}" class="back-button">
        <i class="fas fa-arrow-left"></i>
        Back to Timesheet
    </a>

    <div class="settings-header">
        <h1><i class="fas fa-cog"></i> Settings</h1>
        <p>Manage your application settings, email configuration, and automation</p>
    </div>

    {{-- Tabs --}}
    <div class="settings-tabs">
        <button class="settings-tab active" onclick="switchTab('general')">
            <i class="fas fa-building"></i>
            General
        </button>
        <button class="settings-tab" onclick="switchTab('email')">
            <i class="fas fa-envelope"></i>
            Email
        </button>
        <button class="settings-tab" onclick="switchTab('payment')">
            <i class="fas fa-money-check-alt"></i>
            Payment
        </button>
        <button class="settings-tab" onclick="switchTab('automation')">
            <i class="fas fa-clock"></i>
            Automation
        </button>
    </div>

    <form id="settings-form">
        {{-- General Tab --}}
        <div class="tab-content active" id="general-tab">
            @include('settings.tabs.general')
        </div>

        {{-- Email Tab --}}
        <div class="tab-content" id="email-tab">
            @include('settings.tabs.email')
        </div>

        {{-- Payment Tab --}}
        <div class="tab-content" id="payment-tab">
            @include('settings.tabs.payment')
        </div>

        {{-- Automation Tab --}}
        <div class="tab-content" id="automation-tab">
            @include('settings.tabs.automation')
        </div>
    </form>
</div>

{{-- Save Bar --}}
<div class="save-bar">
    <div class="save-bar-content">
        <div id="save-message" class="save-bar-message"></div>
        <div>
            <button type="button" class="btn btn-secondary" onclick="window.location.href='{{ route('timesheet.index') }}'">
                Cancel
            </button>
            <button type="submit" form="settings-form" class="btn btn-primary">
                <i class="fas fa-save"></i>
                Save All Settings
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script type="module" src="{{ asset('js/settings/index.js') }}?v={{ time() }}"></script>
@endpush
