<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display the Stripe checkout result page.
     */
    public function stripeResult(Request $request)
    {
        $status = strtolower($request->query('status', 'success'));
        $invoiceId = $request->query('invoice');
        $invoice = null;

        if ($invoiceId) {
            $invoice = Invoice::with('project')->find($invoiceId);
        }

        return view('payments.stripe-result', [
            'status' => $status === 'failed' ? 'failed' : 'success',
            'invoice' => $invoice,
        ]);
    }
}
