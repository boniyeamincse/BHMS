<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Hospital Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-5">
        <div class="container">
            <div class="bg-white shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="row g-4">
                        <!-- Card 1: Total Users -->
                        <div class="col-lg-3 col-md-6">
                            <div class="bg-primary text-white p-4 rounded shadow-sm">
                                <h5 class="fw-bold">Total Users</h5>
                                <p class="h3 mb-0">{{ $user_count }}</p>
                            </div>
                        </div>

                        <!-- Card 2: Patients -->
                        <div class="col-lg-3 col-md-6">
                            <div class="bg-success text-white p-4 rounded shadow-sm">
                                <h5 class="fw-bold">Patients</h5>
                                <p class="h3 mb-0">{{ $patient_count }}</p>
                            </div>
                        </div>

                        <!-- Card 3: Bills -->
                        <div class="col-lg-3 col-md-6">
                            <div class="bg-warning text-white p-4 rounded shadow-sm">
                                <h5 class="fw-bold">Total Bills</h5>
                                <p class="h3 mb-0">${{ $bills }}</p>
                            </div>
                        </div>

                        <!-- Card 4: Available Beds -->
                        <div class="col-lg-3 col-md-6">
                            <div class="bg-danger text-white p-4 rounded shadow-sm">
                                <h5 class="fw-bold">Available Beds</h5>
                                <p class="h3 mb-0">{{ $available_beds }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="row mt-5">
                        <div class="col-12">
                            <h4 class="mb-4">Quick Actions</h4>
                            <div class="d-grid gap-3 d-md-flex">
                                <a href="{{ route('hospital.settings.index') }}" class="btn btn-outline-primary">
                                    <i class="bi bi-gear me-2"></i>Settings
                                </a>
                                <button class="btn btn-outline-secondary">
                                    <i class="bi bi-file-earmark-plus me-2"></i>Add Patient
                                </button>
                                <button class="btn btn-outline-info">
                                    <i class="bi bi-calendar3 me-2"></i>Appointments
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>