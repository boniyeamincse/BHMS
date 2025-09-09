<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Enquiry Details') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('saas.enquiries.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Back to Enquiries
                </a>
                @if($enquiry->status === 'unread')
                <form method="POST" action="{{ route('saas.enquiries.mark-read', $enquiry) }}" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Mark as Read
                    </button>
                </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Success Messages -->
            @if(session('success'))
            <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4">
                {{ session('success') }}
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <!-- Header Info -->
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ $enquiry->subject }}</h3>
                            <div class="mt-1 flex items-center space-x-4">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    @if($enquiry->priority === 'high') bg-red-100 text-red-800
                                    @elseif($enquiry->priority === 'medium') bg-yellow-100 text-yellow-800
                                    @else bg-green-100 text-green-800 @endif">
                                    {{ ucfirst($enquiry->priority ?? 'low') }} Priority
                                </span>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    @if($enquiry->status === 'unread') bg-blue-100 text-blue-800
                                    @elseif($enquiry->status === 'read') bg-gray-100 text-gray-800
                                    @else bg-green-100 text-green-800 @endif">
                                    {{ ucfirst($enquiry->status) }}
                                </span>
                                <span class="text-sm text-gray-500">{{ $enquiry->created_at->format('M d, Y H:i') }}</span>
                            </div>
                        </div>
                        <div class="text-right text-sm text-gray-500">
                            {{ $enquiry->created_at->diffForHumans() }}
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4">
                    <!-- Contact Information -->
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Contact Information</h4>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500">Name</label>
                                    <div class="mt-1 text-sm text-gray-900">{{ $enquiry->name }}</div>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500">Email</label>
                                    <div class="mt-1 text-sm text-blue-600">{{ $enquiry->email }}</div>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500">Phone</label>
                                    <div class="mt-1 text-sm text-gray-900">{{ $enquiry->phone ?? 'Not provided' }}</div>
                                </div>
                                @if($enquiry->company)
                                <div>
                                    <label class="block text-xs font-medium text-gray-500">Company</label>
                                    <div class="mt-1 text-sm text-gray-900">{{ $enquiry->company }}</div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Enquiry Details -->
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Enquiry Details</h4>
                        <div class="bg-white border rounded-lg p-4">
                            <div class="prose max-w-none">
                                {{ nl2br(e($enquiry->message)) }}
                            </div>
                        </div>
                    </div>

                    <!-- Response Notes -->
                    @if($enquiry->responded_at)
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Response Details</h4>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <p class="text-sm text-gray-700">
                                <strong>First responded:</strong> {{ $enquiry->responded_at->format('M d, Y H:i') }}
                                ({{ $enquiry->responded_at->diffForHumans() }})
                            </p>
                            @if($enquiry->notes)
                            <div class="mt-2">
                                <strong>Notes:</strong>
                                <div class="mt-1 text-sm text-gray-700">{{ nl2br(e($enquiry->notes)) }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                        <div class="flex space-x-2">
                            <form method="POST" action="{{ route('saas.enquiries.destroy', $enquiry) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Are you sure you want to delete this enquiry?')" class="inline-flex items-center px-3 py-1.5 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Delete Enquiry
                                </button>
                            </form>
                        </div>
                        <div class="text-sm text-gray-500">
                            Enquiry ID: {{ $enquiry->id }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>