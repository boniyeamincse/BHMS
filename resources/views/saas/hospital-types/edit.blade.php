<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Hospital Type: :name', ['name' => $hospitalType->name]) }}
        </h2>
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
                <form method="POST" action="{{ route('saas.hospital-types.update', $hospitalType) }}">
                    @method('PATCH')
                    @csrf

                    <div class="p-6 space-y-6">
                        <!-- Type Information -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Hospital Type Information</h3>
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Name</label>
                                    <input type="text" name="name" value="{{ old('name', $hospitalType->name) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Description</label>
                                    <textarea name="description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('description', $hospitalType->description) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Statistics -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Statistics</h4>
                            <div class="text-sm text-gray-600">
                                <p>Used by <strong>{{ $hospitalType->hospitals_count }}</strong> hospitals</p>
                                <p>Created {{ $hospitalType->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-6 py-3 text-right">
                        <a href="{{ route('saas.hospital-types.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 mr-4">Cancel</a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">Update Hospital Type</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>