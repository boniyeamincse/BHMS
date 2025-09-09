<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Hospital Settings') }}
        </h2>
    </x-slot>

    <div class="py-5">
        <div class="container">
            <div class="bg-white shadow-sm rounded-lg">
                <div class="p-6">
                    @if(session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @elseif(session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('hospital.settings.update') }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <!-- Module Toggles -->
                        <div class="mb-5">
                            <h4 class="mb-4">Module Toggles</h4>
                            <div class="row">
                                @foreach($modules as $key => $label)
                                    <div class="col-lg-4 col-md-6 mb-3">
                                        <div class="form-check">
                                            <input type="checkbox" id="module-{{ $key }}" name="modules[{{ $key }}]"
                                                   value="1" {{ isset($settings['modules'][$key]) ? 'checked' : '' }}
                                                   class="form-check-input">
                                            <label for="module-{{ $key }}" class="form-check-label">{{ $label }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- General Settings -->
                        <div class="mb-5">
                            <h4 class="mb-4">General Settings</h4>
                            <div class="mb-3">
                                <label for="currency" class="form-label">Currency</label>
                                <input type="text" id="currency" name="currency" value="{{ $settings['currency'] ?? 'BDT' }}"
                                       class="form-control" placeholder="e.g., USD, EUR, BDT">
                            </div>
                            <div class="mb-3">
                                <label for="timezone" class="form-label">Timezone</label>
                                <input type="text" id="timezone" name="timezone" value="{{ $settings['timezone'] ?? 'Asia/Dhaka' }}"
                                       class="form-control" placeholder="e.g., UTC, Asia/Dhaka">
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Save Settings
                            </button>
                            <a href="{{ route('hospital.dashboard') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>