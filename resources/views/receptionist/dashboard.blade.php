<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Receptionist Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-5">
        <div class="container-fluid">
            <div class="row g-4">

                <!-- Appointment Requests -->
                <div class="col-12">
                    <h4 class="mb-3">Appointment Requests</h4>
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Patient Name</th>
                                            <th>Phone</th>
                                            <th>Date</th>
                                            <th>Time</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($appointment_requests ?? [] as $appointment)
                                        <tr>
                                            <td>{{ $appointment['patient_name'] }}</td>
                                            <td>{{ $appointment['phone'] }}</td>
                                            <td>{{ $appointment['appointment_date'] }}</td>
                                            <td>{{ $appointment['appointment_time'] }}</td>
                                            <td><span class="badge bg-primary">{{ $appointment['type'] }}</span></td>
                                            <td>
                                                @if($appointment['status'] == 'pending')
                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                @else
                                                    <span class="badge bg-success">Approved</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-primary">View</button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">No appointment requests</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Patient Registrations -->
                <div class="col-lg-8">
                    <h4 class="mb-3">Patient Registrations</h4>
                    <div class="card">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="border rounded p-3 text-center">
                                        <h5 class="text-primary">{{ $patient_registrations['new_this_month'] ?? 0 }}</h5>
                                        <small class="text-muted">New This Month</small>
                                        <div class="mt-2">
                                            @if(($patient_registrations['new_increase_percent'] ?? 0) > 0)
                                                <span class="text-success">⬆️ {{ $patient_registrations['new_increase_percent'] }}%</span>
                                            @else
                                                <span class="text-danger">⬇️ {{ abs($patient_registrations['new_increase_percent']) }}%</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="border rounded p-3 text-center">
                                        <h5 class="text-info">{{ $patient_registrations['returning_this_month'] ?? 0 }}</h5>
                                        <small class="text-muted">Returning This Month</small>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="text-center">
                                <h6>Total Registrations This Month</h6>
                                <h4 class="text-success">{{ $patient_registrations['total_this_month'] ?? 0 }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Call Log Summary -->
                <div class="col-lg-4">
                    <h4 class="mb-3">Call Log Summary</h4>
                    <div class="card">
                        <div class="card-body">
                            <h6>Today's Calls</h6>
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <div class="bg-primary text-white text-center p-2 rounded">
                                        <small>Incoming</small>
                                        <br><strong>{{ $call_log_summary['today']['incoming'] ?? 0 }}</strong>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-success text-white text-center p-2 rounded">
                                        <small>Outgoing</small>
                                        <br><strong>{{ $call_log_summary['today']['outgoing'] ?? 0 }}</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="border rounded p-2 text-center mb-2">
                                <small class="text-muted">Missed</small>
                                <br><span class="text-danger"><strong>{{ $call_log_summary['today']['missed'] ?? 0 }}</strong></span>
                            </div>
                            <div class="border rounded p-2 text-center">
                                <small class="text-muted">Total Today</small>
                                <br><strong>{{ $call_log_summary['today']['total'] ?? 0 }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Visitor Log Summary -->
                <div class="col-lg-6">
                    <h4 class="mb-3">Visitor Log Summary</h4>
                    <div class="card">
                        <div class="card-body">
                            <div class="row g-3 mb-3">
                                <div class="col-4">
                                    <div class="border rounded p-3 text-center">
                                        <h6>{{ $visitor_log_summary['today']['total'] ?? 0 }}</h6>
                                        <small class="text-muted">Today</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="border rounded p-3 text-center">
                                        <h6>{{ $visitor_log_summary['this_week'] ?? 0 }}</h6>
                                        <small class="text-muted">This Week</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="border rounded p-3 text-center">
                                        <h6>{{ $visitor_log_summary['this_month'] ?? 0 }}</h6>
                                        <small class="text-muted">This Month</small>
                                    </div>
                                </div>
                            </div>

                            <h6>Today's Visitors by Type</h6>
                            <@if($visitor_log_summary['today']['by_type'] ?? null)
                                <div class="row g-2">
                                    <div class="col-4">
                                        <div class="text-center">
                                            <strong>{{ $visitor_log_summary['today']['by_type']['patients'] ?? 0 }}</strong>
                                            <br><small class="text-muted">Patients</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-center">
                                            <strong>{{ $visitor_log_summary['today']['by_type']['patient_family'] ?? 0 }}</strong>
                                            <br><small class="text-muted">Family</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-center">
                                            <strong>{{ $visitor_log_summary['today']['by_type']['others'] ?? 0 }}</strong>
                                            <br><small class="text-muted">Others</small>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Enquiries/Tickets -->
                <div class="col-lg-6">
                    <h4 class="mb-3">Enquiries & Tickets</h4>

                    <!-- Summary Cards -->
                    <div class="row g-2 mb-3">
                        <div class="col-3">
                            <div class="bg-warning text-dark text-center p-2 rounded">
                                <small>Total</small>
                                <br><strong>{{ $enquiries_tickets['summary']['total'] ?? 0 }}</strong>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="bg-danger text-white text-center p-2 rounded">
                                <small>Unread</small>
                                <br><strong>{{ $enquiries_tickets['summary']['unread'] ?? 0 }}</strong>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="bg-success text-white text-center p-2 rounded">
                                <small>High</small>
                                <br><strong>{{ $enquiries_tickets['summary']['high_priority'] ?? 0 }}</strong>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="bg-info text-white text-center p-2 rounded">
                                <small>Medium</small>
                                <br><strong>{{ $enquiries_tickets['summary']['medium_priority'] ?? 0 }}</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Enquiries -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Recent Enquiries</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                @forelse($enquiries_tickets['recent'] ?? [] as $enquiry)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <strong>{{ $enquiry['name'] }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $enquiry['subject'] }}</small>
                                            <p class="mb-1 text-sm">{{ Str::limit($enquiry['message'], 60) }}</p>
                                            <div>
                                                @if($enquiry['status'] == 'unread')
                                                    <span class="badge bg-danger">Unread</span>
                                                @else
                                                    <span class="badge bg-secondary">Read</span>
                                                @endif
                                                @if($enquiry['priority'] == 'high')
                                                    <span class="badge bg-danger">High Priority</span>
                                                @elseif($enquiry['priority'] == 'medium')
                                                    <span class="badge bg-warning">Medium Priority</span>
                                                @else
                                                    <span class="badge bg-light text-dark">Low Priority</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <small class="text-muted">{{ $enquiry['created_at'] }}</small>
                                            <br>
                                            <small class="text-muted">{{ $enquiry['phone'] }}</small>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <div class="list-group-item text-center text-muted">
                                    No recent enquiries
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>