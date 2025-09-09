<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Doctor Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-5">
        <div class="container-fluid">
            <div class="row g-4">

                <!-- Today's Appointments -->
                <div class="col-12">
                    <h4 class="mb-3">Today's Appointments</h4>
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Patient Name</th>
                                            <th>Type</th>
                                            <th>Time</th>
                                            <th>Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($today_appointments ?? [] as $appointment)
                                        <tr>
                                            <td>{{ $appointment['patient_name'] }}</td>
                                            <td><span class="badge bg-primary">{{ $appointment['type'] }}</span></td>
                                            <td>{{ $appointment['time'] }}</td>
                                            <td>{{ $appointment['notes'] ?? 'N/A' }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">No appointments for today</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Patient Cases Assigned -->
                <div class="col-12">
                    <h4 class="mb-3">Assigned Patient Cases</h4>
                    <div class="row g-3">
                        @forelse($assigned_patients ?? [] as $patient)
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6>{{ $patient['name'] }}</h6>
                                            <small class="text-muted">{{ $patient['type'] }}</small>
                                        </div>
                                        <span class="badge bg-success">{{ $patient['status'] }}</span>
                                    </div>
                                    <p class="mb-0 text-sm">Admission: {{ $patient['admission_date'] }}</p>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body text-center text-muted">
                                    <p>No assigned patients</p>
                                </div>
                            </div>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- OPD/IPD Patients Under Care -->
                <div class="col-12">
                    <h4 class="mb-3">Patients Under Care</h4>
                    <div class="row g-3">
                        <div class="col-lg-6">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <i class="bi bi-clipboard-data-fill fs-1"></i>
                                    <h5>OPD Patients</h5>
                                    <h3>{{ $opd_ipd_patients['opd'] ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <i class="bi bi-hospital-fill fs-1"></i>
                                    <h5>IPD Patients</h5>
                                    <h3>{{ $opd_ipd_patients['ipd'] ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Prescriptions -->
                <div class="col-lg-6">
                    <h4 class="mb-3">Pending Prescriptions</h4>
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Patient</th>
                                            <th>Amount</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($pending_prescriptions ?? [] as $prescription)
                                        <tr>
                                            <td>{{ $prescription['patient_name'] }}</td>
                                            <td>${{ number_format($prescription['amount'], 2) }}</td>
                                            <td>{{ $prescription['created_date'] }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">No pending prescriptions</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Schedules -->
                <div class="col-lg-6">
                    <h4 class="mb-3">Upcoming Schedules</h4>
                    <div class="card">
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                @forelse($upcoming_schedules ?? [] as $schedule)
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $schedule['patient_name'] }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $schedule['type'] }}</small>
                                        </div>
                                        <span class="badge bg-info">{{ $schedule['date'] }}</span>
                                    </div>
                                </li>
                                @empty
                                <li class="list-group-item text-center text-muted">
                                    No upcoming schedules
                                </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Tele-consultations Scheduled -->
                <div class="col-12">
                    <h4 class="mb-3">Tele-consultations Scheduled</h4>
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Patient Name</th>
                                            <th>Scheduled Date</th>
                                            <th>Details</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($tele_consultations ?? [] as $tele)
                                        <tr>
                                            <td>{{ $tele['patient_name'] }}</td>
                                            <td>{{ $tele['date'] }}</td>
                                            <td>{{ $tele['notes'] ?? 'N/A' }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">No tele-consultations scheduled</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>