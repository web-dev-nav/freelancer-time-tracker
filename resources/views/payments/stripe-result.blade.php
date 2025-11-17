<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $status === 'success' ? 'Payment Successful' : 'Payment Failed' }} | {{ config('app.name') }}</title>
    <style>
        :root {
            color-scheme: light dark;
        }
        body {
            margin: 0;
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: #f3f4f6;
            color: #111827;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .container {
            background: #ffffff;
            padding: 48px 40px;
            border-radius: 18px;
            box-shadow: 0 25px 70px rgba(15, 23, 42, 0.15);
            max-width: 540px;
            width: 90%;
            text-align: center;
        }
        .status-icon {
            width: 86px;
            height: 86px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 38px;
            margin-bottom: 24px;
        }
        .status-success {
            background: rgba(34, 197, 94, 0.12);
            color: #16a34a;
        }
        .status-failed {
            background: rgba(239, 68, 68, 0.12);
            color: #ef4444;
        }
        h1 {
            margin: 0 0 16px;
            font-size: 28px;
            color: #111827;
        }
        p {
            margin: 0 0 12px;
            font-size: 15px;
            color: #4b5563;
            line-height: 1.6;
        }
        .card {
            margin: 26px 0;
            border-radius: 14px;
            padding: 16px 20px;
            background: linear-gradient(135deg, #eef2ff, #e0e7ff);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.6);
            text-align: left;
        }
        .card h2 {
            margin: 0 0 8px;
            font-size: 16px;
            color: #1f2937;
        }
        .card div {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            color: #374151;
            margin-bottom: 6px;
        }
        .button-row {
            margin-top: 32px;
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .button {
            padding: 12px 22px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .button-primary {
            background: linear-gradient(135deg, #4f46e5, #6366f1);
            color: #ffffff;
            box-shadow: 0 12px 30px rgba(99, 102, 241, 0.25);
        }
        .button-secondary {
            background: #e5e7eb;
            color: #1f2937;
        }
        .button:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 32px rgba(99, 102, 241, 0.32);
        }
        footer {
            margin-top: 40px;
            font-size: 12px;
            color: #9ca3af;
        }
        @media (max-width: 640px) {
            .container {
                padding: 36px 24px;
            }
            h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="status-icon {{ $status === 'success' ? 'status-success' : 'status-failed' }}">
            {!! $status === 'success' ? '&#10003;' : '&#10007;' !!}
        </div>
        <h1>{{ $status === 'success' ? 'Payment Completed' : 'Payment Interrupted' }}</h1>

        <p>
            {{ $status === 'success'
                ? 'Thank you! Your payment has been received and is being processed.'
                : 'We were unable to confirm your payment. You can try again or choose another payment method.' }}
        </p>

        @if($invoice)
            <div class="card">
                <h2>Invoice {{ $invoice->invoice_number }}</h2>
                <div>
                    <span>Client</span>
                    <strong>{{ $invoice->client_name ?? 'N/A' }}</strong>
                </div>
                <div>
                    <span>Amount</span>
                    <strong>${{ number_format($invoice->total, 2) }}</strong>
                </div>
                <div>
                    <span>Status</span>
                    <strong style="text-transform: uppercase;">{{ $invoice->status }}</strong>
                </div>
            </div>
        @endif

        <p>
            We'll email you a receipt once everything is confirmed. If you have questions,
            please contact {{ config('mail.from.address') ?? 'our team' }}.
        </p>

        <div class="button-row">
            <a href="{{ route('timesheet.index') }}" class="button button-primary">
                &larr; Return to Dashboard
            </a>
            <a href="{{ route('timesheet.index') }}#invoices" class="button button-secondary">
                View Invoices
            </a>
        </div>

        <footer>
            {{ config('app.name') }} &middot; Secure payments powered by Stripe
        </footer>
    </div>
</body>
</html>
