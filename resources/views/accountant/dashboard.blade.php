<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Accountant Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-5">
        <div class="container-fluid">
            <div class="row g-4">

                <!-- Total Revenue Overview -->
                <div class="col-12">
                    <h4 class="mb-3">Total Revenue Overview</h4>
                    <div class="row g-3">
                        <div class="col-lg-4">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <i class="bi bi-currency-dollar fs-1"></i>
                                    <h5>Month-to-Date Revenue</h5>
                                    <h3>${{ number_format($revenue_data['month_to_date'], 2) }}</h3>
                                    @if($revenue_data['monthly_change_percent'] != 0)
                                        <small class="{{ $revenue_data['monthly_change_percent'] > 0 ? 'text-warning' : 'text-light' }}">
                                            {{ $revenue_data['monthly_change_percent'] > 0 ? '+' : '' }}{{ $revenue_data['monthly_change_percent'] }}% vs last month
                                        </small>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <i class="bi bi-graph-up fs-1"></i>
                                    <h5>Year-to-Date Revenue</h5>
                                    <h3>${{ number_format($revenue_data['year_to_date'], 2) }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <i class="bi bi-calculator fs-1"></i>
                                    <h5>Last Month Revenue</h5>
                                    <h3>${{ number_format($revenue_data['previous_month'], 2) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Bills & Invoices -->
                <div class="col-lg-8">
                    <h4 class="mb-3">Pending Bills & Invoices</h4>
                    <div class="card">
                        <div class="card-body">
                            <!-- Summary Cards -->
                            <div class="row g-2 mb-3">
                                <div class="col-3">
                                    <div class="bg-warning text-dark text-center p-2 rounded">
                                        <small>Total Pending</small>
                                        <br><strong>{{ $pending_invoices['summary']['pending_count'] }}</strong>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="bg-danger text-white text-center p-2 rounded">
                                        <small>Overdue</small>
                                        <br><strong>{{ $pending_invoices['summary']['overdue_count'] }}</strong>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="bg-warning text-dark text-center p-2 rounded">
                                        <small>Amount Due</small>
                                        <br><strong>${{ number_format($pending_invoices['summary']['total_pending_amount'], 2) }}</strong>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="bg-danger text-white text-center p-2 rounded">
                                        <small>Overdue Amount</small>
                                        <br><strong>${{ number_format($pending_invoices['summary']['overdue_amount'], 2) }}</strong>
                                    </div>
                                </div>
                            </div>

                            <!-- Invoice Table -->
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Patient</th>
                                            <th>Total Amount</th>
                                            <th>Paid</th>
                                            <th>Remaining</th>
                                            <th>Due Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($pending_invoices['invoices'] ?? [] as $invoice)
                                        <tr>
                                            <td>{{ $invoice['patient_name'] }}</td>
                                            <td>${{ number_format($invoice['amount'], 2) }}</td>
                                            <td>${{ number_format($invoice['paid_amount'], 2) }}</td>
                                            <td>${{ number_format($invoice['remaining_amount'], 2) }}</td>
                                            <td>{{ $invoice['due_date'] }}</td>
                                            <td>
                                                @if($invoice['status'] == 'pending')
                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                @else
                                                    <span class="badge bg-danger">Overdue</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">No pending invoices</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payments Received vs Due & Payrolls -->
                <div class="col-lg-4">
                    <div class="row g-4">
                        <!-- Payments Received vs Due -->
                        <div class="col-12">
                            <h4 class="mb-3">Payments Received vs Due</h4>
                            <div class="card">
                                <div class="card-body">
                                    <div class="row g-2 mb-3">
                                        <div class="col-6">
                                            <div class="bg-success text-white text-center p-3 rounded">
                                                <small>Received This Month</small>
                                                <br><strong>${{ number_format($payment_data['received_this_month'], 2) }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="bg-danger text-white text-center p-3 rounded">
                                                <small>Total Due</small>
                                                <br><strong>${{ number_format($payment_data['total_dues'], 2) }}</strong>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="border rounded p-3 text-center mb-2">
                                        <small class="text-muted">Collection Rate</small>
                                        <br><strong>{{ $payment_data['collection_rate'] }}%</strong>
                                    </div>

                                    <!-- Recent Payments -->
                                    <small class="text-muted">Recent Payments</small>
                                    <ul class="list-group list-group-flush mt-2">
                                        @forelse($payment_data['recent_payments'] ?? [] as $payment)
                                        <li class="list-group-item py-1 px-0 border-0">
                                            <small>{{ $payment['patient_name'] }} - ${{ number_format($payment['amount'], 2) }}</small>
                                            <br><small class="text-muted">{{ $payment['payment_date'] }} via {{ $payment['method'] }}</small>
                                        </li>
                                        @empty
                                        <li class="list-group-item py-1 px-0 border-0 text-muted">
                                            <small>No recent payments</small>
                                        </li>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Payrolls Processed/Pending -->
                        <div class="col-12">
                            <h4 class="mb-3">Payroll Information</h4>
                            <div class="card">
                                <div class="card-body">
                                    <div class="row g-2 mb-3">
                                        <div class="col-4">
                                            <div class="text-center">
                                                <strong>{{ $payroll_data['active_employees'] }}</strong>
                                                <br><small class="text-muted">Active Staff</small>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="text-center">
                                                <strong>{{ $payroll_data['processed_this_month'] }}</strong>
                                                <br><small class="text-success">Processed</small>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="text-center">
                                                <strong>{{ $payroll_data['pending_this_month'] }}</strong>
                                                <br><small class="text-warning">Pending</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="border rounded p-2 text-center mb-2">
                                        <small class="text-muted">Total Payroll Cost</small>
                                        <br><strong>${{ number_format($payroll_data['total_payroll_cost'], 2) }}</strong>
                                    </div>

                                    <div class="row g-2">
                                        <div class="col-6">
                                            <div class="bg-success text-white text-center p-2 rounded">
                                                <small>Processed Amount</small>
                                                <br><strong class="fs-6">${{ number_format($payroll_data['processed_amount'], 2) }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="bg-warning text-white text-center p-2 rounded">
                                                <small>Pending Amount</small>
                                                <br><strong class="fs-6">${{ number_format($payroll_data['pending_amount'], 2) }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Income vs Expense Chart -->
                <div class="col-12">
                    <h4 class="mb-3">Income vs Expense Trends (Last 12 Months)</h4>
                    <div class="card">
                        <div class="card-body">
                            <canvas id="incomeExpenseChart" style="max-height: 400px;"></canvas>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Chart.js Script -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
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
                                borderColor: 'rgb(75, 192, 192)',
                                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                                fill: true,
                                tension: 0.4
                            }, {
                                label: 'Expenses',
                                data: chartData.expense_data.map(item => item.amount),
                                borderColor: 'rgb(255, 99, 132)',
                                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                                fill: true,
                                tension: 0.4
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                title: {
                                    display: true,
                                    text: 'Monthly Income vs Expenses'
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return context.dataset.label + ': $' + context.parsed.y.toLocaleString();
                                        }
                                    }
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
    </div>
</x-app-layout>