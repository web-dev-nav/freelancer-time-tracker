<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Return application settings.
     */
    public function index()
    {
        $defaults = [
            'invoice_company_name'   => config('app.name'),
            'invoice_company_address'=> null,
            'invoice_tax_number'     => null,
            'email_mailer'           => 'default',
            'email_smtp_host'        => null,
            'email_smtp_port'        => null,
            'email_smtp_username'    => null,
            'email_smtp_password'    => null,
            'email_smtp_encryption'  => null,
            'email_from_address'     => config('mail.from.address'),
            'email_from_name'        => config('mail.from.name'),
        ];

        try {
            $settings = Setting::getValues(array_keys($defaults), $defaults);
        } catch (\Throwable $e) {
            report($e);
            $settings = $defaults;
        }

        return response()->json([
            'success' => true,
            'data' => $settings,
        ]);
    }

    /**
     * Update application settings.
     */
    public function update(Request $request)
    {
        $defaults = [
            'invoice_company_name'   => config('app.name'),
            'invoice_company_address'=> null,
            'invoice_tax_number'     => null,
            'email_mailer'           => 'default',
            'email_smtp_host'        => null,
            'email_smtp_port'        => null,
            'email_smtp_username'    => null,
            'email_smtp_password'    => null,
            'email_smtp_encryption'  => null,
            'email_from_address'     => config('mail.from.address'),
            'email_from_name'        => config('mail.from.name'),
        ];

        $keys = array_keys($defaults);

        try {
            $validated = $request->validate([
                'invoice_company_name'    => 'nullable|string|max:255',
                'invoice_company_address' => 'nullable|string|max:2000',
                'invoice_tax_number'      => 'nullable|string|max:255',
                'email_mailer'            => 'nullable|in:default,smtp',
                'email_smtp_host'         => 'nullable|string|max:255',
                'email_smtp_port'         => 'nullable|integer|min:1|max:65535',
                'email_smtp_username'     => 'nullable|string|max:255',
                'email_smtp_password'     => 'nullable|string|max:255',
                'email_smtp_encryption'   => 'nullable|in:tls,ssl,starttls',
                'email_from_address'      => 'nullable|email|max:255',
                'email_from_name'         => 'nullable|string|max:255',
            ]);

            foreach ($keys as $key) {
                if (!array_key_exists($key, $validated)) {
                    continue;
                }

                $value = $validated[$key];

                if ($value === null) {
                    Setting::setValue($key, null);
                    continue;
                }

                $sanitized = trim($value);
                Setting::setValue($key, $sanitized !== '' ? $sanitized : null);
            }

            $data = Setting::getValues($keys, $defaults);

            return response()->json([
                'success' => true,
                'message' => 'Settings updated successfully',
                'data' => $data,
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Unable to save settings at the moment.',
                'data' => $defaults,
            ]);
        }
    }
}
