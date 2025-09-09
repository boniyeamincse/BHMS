<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Case Handler Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-5">
        <div class="container-fluid">
            <div class="row g-4">

                <!-- Open Patient Cases -->
                <div class="col-12">
                    <h4 class="mb-3">Open Patient Cases</h4>

                    <!-- Case Statistics Overview -->
                    <div class="row g-3 mb-4">
                        <div class="col-lg-4">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <i class="bi bi-file-earmark-medical-fill fs-1"></i>
                                    <h5>Total Cases</h5>
                                    <h3>{{ $open_cases['total_cases'] ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <i class="bi bi-hospital fs-1"></i>
                                    <h5>IPD Cases</h5>
                                    <h3>{{ $open_cases['ipd_cases'] ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <i class="bi bi-person-check fs-1"></i>
                                    <h5>OPD Cases</h5>
                                    <h3>{{ $open_cases['opd_cases'] ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cases by Ward -->
                    <div class="card">
                        <div class="card-body">
                            <h6>Cases by Ward</h6>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Ward</th>
                                            <th>Total Patients</th>
                                            <th>IPD</th>
                                            <th>OPD</th>
                                            <th>Recent Cases</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($open_cases['cases_by_ward'] ?? [] as $ward)
                                        <tr>
                                            <td><strong>{{ $ward['ward_name'] }}</strong></td>
                                            <td><span class="badge bg-primary">{{ $ward['total_patients'] }}</span></td>
                                            <td><span class="badge bg-success">{{ $ward['ipd_patients'] }}</span></td>
                                            <td><span class="badge bg-info">{{ $ward['opd_patients'] }}</span></td>
                                            <td>
                                                @foreach($ward['patients'] as $patient)
                                                    <small class="d-block">{{ $patient['name'] }} ({{ $patient['days_admitted'] }}d)</small>
                                                @endforeach
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">No open cases found</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Admissions & Discharges -->
                <div class="col-lg-6">
                    <h4 class="mb-3">Admissions & Discharges This Month</h4>

                    <!-- Statistics Cards -->
                    <div class="row g-2 mb-3">
                        <div class="col-4">
                            <div class="bg-primary text-white text-center p-3 rounded">
                                <strong>{{ $admissions_discharges['this_month']['admissions'] ?? 0 }}</strong>
                                <br><small>Admissions</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="bg-success text-white text-center p-3 rounded">
                                <strong>{{ $admissions_discharges['this_month']['discharges'] ?? 0 }}</strong>
                                <br><small>Discharges</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="bg-info text-white text-center p-3 rounded">
                                <strong>{{ $admissions_discharges['this_month']['avg_stay_duration'] }}d</strong>
                                <br><small>Avg Stay</small>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Admissions -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">Recent Admissions</h6>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                @forelse($admissions_discharges['recent_admissions'] ?? [] as $admission)
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $admission['name'] }}</strong>
                                            <br><small class="text-muted">{{ $admission['ward_name'] }} | {{ $admission['type'] }}</small>
                                        </div>
                                        <div class="text-end">
                                            <small class="badge bg-primary">{{ $admission['admission_date'] }}</small>
                                        </div>
                                    </div>
                                </li>
                                @empty
                                <li class="list-group-item text-center text-muted">No recent admissions</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>

                    <!-- Recent Discharges -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Recent Discharges</h6>
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                @forelse($admissions_discharges['recent_discharges'] ?? [] as $discharge)
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $discharge['name'] }}</strong>
                                            <br><small class="text-muted">{{ $discharge['ward_name'] }} | {{ $discharge['type'] }}</small>
                                        </div>
                                        <div class="text-end">
                                            <small class="badge bg-success">{{ $discharge['discharge_date'] }}</small>
                                            <br><small class="text-muted">{{ $discharge['admission_duration'] }}d</small>
                                        </div>
                                    </div>
                                </li>
                                @empty
                                <li class="list-group-item text-center text-muted">No recent discharges</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Case Timelines Pending Updates -->
                <div class="col-lg-6">
                    <h4 class="mb-3">Case Timelines Pending Updates</h4>

                    <!-- Timeline Statistics -->
                    <div class="row g-2 mb-3">
                        <div class="col-4">
                            <div class="bg-warning text-white text-center p-3 rounded">
                                <strong>{{ $timeline_updates['total_pending_updates'] ?? 0 }}</strong>
                                <br><small>Total Pending</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="bg-danger text-white text-center p-3 rounded">
                                <strong>{{ $timeline_updates['urgent_updates'] ?? 0 }}</strong>
                                <br><small>Urgent</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="bg-info text-white text-center p-3 rounded">
                                <strong>{{ $timeline_updates['pending_updates'] ?? 0 }}</strong>
                                <br><small>Regular</small>
                            </div>
                        </div>
                    </div>

                    <!-- Cases Requiring Updates -->
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Patient</th>
                                            <th>Days Admitted</th>
                                            <th>Last Update</th>
                                            <th>Status</th>
                                            <th>Next Milestone</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($timeline_updates['cases_requiring_updates'] ?? [] as $case)
                                        <tr>
                                            <td>
                                                <strong>{{ $case['name'] }}</strong>
                                                <br><small class="text-muted">{{ $case['ward_name'] }}</small>
                                            </td>
                                            <td>
                                                <span class="{{ $case['days_admitted'] > 10 ? 'badge bg-danger' : 'badge bg-warning' }}">
                                                    {{ $case['days_admitted'] }}
                                                </span>
                                            </td>
                                            <td><small class="text-muted">{{ $case['last_update'] }}</small></td>
                                            <td>
                                                <span class="badge {{ $case['update_status'] == 'Urgent' ? 'bg-danger' : 'bg-warning' }}">
                                                    {{ $case['update_status'] }}
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $case['next_milestone'] }}</small>
                                                <br><button class="btn btn-sm btn-outline-primary mt-1">Update</button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">No cases requiring timeline updates</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ambulance Requests Assigned -->
                <div class="col-12">
                    <h4 class="mb-3">Ambulance Requests Assigned</h4>

                    <!-- Assignment Statistics -->
                    <div class="row g-3 mb-3">
                        <div class="col-lg-4">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <i class="bi bi-ambulance fs-1"></i>
                                    <h5>Total Requests</h5>
                                    <h3>{{ $ambulance_requests['total_requests'] ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <i class="bi bi-check-circle-fill fs-1"></i>
                                    <h5>Assigned</h5>
                                    <h3>{{ $ambulance_requests['assigned_requests'] ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <i class="bi bi-clock fs-1"></i>
                                    <h5>Pending</h5>
                                    <h3>{{ $ambulance_requests['pending_requests'] ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ambulance Requests Table -->
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Patient</th>
                                            <th>Ward</th>
                                            <th>Request Type</th>
                                            <th>Priority</th>
                                            <th>Status</th>
                                            <th>Assigned Ambulance</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($ambulance_requests['ambulance_requests'] ?? [] as $request)
                                        <tr>
                                            <td>
                                                <strong>{{ $request['name'] }}</strong>
                                                <br><small class="text-muted">{{ $request['type'] }}</small>
                                            </td>
                                            <td>{{ $request['ward_name'] }}</td>
                                            <td><span class="badge bg-primary">{{ $request['request_type'] }}</span></td>
                                            <td>
                                                <span class="badge
                                                    {{ $request['priority'] == 'Critical' ? 'bg-danger' :
                                                       ($request['priority'] == 'High' ? 'bg-warning' : 'bg-info') }}">
                                                    {{ $request['priority'] }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $request['assigned_status'] == 'Assigned' ? 'bg-success' : 'bg-warning' }}">
                                                    {{ $request['assigned_status'] }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($request['assigned_ambulance'])
                                                    <strong>{{ $request['assigned_ambulance'] }}</strong>
                                                @else
                                                    <em class="text-muted">Not assigned</em>
                                                @endif
                                            </td>
                                            <td>
                                                @if($request['assigned_status'] == 'Assigned')
                                                    <button class="btn btn-sm btn-outline-primary">Track</button>
                                                @else
                                                    <button class="btn btn-sm btn-primary">Assign</button>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">No ambulance requests</td>
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