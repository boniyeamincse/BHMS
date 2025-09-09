<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Hospital Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-5">
        <div class="container-fluid">
            <div class="row g-4">

                <!-- Staff Count Cards -->
                <div class="col-12">
                    <h4 class="mb-3">Staff Overview</h4>
                    <div class="row g-3">
                        <div class="col-lg-3 col-md-6">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <i class="bi bi-people-fill fs-1"></i>
                                    <h5>Doctors</h5>
                                    <h3>{{ $staff_counts['Doctor'] ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <i class="bi bi-activity fs-1"></i>
                                    <h5>Nurses</h5>
                                    <h3>{{ $staff_counts['Nurse'] ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <i class="bi bi-reception-4 fs-1"></i>
                                    <h5>Receptionists</h5>
                                    <h3>{{ $staff_counts['Receptionist'] ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <i class="bi bi-capsule fs-1"></i>
                                    <h5>Pharmacists</h5>
                                    <h3>{{ $staff_counts['Pharmacist'] ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Patient Stats -->
                <div class="col-12">
                    <h4 class="mb-3">Patient Statistics</h4>
                    <div class="row g-3">
                        <div class="col-lg-3 col-md-6">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h5>Total Patients</h5>
                                    <h3 class="text-primary">{{ $patient_stats['total_patients'] ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h5>Admitted Patients</h5>
                                    <h3 class="text-success">{{ $patient_stats['admitted_patients'] ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h5>This Month Discharges</h5>
                                    <h3 class="text-info">{{ $patient_stats['discharged_patients'] ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h5>This Month OPD Visits</h5>
                                    <h3 class="text-warning">{{ $patient_stats['opd_visits'] ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Finance Overview -->
                <div class="col-lg-6">
                    <h4 class="mb-3">Finance Overview</h4>
                    <div class="card">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="border rounded p-3 text-center">
                                        <small class="text-muted">Total Invoices</small>
                                        <h4 class="text-success">${{ number_format($finance_data['total_invoices'] ?? 0, 2) }}</h4>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="border rounded p-3 text-center">
                                        <small class="text-muted">Total Payments</small>
                                        <h4 class="text-primary">${{ number_format($finance_data['total_payments'] ?? 0, 2) }}</h4>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="border rounded p-3 text-center">
                                        <small class="text-muted">Outstanding</small>
                                        <h4 class="text-warning">${{ number_format($finance_data['due_amount'] ?? 0, 2) }}</h4>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="border rounded p-3 text-center">
                                        <small class="text-muted">This Month Invoices</small>
                                        <h4 class="text-info">${{ number_format($finance_data['monthly_invoices'] ?? 0, 2) }}</h4>
                                    </div>
                                </div>
                            </div>
           
                        </div>
                    </div>
                </div>
           
                <!-- Chart.js Script -->
                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Income Expense Chart
                        const ctx = document.getElementById('incomeExpenseChart');
                        if (ctx) {
                            const chartData = @json($chart_data);
           
                            new Chart(ctx, {
                                type: 'line',
                                data: {
                                    labels: chartData.income_data.map(item => item.month),
                                    datasets: [{
                                        label: 'Income',
                                        data: chartData.income_data.map(item => item.amount),
                                        borderColor: 'rgb(54, 162, 235)',
                                        backgroundColor: 'rgba(54, 162, 235, 0.1)',
                                        fill: true,
                                        tension: 0.1
                                    }, {
                                        label: 'Expenses',
                                        data: chartData.expense_data.map(item => item.amount),
                                        borderColor: 'rgb(255, 99, 132)',
                                        backgroundColor: 'rgba(255, 99, 132, 0.1)',
                                        fill: true,
                                        tension: 0.1
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    plugins: {
                                        title: {
                                            display: true,
                                            text: 'Monthly Income & Expenses'
                                        }
                                    },
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            ticks: {
                                                callback: function(value, index, values) {
                                                    return '$' + value.toLocaleString();
                                                }
                                            }
                                        }
                                    }
                                }
                            });
                        }
                    });
                </script>
                <!-- Revenue/Expense Chart -->
                <div class="col-lg-6">
                    <h4 class="mb-3">Income/Expense Trends</h4>
                    <div class="card">
                        <div class="card-body">
                            <canvas id="incomeExpenseChart" style="max-height: 300px;"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Bed Availability -->
                <div class="col-lg-6">
                    <h4 class="mb-3">Bed Availability</h4>
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Ward</th>
                                            <th>Total Beds</th>
                                            <th>Available</th>
                                            <th>Occupied</th>
                                            <th>Maintenance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($bed_data ?? [] as $bed)
                                        <tr>
                                            <td>{{ $bed['ward_name'] }}</td>
                                            <td>{{ $bed['total_beds'] }}</td>
                                            <td><span class="badge bg-success">{{ $bed['available_beds'] }}</span></td>
                                            <td><span class="badge bg-danger">{{ $bed['occupied_beds'] }}</span></td>
                                            <td><span class="badge bg-warning">{{ $bed['maintenance_beds'] }}</span></td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">No bed data available</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Blood Stock Levels -->
                <div class="col-lg-6">
                    <h4 class="mb-3">Blood Stock Levels</h4>
                    <div class="card">
                        <div class="card-body">
                            @if($blood_data ?? [])
                                <div class="row g-3">
                                    @foreach($blood_data as $type => $units)
                                    <div class="col-6 col-md-3">
                                        <div class="border rounded p-3 text-center">
                                            <h6>{{ $type }}</h6>
                                            <h4 class="text-primary">{{ $units }}</h4>
                                            <small class="text-muted">units</small>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-center text-muted">No blood stock data available</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Announcements -->
                <div class="col-12">
                    <h4 class="mb-3">Recent Announcements</h4>
                    <div class="row g-3">
                        @forelse($announcements ?? [] as $announcement)
                        <div class="col-lg-6">
                            <div class="card border-info">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <small class="text-muted">{{ $announcement->scheduled_date->format('M d, Y') }}</small>
                                        @if($announcement->active)
                                            <span class="badge bg-success">Active</span>
                                        @endif
                                    </div>
                                    <p class="mb-0">{{ Str::limit($announcement->message, 100) }}</p>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body text-center text-muted">
                                    <p>No recent announcements</p>
                                </div>
                            </div>
                        </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>