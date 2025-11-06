<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
            padding: 40px;
        }

        .invoice-header {
            margin-bottom: 40px;
            display: table;
            width: 100%;
        }

        .company-details {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .invoice-details {
            display: table-cell;
            width: 50%;
            text-align: right;
            vertical-align: top;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #8b5cf6;
            margin-bottom: 10px;
        }

        .invoice-title {
            font-size: 32px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 10px;
        }

        .invoice-number {
            font-size: 16px;
            color: #6b7280;
            margin-bottom: 5px;
        }

        .invoice-status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 10px;
        }

        .status-draft {
            background-color: #f3f4f6;
            color: #6b7280;
        }

        .status-sent {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .status-paid {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-cancelled {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .status-overdue {
            background-color: #fef3c7;
            color: #92400e;
        }

        .client-details {
            margin: 30px 0;
            padding: 20px;
            background-color: #f9fafb;
            border-left: 4px solid #8b5cf6;
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 8px;
        }

        .client-name {
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 5px;
        }

        .client-info {
            color: #6b7280;
            font-size: 13px;
        }

        .dates-row {
            display: table;
            width: 100%;
            margin: 30px 0;
        }

        .date-item {
            display: table-cell;
            width: 50%;
        }

        .date-label {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 4px;
        }

        .date-value {
            font-size: 16px;
            font-weight: bold;
            color: #1f2937;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
        }

        .items-table thead {
            background-color: #1f2937;
            color: white;
        }

        .items-table th {
            padding: 12px;
            text-align: left;
            font-size: 12px;
            text-transform: uppercase;
            font-weight: bold;
        }

        .items-table th:last-child,
        .items-table td:last-child {
            text-align: right;
        }

        .items-table tbody tr {
            border-bottom: 1px solid #e5e7eb;
        }

        .items-table tbody tr:last-child {
            border-bottom: 2px solid #1f2937;
        }

        .items-table td {
            padding: 12px;
            font-size: 13px;
        }

        .item-description {
            color: #6b7280;
            font-size: 12px;
            margin-top: 4px;
        }

        .totals-section {
            margin: 30px 0;
            text-align: right;
        }

        .totals-table {
            display: inline-block;
            min-width: 300px;
        }

        .totals-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }

        .totals-label {
            display: table-cell;
            text-align: left;
            padding: 8px 0;
            font-size: 14px;
            color: #6b7280;
        }

        .totals-value {
            display: table-cell;
            text-align: right;
            padding: 8px 0;
            font-size: 14px;
            font-weight: bold;
        }

        .totals-total {
            border-top: 2px solid #1f2937;
            padding-top: 12px;
            margin-top: 8px;
        }

        .totals-total .totals-label {
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
        }

        .totals-total .totals-value {
            font-size: 24px;
            color: #8b5cf6;
        }

        .notes-section {
            margin: 40px 0;
            padding: 20px;
            background-color: #f9fafb;
            border-radius: 8px;
        }

        .notes-title {
            font-size: 14px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 8px;
        }

        .notes-content {
            color: #6b7280;
            font-size: 13px;
            line-height: 1.8;
        }

        .footer {
            margin-top: 60px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #9ca3af;
            font-size: 12px;
        }
    </style>
</head>
<body>
    @php
        $companySettings = $companySettings ?? [];
        $formatAddress = function ($value) {
            if (!$value) {
                return null;
            }
            $normalized = str_replace(['<br>', '<br/>', '<br />'], "\n", $value);
            return nl2br(e($normalized));
        };

        $companyName = $invoice->company_name
            ?? $companySettings['invoice_company_name']
            ?? optional($invoice->project)->name
            ?? config('app.name');

        $companyAddress = $formatAddress(
            $invoice->company_address ?? ($companySettings['invoice_company_address'] ?? null)
        );
        $taxNumber = $companySettings['invoice_tax_number'] ?? null;
    @endphp
    <div class="invoice-header">
        <div class="company-details">
            <div class="company-name">{{ $companyName }}</div>
            @if($companyAddress)
                <div class="client-info">{!! $companyAddress !!}</div>
            @endif
            @if($taxNumber)
                <div class="client-info">GST/HST: {{ $taxNumber }}</div>
            @endif
        </div>
        <div class="invoice-details">
            <div class="invoice-title">INVOICE</div>
            <div class="invoice-number">#{{ $invoice->invoice_number }}</div>
            <span class="invoice-status status-{{ $invoice->status }}{{ $invoice->is_overdue ? ' status-overdue' : '' }}">
                {{ $invoice->is_overdue ? 'Overdue' : ucfirst($invoice->status) }}
            </span>
        </div>
    </div>

    <div class="client-details">
        <div class="section-title">Bill To</div>
        <div class="client-name">{{ $invoice->client_name }}</div>
        @if($invoice->client_email)
            <div class="client-info">{{ $invoice->client_email }}</div>
        @endif
        @if($invoice->client_address)
            <div class="client-info">{!! $formatAddress($invoice->client_address) !!}</div>
        @endif
    </div>

    <div class="dates-row">
        <div class="date-item">
            <div class="date-label">Invoice Date</div>
            <div class="date-value">{{ $invoice->formatted_invoice_date }}</div>
        </div>
        <div class="date-item" style="text-align: right;">
            <div class="date-label">Due Date</div>
            <div class="date-value">{{ $invoice->formatted_due_date }}</div>
        </div>
    </div>

    @if($invoice->description)
        <div style="margin: 20px 0; padding: 15px; background-color: #f0f9ff; border-left: 4px solid #3b82f6;">
            <div style="font-size: 12px; color: #6b7280; margin-bottom: 5px;">PROJECT DESCRIPTION</div>
            <div style="color: #1f2937; font-size: 14px;">{{ $invoice->description }}</div>
        </div>
    @endif

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 20%;">Date</th>
                <th style="width: 45%;">Description</th>
                <th style="width: 10%;">Qty</th>
                <th style="width: 12%;">Rate</th>
                <th style="width: 13%;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoice->items as $item)
                <tr>
                    <td>{{ $item->formatted_work_date }}</td>
                    <td>
                        {{ $item->description }}
                    </td>
                    <td>{{ number_format($item->hours, 2) }}</td>
                    <td>${{ number_format($item->rate, 2) }}</td>
                    <td>${{ number_format($item->amount, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 30px; color: #9ca3af;">
                        No items in this invoice
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="totals-section">
        <div class="totals-table">
            <div class="totals-row">
                <div class="totals-label">Subtotal</div>
                <div class="totals-value">${{ number_format($invoice->subtotal, 2) }}</div>
            </div>
            @if($invoice->tax_rate > 0)
                <div class="totals-row">
                    <div class="totals-label">Tax ({{ number_format($invoice->tax_rate, 2) }}%)</div>
                    <div class="totals-value">${{ number_format($invoice->tax_amount, 2) }}</div>
                </div>
            @endif
            <div class="totals-row totals-total">
                <div class="totals-label">TOTAL</div>
                <div class="totals-value">${{ number_format($invoice->total, 2) }}</div>
            </div>
        </div>
    </div>

    @if($invoice->notes)
        <div class="notes-section">
            <div class="notes-title">Notes & Terms</div>
            <div class="notes-content">{{ $invoice->notes }}</div>
        </div>
    @endif

    <div class="footer">
        Generated by {{ config('app.name') }} on {{ now()->format('F d, Y') }}<br>
        Thank you for your business!
    </div>
</body>
</html>
