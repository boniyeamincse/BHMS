<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Lab Technician Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-5">
        <div class="container-fluid">
            <div class="row g-4">

                <!-- Pending Test Requests -->
                <div class="col-12">
                    <h4 class="mb-3">Pending Test Requests</h4>

                    <!-- Summary Overview -->
                    <div class="row g-3 mb-4">
                        <div class="col-lg-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <i class="bi bi-clipboard-data-fill fs-1"></i>
                                    <h5>Total Pending</h5>
                                    <h3>{{ $pending_requests['summary']['total_pending'] ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center">
                                    <i class="bi bi-exclamation-triangle-fill fs-1"></i>
                                    <h5>Urgent</h5>
                                    <h3>{{ $pending_requests['summary']['urgent_count'] ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <i class="bi bi-droplet-fill fs-1"></i>
                                    <h5>Pathology</h5>
                                    <h3>{{ $pending_requests['summary']['pathology_count'] ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <i class="bi bi-x-ray fs-1"></i>
                                    <h5>Radiology</h5>
                                    <h3>{{ $pending_requests['summary']['radiology_count'] ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Test Requests Table -->
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Patient</th>
                                            <th>Test Type</th>
                                            <th>Test Name</th>
                                            <th>Priority</th>
                                            <th>Requested By</th>
                                            <th>Request Time</th>
                                            <th>Est. Time</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($pending_requests['requests'] ?? [] as $request)
                                        <tr>
                                            <td>
                                                <strong>{{ $request['patient_name'] }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-
                                                    {{ $request['test_type'] == 'pathology' ? 'success' :
                                                       ($request['test_type'] == 'radiology' ? 'info' : 'warning') }}">
                                                    {{ ucfirst($request['test_type']) }}
                                                </span>
                                            </td>
                                            <td>{{ $request['test_name'] }}</td>
                                            <td>
                                                <span class="badge bg-{{ $request['priority'] == 'Urgent' ? 'danger' : 'warning' }}">
                                                    {{ $request['priority'] }}
                                                </span>
                                            </td>
                                            <td>{{ $request['requested_by'] }}</td>
                                            <td>{{ $request['request_date'] }}</td>
                                            <td>{{ $request['estimated_time'] }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-success">Start Test</button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted">No pending test requests</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tests in Progress vs Completed -->
                <div class="col-lg-8">
                    <h4 class="mb-3">Tests in Progress vs Completed</h4>

                    <!-- Test Progress Summary -->
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="bg-success text-white text-center p-3 rounded">
                                <h5>{{ $test_progress['total_tests_today'] ?? 0 }}</h5>
                                <small>Total Tests Today</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-primary text-white text-center p-3 rounded">
                                <h5>{{ $test_progress['completion_rate'] ?? 0 }}%</h5>
                                <small>Completion Rate</small>
                            </div>
                        </div>
                    </div>

                    <!-- Tests in Progress -->
                    <div class="card mb-3">
                        <div class="card-header bg-warning">
                            <h6 class="mb-0 text-dark">Tests in Progress ({{ $test_progress['in_progress_tests'] ?? 0 }})</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Test Name</th>
                                            <th>Patient</th>
                                            <th>Technician</th>
                                            <th>Progress</th>
                                            <th>Est. Complete</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($test_progress['tests_in_progress'] ?? [] as $test)
                                        <tr>
                                            <td>{{ $test['test_name'] }}</td>
                                            <td>{{ $test['patient_name'] }}</td>
                                            <td>{{ $test['technician'] }}</td>
                                            <td>
                                                <div class="progress" style="width: 80px; height: 8px;">
                                                    <div class="progress-bar bg-{{ $test['progress_percentage'] > 80 ? 'success' : ($test['progress_percentage'] > 50 ? 'warning' : 'danger') }}"
                                                         style="width: {{ $test['progress_percentage'] }}%">
                                                    </div>
                                                </div>
                                                <small>{{ $test['progress_percentage'] }}%</small>
                                            </td>
                                            <td>{{ $test['estimated_completion'] }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary">Update</button>
                                                <button class="btn btn-sm btn-outline-success">Complete</button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">No tests in progress</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Recently Completed Tests -->
                    <div class="card">
                        <div class="card-header bg-success">
                            <h6 class="mb-0 text-white">Recently Completed ({{ count($test_progress['recently_completed']) }}/{{ $test_progress['completed_tests'] ?? 0 }})</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Test Name</th>
                                            <th>Patient</th>
                                            <th>Technician</th>
                                            <th>Duration</th>
                                            <th>Completed</th>
                                            <th>Result</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($test_progress['recently_completed'] ?? [] as $test)
                                        <tr>
                                            <td>{{ $test['test_name'] }}</td>
                                            <td>{{ $test['patient_name'] }}</td>
                                            <td>{{ $test['technician'] }}</td>
                                            <td>{{ $test['duration'] }}</td>
                                            <td>{{ $test['completed_time'] }}</td>
                                            <td>
                                                <span class="badge bg-{{ $test['result_status'] == 'Normal' ? 'success' : 'warning text-dark' }}">
                                                    {{ $test['result_status'] }}
                                                </span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">No recently completed tests</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reports Pending Review & Equipment Usage -->
                <div class="col-lg-4">
                    <div class="row g-4">

                        <!-- Reports Pending Review/Sign-off -->
                        <div class="col-12">
                            <h4 class="mb-3">Reports Pending Review</h4>

                            <!-- Reports Summary -->
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <div class="bg-warning text-white text-center p-3 rounded">
                                        <strong>{{ $pending_reports['summary']['total_pending'] ?? 0 }}</strong>
                                        <br><small>Total Pending</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-danger text-white text-center p-3 rounded">
                                        <strong>{{ $pending_reports['summary']['urgent_count'] ?? 0 }}</strong>
                                        <br><small>Urgent</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Pending Reports List -->
                            <div class="card">
                                <div class="card-body p-0">
                                    <div class="list-group list-group-flush">
                                        @forelse($pending_reports['reports'] ?? [] as $report)
                                        <li class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="flex-grow-1">
                                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                                        <strong>{{ $report['test_name'] }}</strong>
                                                        <span class="badge bg-{{ $report['priority'] == 'Urgent' ? 'danger' : 'warning' }}">
                                                            {{ $report['priority'] }}
                                                        </span>
                                                    </div>
                                                    <small>{{ $report['patient_name'] }} - {{ $report['technician'] }}</small>
                                                    <br><small class="text-muted">{{ $report['completed_time'] }} ({{ $report['waiting_time'] }} waiting)</small>
                                                    <br><small>{{ $report['result_summary'] }}</small>
                                                </div>
                                                <button class="btn btn-sm btn-outline-primary mt-2">Review</button>
                                            </div>
                                        </li>
                                        @empty
                                        <li class="list-group-item text-center text-muted">No reports pending review</li>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Equipment Usage Logs -->
                        <div class="col-12">
                            <h4 class="mb-3">Equipment Usage</h4>

                            <!-- Equipment Summary -->
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <div class="bg-info text-white text-center p-3 rounded">
                                        <strong>{{ $equipment_logs['summary']['total_equipment'] ?? 0 }}</strong>
                                        <br><small>Total Equipment</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-danger text-white text-center p-3 rounded">
                                        <strong>{{ $equipment_logs['summary']['maintenance_required'] ?? 0 }}</strong>
                                        <br><small>Maintenance</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Equipment Status -->
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Equipment Status</h6>
                                </div>
                                <div class="card-body p-0">
                                    @foreach($equipment_logs['equipment_status'] ?? [] as $equip)
                                    <div class="border-bottom p-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <small class="d-block">{{ $equip['name'] }}</small>
                                                <small class="text-muted">{{ round($equip['total_usage_time'] / 60, 1) }} hours today</small>
                                            </div>
                                            <span class="badge bg-success">Operational</span>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Recent Usage Logs -->
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Recent Usage Logs</h6>
                                </div>
                                <div class="card-body p-0">
                                    <ul class="list-group list-group-flush">
                                        @forelse(array_slice($equipment_logs['usage_logs'] ?? [], 0, 5) as $log)
                                        <li class="list-group-item py-2">
                                            <small><strong>{{ $log['equipment_name'] }}</strong></small>
                                            <br><small>{{ $log['patient'] }} - {{ $log['test_type'] }} ({{ $log['duration_min'] }} min)</small>
                                            <br><small class="text-muted">{{ $log['start_time'] }} by {{ $log['technician'] }}</small>
                                        </li>
                                        @empty
                                        <li class="list-group-item text-center text-muted">No recent usage logs</li>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>

                            <!-- Maintenance Alerts -->
                            @if(count($equipment_logs['maintenance_alerts'] ?? []) > 0)
                            <div class="alert alert-danger mt-3">
                                <h6 class="alert-heading mb-2">⚠️ Maintenance Required</h6>
                                @foreach($equipment_logs['maintenance_alerts'] as $alert)
                                <p class="mb-1"><strong>{{ $alert['equipment_name'] }}</strong> - Last maintenance: {{ $alert['last_maintenance'] }}</p>
                                @endforeach
                            </div>
                            @endif
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>