<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Hospital: :name', ['name' => $hospital->name]) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('saas.hospitals.update', $hospital) }}">
                    @method('PATCH')
                    @csrf

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Hospital Info -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Hospital Information</h3>
                                <div class="bg-gray-50 p-4 rounded-lg space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Name</label>
                                        <div class="mt-1 text-sm text-gray-900">{{ $hospital->name }}</div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Email</label>
                                        <div class="mt-1 text-sm text-gray-900">{{ $hospital->email }}</div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Phone</label>
                                        <div class="mt-1 text-sm text-gray-900">{{ $hospital->phone ?? 'Not provided' }}</div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Address</label>
                                        <div class="mt-1 text-sm text-gray-900">{{ $hospital->address ?? 'Not provided' }}</div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Created</label>
                                        <div class="mt-1 text-sm text-gray-900">{{ $hospital->created_at->format('M d, Y H:i') }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Status Settings -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Status Settings</h3>
                                <div class="space-y-6">
                                    <!-- Hospital Status -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Hospital Status</label>
                                        <div class="mt-1">
                                            <select name="status" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                <option value="active" {{ $hospital->status === 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="inactive" {{ $hospital->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Payment Status -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Payment Status</label>
                                        <div class="mt-1">
                                            <select name="payment_status" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                                <option value="active" {{ $hospital->payment_status === 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="trial" {{ $hospital->payment_status === 'trial' ? 'selected' : '' }}>Trial</option>
                                                <option value="expired" {{ $hospital->payment_status === 'expired' ? 'selected' : '' }}>Expired</option>
                                                <option value="free" {{ $hospital->payment_status === 'free' ? 'selected' : '' }}>Free</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Subscription Info -->
                        @if($hospital->subscriptionPlan)
                        <div class="mt-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Subscription Information</h3>
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-blue-700">Plan</label>
                                        <div class="mt-1 text-sm text-blue-800">{{ $hospital->subscriptionPlan->name }}</div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-blue-700">Billing Cycle</label>
                                        <div class="mt-1 text-sm text-blue-800">{{ ucfirst($hospital->billing_cycle ?? 'Not set') }}</div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-blue-700">Last Billed</label>
                                        <div class="mt-1 text-sm text-blue-800">{{ $hospital->last_billed_at?->format('M d, Y') ?? 'Never' }}</div>
                                    </div>
                                    @if($hospital->subscription_end_date)
                                    <div>
                                        <label class="block text-sm font-medium text-blue-700">End Date</label>
                                        <div class="mt-1 text-sm text-blue-800">{{ $hospital->subscription_end_date->format('M d, Y') }}</div>
                                    </div>
                                    @endif
                                    @if($hospital->trial_end_date)
                                    <div>
                                        <label class="block text-sm font-medium text-blue-700">Trial End</label>
                                        <div class="mt-1 text-sm text-blue-800">{{ $hospital->trial_end_date->format('M d, Y') }}</div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="bg-gray-50 px-6 py-3 text-right">
                        <a href="{{ route('saas.hospitals.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-4">Cancel</a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">Update Hospital</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>