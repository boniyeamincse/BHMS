<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Plan: :name', ['name' => $plan->name]) }}
            </h2>
            <a href="{{ route('saas.plans.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Back to Plans
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Success Messages -->
            @if(session('success'))
            <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4">
                {{ session('success') }}
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('saas.plans.update', $plan) }}">
                    @method('PATCH')
                    @csrf

                    <div class="p-6">
                        <!-- Basic Information -->
                        <div class="mb-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Plan Name *</label>
                                    <input type="text" name="name" value="{{ old('name', $plan->name) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Plan Type *</label>
                                    <select name="type" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="trial" {{ old('type', $plan->type) === 'trial' ? 'selected' : '' }}>Trial Plan</option>
                                        <option value="paid" {{ old('type', $plan->type) === 'paid' ? 'selected' : '' }}>Paid Plan</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Status *</label>
                                    <select name="status" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="active" {{ old('status', $plan->status) === 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status', $plan->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea name="description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('description', $plan->description) }}</textarea>
                            </div>
                        </div>

                        <!-- Pricing -->
                        <div class="mb-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Pricing</h3>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Monthly Price ($)</label>
                                    <input type="number" name="monthly_price" value="{{ old('monthly_price', $plan->monthly_price) }}" step="0.01" min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Yearly Price ($)</label>
                                    <input type="number" name="yearly_price" value="{{ old('yearly_price', $plan->yearly_price) }}" step="0.01" min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Trial Days</label>
                                    <input type="number" name="trial_days" value="{{ old('trial_days', $plan->trial_days) }}" min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Sort Order</label>
                                    <input type="number" name="sort_order" value="{{ old('sort_order', $plan->sort_order) }}" min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                        </div>

                        <!-- Features -->
                        <div class="mb-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Features (One per line)</h3>
                            @php
                                $featuresString = '';
                                if (old('features')) {
                                    $featuresString = implode("\n", old('features'));
                                } elseif ($plan->features) {
                                    $featuresString = implode("\n", $plan->features);
                                }
                            @endphp
                            <textarea name="features[]" rows="5" placeholder="Enter each feature on a new line" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ $featuresString }}</textarea>
                        </div>

                        <!-- Limits -->
                        <div class="mb-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Plan Limits</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">User Limit</label>
                                    @php
                                        $userLimit = old('limits.user_limit', $plan->limits['user_limit'] ?? null);
                                    @endphp
                                    <input type="number" name="limits[user_limit]" value="{{ $userLimit }}" min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Patient Limit</label>
                                    @php
                                        $patientLimit = old('limits.patient_limit', $plan->limits['patient_limit'] ?? null);
                                    @endphp
                                    <input type="number" name="limits[patient_limit]" value="{{ $patientLimit }}" min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Storage Limit (GB)</label>
                                    @php
                                        $storageLimit = old('limits.storage_limit', $plan->limits['storage_limit'] ?? null);
                                    @endphp
                                    <input type="number" name="limits[storage_limit]" value="{{ $storageLimit }}" min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                        </div>

                        <!-- Statistics -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Statistics</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
                                <p>Used by: <strong>{{ $plan->hospitals_count }}</strong> hospitals</p>
                                <p>Created: {{ $plan->created_at->format('M d, Y') }}</p>
                                <p>Updated: {{ $plan->updated_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-3 text-right">
                        <a href="{{ route('saas.plans.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-4">Cancel</a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">Update Plan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>