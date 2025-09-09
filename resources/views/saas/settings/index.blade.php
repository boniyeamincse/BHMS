<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('System Settings') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Success/Error Messages -->
            @if(session('success'))
            <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4">
                {{ session('success') }}
            </div>
            @endif

            <form method="POST">
                @csrf
                @method('PATCH')

                <!-- Branding Settings -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Branding Settings</h3>
                        <p class="mt-1 text-sm text-gray-600">Configure your app's branding and appearance</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">App Name</label>
                                <input type="text" name="app_name" value="{{ old('app_name', $settings['branding']['app_name']) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">App URL</label>
                                <input type="url" name="app_url" value="{{ old('app_url', $settings['branding']['app_url']) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="text-sm text-gray-600">
                                <strong>Current Favicon:</strong> {{ $settings['branding']['favicon'] ?? 'Default' }}<br>
                                <strong>Current Logo:</strong> {{ $settings['branding']['logo'] ?? 'Default' }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SMTP Settings -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">SMTP Mail Settings</h3>
                        <p class="mt-1 text-sm text-gray-600">Configure email sending settings</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Host</label>
                                <input type="text" name="mail_host" value="{{ old('mail_host', $settings['smtp']['mail_host']) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Port</label>
                                <input type="number" name="mail_port" value="{{ old('mail_port', $settings['smtp']['mail_port']) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Encryption</label>
                                <select name="mail_encryption" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="tls" {{ ($settings['smtp']['mail_encryption'] ?? '') === 'tls' ? 'selected' : '' }}>TLS</option>
                                    <option value="ssl" {{ ($settings['smtp']['mail_encryption'] ?? '') === 'ssl' ? 'selected' : '' }}>SSL</option>
                                    <option value="">None</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Username</label>
                                <input type="text" name="mail_username" value="{{ old('mail_username', $settings['smtp']['mail_username']) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Password</label>
                                <input type="password" name="mail_password" value="" placeholder="Enter new password if changing" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <p class="mt-1 text-xs text-gray-500">Leave empty to keep current password</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">From Name</label>
                                <input type="text" name="mail_from_name" value="{{ old('mail_from_name', $settings['smtp']['mail_from_name']) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">From Email</label>
                                <input type="email" name="mail_from_address" value="{{ old('mail_from_address', $settings['smtp']['mail_from_address']) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SMS Settings -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">SMS Settings</h3>
                        <p class="mt-1 text-sm text-gray-600">Configure SMS service settings (Twilio)</p>
                    </div>
                    <div class="p-6">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">SMS Provider</label>
                            <div class="mt-1 text-sm text-gray-900">{{ $settings['sms']['sms_provider'] ?? 'Twilio' }}</div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Twilio SID</label>
                                <div class="mt-1 text-sm text-gray-900">{{ $settings['sms']['twilio_sid'] ?? 'Not configured' }}</div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Twilio Token</label>
                                <div class="mt-1 text-sm text-gray-900">{{ $settings['sms']['twilio_token'] ?? 'Not configured' }}</div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700">Twilio Phone Number</label>
                            <div class="mt-1 text-sm text-gray-900">{{ $settings['sms']['twilio_phone'] ?? 'Not configured' }}</div>
                        </div>
                    </div>
                </div>

                <!-- Payment Settings -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Payment Settings</h3>
                        <p class="mt-1 text-sm text-gray-600">Configure payment gateways and currency</p>
                    </div>
                    <div class="p-6">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Default Currency</label>
                            <input type="text" name="currency" value="{{ old('currency', $settings['payment']['currency']) }}" placeholder="USD" class="mt-1 block w-full md:w-48 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div class="space-y-4">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">Stripe Configuration</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500">Public Key</label>
                                        <div class="mt-1 text-sm text-gray-900">{{ $settings['payment']['stripe_public_key'] ?? 'Not configured' }}</div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500">Secret Key</label>
                                        <div class="mt-1 text-sm text-gray-900">{{ $settings['payment']['stripe_secret_key'] ?? 'Not configured' }}</div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">PayPal Configuration</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500">Client ID</label>
                                        <div class="mt-1 text-sm text-gray-900">{{ $settings['payment']['paypal_client_id'] ?: 'Not configured' }}</div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500">Secret</label>
                                        <div class="mt-1 text-sm text-gray-900">{{ $settings['payment']['paypal_secret'] ?: 'Not configured' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Locale & System Settings -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Locale & System Settings</h3>
                        <p class="mt-1 text-sm text-gray-600">Configure app locale, timezone and formats</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">App Locale</label>
                                <select name="app_locale" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="en" {{ $settings['locale']['app_locale'] === 'en' ? 'selected' : '' }}>English</option>
                                    <option value="es" {{ $settings['locale']['app_locale'] === 'es' ? 'selected' : '' }}>Spanish</option>
                                    <option value="fr" {{ $settings['locale']['app_locale'] === 'fr' ? 'selected' : '' }}>French</option>
                                    <option value="de" {{ $settings['locale']['app_locale'] === 'de' ? 'selected' : '' }}>German</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Timezone</label>
                                <input type="text" name="timezone" value="{{ old('timezone', $settings['locale']['timezone']) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Date Format</label>
                                <select name="date_format" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="Y-m-d" {{ ($settings['locale']['date_format'] ?? old('date_format')) === 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                                    <option value="d/m/Y" {{ ($settings['locale']['date_format'] ?? old('date_format')) === 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                                    <option value="m/d/Y" {{ ($settings['locale']['date_format'] ?? old('date_format')) === 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-6">
                            <h4 class="text-sm font-medium text-gray-900 mb-3">System Settings</h4>
                            <div class="space-y-3">
                                <div class="flex items-center">
                                    <input type="checkbox" name="maintenance_mode" {{ $settings['system']['maintenance_mode'] ? 'checked' : '' }} class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <label class="ml-3 block text-sm font-medium text-gray-700">Maintenance Mode</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" name="debug_mode" {{ $settings['system']['debug_mode'] ? 'checked' : '' }} class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <label class="ml-3 block text-sm font-medium text-gray-700">Debug Mode</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Save Button -->
                <div class="text-right">
                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>