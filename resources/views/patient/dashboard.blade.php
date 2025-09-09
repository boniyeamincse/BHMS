<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Patient Portal Dashboard') }}
        </h2>
    </x-slot>

    <!-- Welcome Section -->
    <div class="py-5">
        <div class="container-fluid">
            <div class="row g-4">
                <!-- Patient Info Card -->
                <div class="col-12">
                    <div class="card border-primary">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-lg-8">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <div class="text-center">
                                                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2rem;">
                                                    <i class="bi bi-person-circle"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <h4 class="mb-1">{{ $patient_data['name'] }}</h4>
                                            <p class="mb-2 text-muted">{{ $patient_data['age'] }} years old • {{ $patient_data['gender'] }} • {{ $patient_data['blood_group'] }}</p>
                                            <div class="row g-2 mb-2">
                                                <div class="col-6">
                                                    <small class="text-muted"><i class="bi bi-telephone"></i> {{ $patient_data['phone'] }}</small>
                                                </div>
                                                <div class="col-6">
                                                    <small class="text-muted"><i class="bi bi-envelope"></i> {{ $patient_data['email'] }}</small>
                                                </div>
                                            </div>
                                            <small class="text-success"><i class="bi bi-calendar-check"></i> Patient since {{ $patient_data['patient_since'] }}</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="row g-2">
                                        <div class="col-12">
                                            <div class="alert alert-warning border-0">
                                                <small><strong>Emergency Contact:</strong> {{ $patient_data['emergency_contact'] }}</small>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <button class="btn btn-outline-primary btn-sm w-100">Update Profile</button>
                                        </div>
                                        <div class="col-12">
                                            <button class="btn btn-outline-success btn-sm w-100">Emergency Help</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Appointments -->
                <div class="col-lg-8">
                    <h4 class="mb-3">Upcoming Appointments</h4>

                    @if($upcoming_appointments['next_appointment'])
                        <!-- Next Appointment Highlight -->
                        <div class="card border-primary mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h5 class="mb-1">Next Appointment</h5>
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="badge bg-primary me-2">{{ $upcoming_appointments['next_appointment']['date'] }}</span>
                                            <span class="badge bg-info">{{ $upcoming_appointments['next_appointment']['time'] }}</span>
                                        </div>
                                        <p class="mb-1"><strong>{{ $upcoming_appointments['next_appointment']['doctor'] }}</strong></p>
                                        <small class="text-muted">{{ $upcoming_appointments['next_appointment']['department'] }} • {{ $upcoming_appointments['next_appointment']['location'] }}</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-success">{{ $upcoming_appointments['next_appointment']['status'] }}</span>
                                        <br><button class="btn btn-outline-primary btn-sm mt-2">View Details</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- All Upcoming Appointments -->
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date & Time</th>
                                            <th>Doctor & Department</th>
                                            <th>Location</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($upcoming_appointments['appointments'] as $appointment)
                                        <tr>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="fw-bold">{{ $appointment['date'] }}</span>
                                                    <small class="text-muted">{{ $appointment['time'] }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span>{{ $appointment['doctor'] }}</span>
                                                    <small class="text-muted">{{ $appointment['department'] }}</small>
                                                </div>
                                            </td>
                                            <td>{{ $appointment['location'] }}</td>
                                            <td>
                                                <span class="badge bg-
                                                    {{ $appointment['status'] == 'confirmed' ? 'success' : 'warning text-dark' }}">
                                                    {{ $appointment['status'] }}
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-outline-primary btn-sm">Details</button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">
                                                <i class="bi bi-calendar-x fs-2"></i>
                                                <p class="mb-0">No upcoming appointments scheduled</p>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-2">
                                <button class="btn btn-primary btn-sm">Book New Appointment</button>
                                <button class="btn btn-outline-secondary btn-sm">View All Appointments</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bills & Payments -->
                <div class="col-lg-4">
                    <div class="row g-4">

                        <!-- Financial Summary -->
                        <div class="col-12">
                            <h4 class="mb-3">Financial Summary</h4>
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <div class="card text-center py-2 border-danger">
                                        <strong class="text-danger">${{ number_format($bill_payments['total_due'], 2) }}</strong>
                                        <br><small class="text-muted">Amount Due</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="card text-center py-2 border-success">
                                        <strong class="text-success">${{ number_format($bill_payments['total_paid_this_year'], 2) }}</strong>
                                        <br><small class="text-muted">Paid This Year</small>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <h6>Due Bills</h6>
                                <div class="card">
                                    <div class="card-body p-0">
                                        @forelse($bill_payments['due_bills'] as $bill)
                                        <div class="border-bottom p-2">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="flex-grow-1">
                                                    <small class="d-block">{{ $bill['description'] }}</small>
                                                    <small class="text-muted">
                                                        Due: {{ $bill['due_date'] }}
                                                        @if($bill['days_overdue'] > 0)
                                                            (<span class="text-danger">{{ $bill['days_overdue'] }} days overdue</span>)
                                                        @endif
                                                    </small>
                                                </div>
                                                <div class="text-end">
                                                    <strong class="text-danger">${{ number_format($bill['amount'], 2) }}</strong>
                                                    <br><small class="text-muted">${{ number_format($bill['remaining_amount'], 2) }} left</small>
                                                </div>
                                            </div>
                                        </div>
                                        @empty
                                        <div class="text-center text-muted p-3">
                                            <small>No outstanding bills</small>
                                        </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>

                            <button class="btn btn-success btn-sm w-100">Make Payment</button>
                            <button class="btn btn-outline-info btn-sm w-100 mt-2">View Full History</button>
                        </div>

                    </div>
                </div>

                <!-- Prescriptions & Reports -->
                <div class="col-lg-6">
                    <h4 class="mb-3">Medical Records</h4>

                    <!-- Prescriptions Available -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">📋 Prescriptions ({{ $prescriptions_reports['collectible_prescriptions'] }} collectible)</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                @forelse($prescriptions_reports['prescriptions'] as $rx)
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-1">
                                                <strong class="me-2">{{ $rx['medications'][0]['name'] ?? 'Prescription' }}</strong>
                                                @if($rx['is_collectible'])
                                                    <span class="badge bg-success">Ready to Collect</span>
                                                @else
                                                    <span class="badge bg-secondary">Already Collected</span>
                                                @endif
                                            </div>
                                            <small class="text-muted mb-1">{{ $rx['doctor'] }} - {{ $rx['date'] }}</small>
                                            <small>{{ $rx['instructions'] }}</small>
                                        </div>
                                        <button class="btn btn-outline-primary btn-sm">View</button>
                                    </div>
                                </li>
                                @empty
                                <li class="list-group-item text-center text-muted">No recent prescriptions</li>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Reports Available -->
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">📄 Medical Reports ({{ $prescriptions_reports['available_reports'] }} available)</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                @forelse($prescriptions_reports['reports'] as $report)
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <strong>{{ $report['test_name'] }}</strong>
                                            <br><small class="text-muted">{{ $report['type'] }} • {{ $report['doctor'] }} • {{ $report['date'] }}</small>
                                            @if($report['status'] == 'available')
                                                <br><small class="text-success">{{ $report['result_summary'] }}</small>
                                            @else
                                                <br><small class="text-warning">{{ $report['result_summary'] }}</small>
                                            @endif
                                        </div>
                                        <div class="text-end">
                                            @if($report['status'] == 'available')
                                                <span class="badge bg-success mb-1">Available</span>
                                                <br><button class="btn btn-primary btn-sm">Download</button>
                                            @else
                                                <span class="badge bg-warning mb-1">Pending</span>
                                                <br><button class="btn btn-outline-primary btn-sm" disabled>View</button>
                                            @endif
                                        </div>
                                    </div>
                                </li>
                                @empty
                                <li class="list-group-item text-center text-muted">No recent reports</li>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- IPD Admission History & Notifications -->
                <div class="col-lg-6">
                    <div class="row g-4">

                        <!-- IPD Admission History -->
                        <div class="col-12">
                            <h4 class="mb-3">IPD Admission History</h4>

                            @if($ipd_history['last_admission'])
                                <!-- Last Admission Summary -->
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h6>Last Admission</h6>
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <small class="text-muted">Admitted</small>
                                                <br><strong>{{ $ipd_history['last_admission']['admission_date'] }}</strong>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">Discharged</small>
                                                <br><strong>{{ $ipd_history['last_admission']['discharge_date'] }}</strong>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">Diagnosis</small>
                                                <br><strong>{{ $ipd_history['last_admission']['primary_diagnosis'] }}</strong>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">Stay Duration</small>
                                                <br><strong>{{ $ipd_history['last_admission']['length_of_stay'] }} days</strong>
                                            </div>
                                            <div class="col-12">
                                                <small class="text-muted">Total Cost: ${{ number_format($ipd_history['last_admission']['total_cost'], 2) }}</small>
                                                <span class="badge bg-info ms-2">{{ $ipd_history['last_admission']['outcome'] }}</span>
                                            </div>
                                        </div>
                                        <button class="btn btn-outline-info btn-sm w-100 mt-2">View Full Details</button>
                                    </div>
                                </div>
                            @endif

                            <!-- Admissions List -->
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">All Admissions ({{ $ipd_history['total_admissions'] }} total)</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="list-group list-group-flush">
                                        @forelse($ipd_history['admissions'] as $admission)
                                        <li class="list-group-item">
                                            <div class="row g-2 align-items-center">
                                                <div class="col-8">
                                                    <strong>{{ $admission['primary_diagnosis'] }}</strong>
                                                    <br><small class="text-muted">{{ $admission['admission_date'] }} to {{ $admission['discharge_date'] }}</small>
                                                </div>
                                                <div class="col-4 text-end">
                                                    <small class="d-block">${{ number_format($admission['total_cost'], 2) }}</small>
                                                    <small class="text-muted">{{ $admission['length_of_stay'] }} days</small>
                                                </div>
                                            </div>
                                        </li>
                                        @empty
                                        <li class="list-group-item text-center text-muted">No IPD admission history</li>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notifications/Messages -->
                        <div class="col-12">
                            <h4 class="mb-3">Notifications & Messages</h4>

                            <!-- Notification Count -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <small class="text-muted">Total Notifications</small>
                                <div class="d-flex gap-2">
                                    <span class="badge bg-primary">{{ $notifications['unread_count'] }}</span>
                                    @if($notifications['urgent_count'] > 0)
                                        <span class="badge bg-danger">{{ $notifications['urgent_count'] }} urgent</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Notifications -->
                            <div class="card">
                                <div class="card-body p-0">
                                    <div class="list-group list-group-flush">
                                        @forelse($notifications['notifications'] as $notification)
                                        <li class="list-group-item {{ !$notification['is_read'] ? 'border-start border-warning border-4' : '' }}">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="flex-grow-1">
                                                    <div class="d-flex align-items-center mb-1">
                                                        <span class="badge bg-{{ $notification['priority'] == 'high' ? 'danger' : 'info' }} me-2">{{ $notification['type'] }}</span>
                                                        @if(!$notification['is_read'])
                                                            <span class="badge bg-secondary">New</span>
                                                        @endif
                                                    </div>
                                                    <strong>{{ $notification['title'] }}</strong>
                                                    <br><small class="text-muted">{{ $notification['message'] }}</small>
                                                </div>
                                                <small class="text-muted">{{ $notification['date'] }}</small>
                                            </div>
                                        </li>
                                        @empty
                                        <li class="list-group-item text-center text-muted">No new notifications</li>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tele-consultation Links -->
                        <div class="col-12">
                            <h4 class="mb-3">Tele-Consultations</h4>

                            @if($tele_consultations['next_consultation'])
                                <!-- Next Consultation -->
                                <div class="card mb-3 border-success">
                                    <div class="card-body">
                                        <h6 class="card-title text-success">📹 Next Consultation</h6>
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <small class="text-muted">Date</small>
                                                <br><strong>{{ $tele_consultations['next_consultation']['scheduled_date'] }}</strong>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted">Time</small>
                                                <br><strong>{{ $tele_consultations['next_consultation']['scheduled_time'] }}</strong>
                                            </div>
                                            <div class="col-12">
                                                <small class="text-muted">Doctor</small>
                                                <br><strong>{{ $tele_consultations['next_consultation']['doctor'] }}</strong>
                                            </div>
                                            @if($tele_consultations['next_consultation']['special_notes'])
                                            <div class="col-12">
                                                <small class="text-muted">Notes:</small>
                                                <br><small>{{ $tele_consultations['next_consultation']['special_notes'] }}</small>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="mt-2">
                                            <button class="btn btn-success btn-sm">Join Consultation</button>
                                            <small class="text-muted ms-2">Passcode: {{ $tele_consultations['next_consultation']['passcode'] }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Tele-consultation History -->
                            @if(count($tele_consultations['completed_sessions']) > 0)
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Recent Sessions</h6>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="list-group list-group-flush">
                                            @forelse($tele_consultations['completed_sessions'] as $session)
                                            <li class="list-group-item">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div class="flex-grow-1">
                                                        <strong>{{ $session['session_title'] }}</strong>
                                                        <br><small class="text-muted">{{ $session['doctor'] }} - {{ $session['completed_date'] }} ({{ $session['duration'] }} min)</small>
                                                        <br><small>{{ $session['summary'] }}</small>
                                                    </div>
                                                    <span class="badge bg-secondary">Completed</span>
                                                </div>
                                            </li>
                                            @empty
                                            <div class="text-center text-muted p-3">
                                                <small>No completed tele-consultations</small>
                                            </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <button class="btn btn-outline-primary btn-sm w-100 mt-2">Schedule New Consultation</button>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>