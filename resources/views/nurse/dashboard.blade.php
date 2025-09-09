<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Nurse Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-5">
        <div class="container-fluid">
            <div class="row g-4">

                <!-- Bed Occupancy Overview -->
                <div class="col-12">
                    <h4 class="mb-3">Bed Occupancy Overview</h4>

                    <!-- Hospital Summary Cards -->
                    <div class="row g-3 mb-4">
                        <div class="col-lg-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <i class="bi bi-hospital fs-1"></i>
                                    <h5>Total Beds</h5>
                                    <h3>{{ $bed_occupancy['hospital_summary']['total_beds'] ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <i class="bi bi-bed fs-1"></i>
                                    <h5>Occupied</h5>
                                    <h3>{{ $bed_occupancy['hospital_summary']['occupied_beds'] ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <i class="bi bi-check-circle fs-1"></i>
                                    <h5>Available</h5>
                                    <h3>{{ $bed_occupancy['hospital_summary']['available_beds'] ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <i class="bi bi-exclamation-triangle fs-1"></i>
                                    <h5>Occupancy Rate</h5>
                                    <h3>{{ $bed_occupancy['hospital_summary']['overall_occupancy_rate'] ?? 0 }}%</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ward-wise Occupancy Table -->
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Ward Name</th>
                                            <th>Total Beds</th>
                                            <th>Occupied</th>
                                            <th>Available</th>
                                            <th>Maintenance</th>
                                            <th>Occupancy Rate</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($bed_occupancy['wards'] ?? [] as $ward)
                                        <tr>
                                            <td><strong>{{ $ward['ward_name'] }}</strong></td>
                                            <td>{{ $ward['total_beds'] }}</td>
                                            <td><span class="badge bg-danger">{{ $ward['occupied_beds'] }}</span></td>
                                            <td><span class="badge bg-success">{{ $ward['available_beds'] }}</span></td>
                                            <td><span class="badge bg-secondary">{{ $ward['maintenance_beds'] }}</span></td>
                                            <td>{{ $ward['occupancy_rate'] }}%</td>
                                            <td>
                                                @if($ward['status'] == 'critical')
                                                    <span class="badge bg-danger">Critical</span>
                                                @elseif($ward['status'] == 'high')
                                                    <span class="badge bg-warning text-dark">High</span>
                                                @elseif($ward['status'] == 'moderate')
                                                    <span class="badge bg-info">Moderate</span>
                                                @else
                                                    <span class="badge bg-light text-dark">Low</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">No ward occupancy data available</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Assigned Patients & Medication Schedule -->
                <div class="col-lg-8">
                    <h4 class="mb-3">Assigned Patients ({{ $assigned_patients['total_assigned'] ?? 0 }} patients)</h4>

                    <!-- Today's Medication Schedule -->
                    <div class="row g-3 mb-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Today's Medication Schedule (Next 3 Hours)</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-sm mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Time</th>
                                                    <th>Patient</th>
                                                    <th>Medication</th>
                                                    <th>Dose</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($assigned_patients['medication_schedule'] ?? [] as $item)
                                                <tr>
                                                    <td>
                                                        @if($item['due_in_hours'] <= 1)
                                                            <span class="badge bg-danger">{{ $item['scheduled_time'] }}</span>
                                                        @elseif($item['due_in_hours'] <= 2)
                                                            <span class="badge bg-warning text-dark">{{ $item['scheduled_time'] }}</span>
                                                        @else
                                                            <span class="badge bg-info">{{ $item['scheduled_time'] }}</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $item['patient_name'] }}</td>
                                                    <td>{{ $item['medication'] }}</td>
                                                    <td>{{ $item['dose'] }}</td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-primary">Complete</button>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted">No upcoming medications</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Patient Cards -->
                    <div class="row g-3">
                        @forelse($assigned_patients['patients'] ?? [] as $patient)
                        <div class="col-lg-6">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="mb-1">{{ $patient['name'] }}</h6>
                                            <small class="text-muted">{{ $patient['ward_name'] }} | Day {{ $patient['days_admitted'] }}</small>
                                        </div>
                                        @if($patient['vitals_pending'])
                                            <span class="badge bg-danger">Vitals Pending</span>
                                        @endif
                                    </div>

                                    <small class="text-muted mb-2">
                                        Next check: {{ $patient['next_checking_time'] }}
                                        <br>Admitted: {{ $patient['admission_date'] }}
                                    </small>

                                    <div class="border rounded p-2 mb-2">
                                        <small><strong>Current Meds:</strong></small>
                                        @foreach($patient['medication_schedule'] as $med)
                                            <div class="d-flex justify-content-between">
                                                <small>{{ $med['name'] }}</small>
                                                <small class="text-muted">{{ $med['dose'] }}</small>
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="row g-2">
                                        <div class="col-6">
                                            <button class="btn btn-sm btn-primary w-100">Vitals</button>
                                        </div>
                                        <div class="col-6">
                                            <button class="btn btn-sm btn-success w-100">Med Admin</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <div class="text-center text-muted">No assigned patients found</div>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Pending Vitals Entry & Blood Alerts -->
                <div class="col-lg-4">
                    <div class="row g-4">
                        <!-- Pending Vitals Entry -->
                        <div class="col-12">
                            <h4 class="mb-3">Pending Vitals Entry</h4>

                            <!-- Summary Stats -->
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <div class="bg-danger text-white text-center p-3 rounded">
                                        <strong>{{ $pending_vitals['overdue_count'] ?? 0 }}</strong>
                                        <br><small>Overdue</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-warning text-white text-center p-3 rounded">
                                        <strong>{{ $pending_vitals['total_pending'] ?? 0 }}</strong>
                                        <br><small>Total Pending</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Vitals List -->
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Patient Vitals Status</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="list-group list-group-flush">
                                        @forelse($pending_vitals['entries'] ?? [] as $entry)
                                        <li class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="flex-grow-1">
                                                    <strong>{{ $entry['name'] }}</strong>
                                                    <br><small class="text-muted">{{ $entry['ward_name'] }} | {{ $entry['patient_type'] }}</small>
                                                    <br><small>Last vitals: {{ $entry['last_vitals'] }} ago</small>
                                                </div>
                                                <div class="text-end">
                                                    @if($entry['is_overdue'])
                                                        <button class="btn btn-sm btn-danger mb-1">Record Vitals</button>
                                                        <br><small class="text-danger">{{ $entry['hours_since_last'] }}h overdue</small>
                                                    @else
                                                        <button class="btn btn-sm btn-warning mb-1">Due Soon</button>
                                                        <br><small class="text-warning">Due in {{ 6 - $entry['hours_since_last'] }}h</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </li>
                                        @empty
                                        <li class="list-group-item text-center text-muted">No pending vitals entries</li>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Blood Bank Alerts -->
                        <div class="col-12">
                            <h4 class="mb-3">Blood Bank Alerts</h4>

                            <!-- Alert Summary -->
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <div class="bg-danger text-white text-center p-2 rounded">
                                        <strong>{{ $blood_alerts['summary']['critical_alerts'] ?? 0 }}</strong>
                                        <br><small class="small">Critical</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-warning text-white text-center p-2 rounded">
                                        <strong>{{ $blood_alerts['summary']['total_alerts'] ?? 0 }}</strong>
                                        <br><small class="small">Total Alerts</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Alert Details -->
                            <div class="card">
                                <div class="card-body">
                                    @forelse($blood_alerts['alerts'] ?? [] as $alert)
                                        @if($alert['alert_type'] == 'low_stock')
                                            <div class="alert alert-warning p-2 mb-2">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small><strong>{{ $alert['blood_type'] }}</strong> - {{ $alert['units'] }} units remaining</small>
                                                    <span class="badge bg-{{ $alert['warning_level'] == 'Critical' ? 'danger' : 'warning' }}">
                                                        {{ $alert['warning_level'] }}
                                                    </span>
                                                </div>
                                            </div>
                                        @elseif($alert['alert_type'] == 'expiring_soon')
                                            <div class="alert alert-info p-2 mb-2">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small><strong>{{ $alert['blood_type'] }}</strong> - {{ $alert['units'] }} units</small>
                                                    <span class="badge bg-info">{{ $alert['days_until_expiry'] }} days left</span>
                                                </div>
                                            </div>
                                        @endif
                                    @empty
                                        <div class="alert alert-success">
                                            <small>All blood levels are within normal range</small>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>