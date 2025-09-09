<?php

namespace App\Http\Controllers\Saas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    public function index()
    {
        // Get current settings from config files and .env
        $settings = [
            'branding' => [
                'app_name' => config('app.name', 'Hospital Management'),
                'app_url' => config('app.url', 'http://localhost'),
                'favicon' => $this->getSetting('favicon'),
                'logo' => $this->getSetting('logo'),
            ],
            'smtp' => [
                'mail_host' => config('mail.mailers.smtp.host'),
                'mail_port' => config('mail.mailers.smtp.port'),
                'mail_username' => config('mail.mailers.smtp.username'),
                'mail_password' => $this->maskPassword(config('mail.mailers.smtp.password')),
                'mail_encryption' => config('mail.mailers.smtp.encryption'),
                'mail_from_name' => config('mail.from.name'),
                'mail_from_address' => config('mail.from.address'),
            ],
            'sms' => [
                'sms_provider' => $this->getSetting('sms_provider', 'twilio'),
                'twilio_sid' => $this->getSetting('twilio_sid'),
                'twilio_token' => $this->maskPassword($this->getSetting('twilio_token')),
                'twilio_phone' => $this->getSetting('twilio_phone'),
            ],
            'payment' => [
                'stripe_public_key' => substr(config('services.stripe.key'), 0, 10) . '...',
                'stripe_secret_key' => substr(config('services.stripe.secret'), 0, 10) . '...',
                'paypal_client_id' => substr(config('services.paypal.client_id') ?: '', 0, 10) . '...',
                'paypal_secret' => substr(config('services.paypal.secret') ?: '', 0, 10) . '...',
                'currency' => config('services.currency', 'USD'),
            ],
            'locale' => [
                'app_locale' => config('app.locale', 'en'),
                'timezone' => config('app.timezone', 'UTC'),
                'date_format' => $this->getSetting('date_format', 'Y-m-d'),
            ],
            'system' => [
                'maintenance_mode' => app()->isDownForMaintenance(),
                'debug_mode' => config('app.debug', false),
            ]
        ];

        return view('saas.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'app_name' => 'required|string|max:255',
            'app_locale' => 'required|string',
            'timezone' => 'required|string',
            'currency' => 'required|string|size:3',
            'date_format' => 'required|string',
            'mail_host' => 'nullable|string',
            'mail_port' => 'nullable|integer',
            'mail_username' => 'nullable|string',
            'mail_password' => 'nullable|string',
            'mail_from_name' => 'nullable|string',
            'mail_from_address' => 'nullable|email',
            'mail_encryption' => 'nullable|string',
        ]);

        // Here you would normally save to database or update .env
        // For demo purposes, we'll just return success
        return redirect()->route('saas.settings.index')
            ->with('success', 'Settings updated successfully. Note: In production, these would be stored in the database.');
    }

    private function getSetting($key, $default = null)
    {
        // This is a simple implementation
        // In production, you'd have a proper settings table/caching system
        return config("saas_settings.{$key}", $default);
    }

    private function maskPassword($password)
    {
        if (!$password) return null;
        return str_repeat('*', strlen($password));
    }
}