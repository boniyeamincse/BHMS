<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pharmacist Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-5">
        <div class="container-fluid">
            <div class="row g-4">

                <!-- Medicine Stock Overview -->
                <div class="col-12">
                    <h4 class="mb-3">Medicine Stock Overview</h4>

                    <!-- Overall Stock Summary -->
                    <div class="row g-3 mb-4">
                        <div class="col-lg-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <i class="bi bi-capsule-pill fs-1"></i>
                                    <h5>Total Medicines</h5>
                                    <h3>{{ $medicine_stock['summary']['total_medicines'] ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <i class="bi bi-exclamation-triangle fs-1"></i>
                                    <h5>Low Stock</h5>
                                    <h3>{{ $medicine_stock['summary']['low_stock_count'] ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center">
                                    <i class="bi bi-alarm fs-1"></i>
                                    <h5>Critical Stock</h5>
                                    <h3>{{ $medicine_stock['summary']['critical_count'] ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="card bg-secondary text-white">
                                <div class="card-body text-center">
                                    <i class="bi bi-clock-history fs-1"></i>
                                    <h5>Expiring Soon</h5>
                                    <h3>{{ $medicine_stock['summary']['expiring_soon_count'] ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Medicine Stock Alerts -->
                    <div class="card">
                        <div class="card-header bg-warning">
                            <h6 class="mb-0 text-dark">Stock Alerts & Warnings</h6>
                        </div>
                        <div class="card-body">
                            @if(count($medicine_stock['alerts'] ?? []) > 0)
                                <div class="row">
                                    @foreach($medicine_stock['alerts'] as $alert)
                                        <div class="col-md-6 mb-3">
                                            <div class="alert {{ $alert['alert_type'] == 'low_stock' ? 'alert-warning' : 'alert-info' }} py-2">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div class="flex-grow-1">
                                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                                            <strong>{{ $alert['medicine_name'] }}</strong>
                                                            @if($alert['alert_type'] == 'low_stock')
                                                                <span class="badge bg-{{ $alert['alert_level'] == 'Critical' ? 'danger' : 'warning' }}">
                                                                    {{ $alert['alert_level'] }}
                                                                </span>
                                                            @else
                                                                <small class="badge bg-secondary">{{ $alert['days_left'] }} days left</small>
                                                            @endif
                                                        </div>

                                                        @if($alert['alert_type'] == 'low_stock')
                                                            <small>Current: {{ $alert['current_stock'] }} | Min: {{ $alert['min_stock'] }}</small>
                                                        @else
                                                            <small>Expires: {{ \Carbon\Carbon::parse($alert['expiry_date'])->format('M d, Y') }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center text-muted">
                                    <i class="bi bi-check-circle fs-2 text-success"></i>
                                    <p>All medicines are within acceptable stock levels</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Prescriptions Pending Fulfillment -->
                <div class="col-lg-6">
                    <h4 class="mb-3">Prescriptions Pending Fulfillment</h4>

                    <!-- Summary Cards -->
                    <div class="row g-2 mb-3">
                        <div class="col-4">
                            <div class="bg-primary text-white text-center p-2 rounded">
                                <strong>{{ $pending_prescriptions['summary']['total_pending'] ?? 0 }}</strong>
                                <br><small>Total Pending</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="bg-warning text-white text-center p-2 rounded">
                                <strong>{{ $pending_prescriptions['summary']['urgent_count'] ?? 0 }}</strong>
                                <br><small>Urgent</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="bg-danger text-white text-center p-2 rounded">
                                <strong>{{ $pending_prescriptions['summary']['overdue_count'] ?? 0 }}</strong>
                                <br><small>Overdue</small>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Prescriptions List -->
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Prescription Queue</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Patient</th>
                                            <th>Type</th>
                                            <th>Amount</th>
                                            <th>Due Date</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($pending_prescriptions['prescriptions'] ?? [] as $rx)
                                        <tr>
                                            <td>
                                                <strong>{{ $rx['patient_name'] }}</strong>
                                                <br><small class="text-muted">{{ $rx['prescription_date'] }}</small>
                                            </td>
                                            <td>{{ $rx['prescription_type'] }}</td>
                                            <td>${{ number_format($rx['amount'], 2) }}</td>
                                            <td>
                                                @if($rx['days_overdue'] > 0)
                                                    <span class="text-danger">{{ $rx['days_overdue'] }} days ago</span>
                                                @else
                                                    <span class="text-success">{{ $rx['due_date'] }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $rx['urgency'] == 'Urgent' ? 'danger' : ($rx['urgency'] == 'High' ? 'warning' : 'info') }}">
                                                    {{ $rx['urgency'] }}
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary">Fulfill</button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">No pending prescriptions</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sales/Bills Generated Today -->
                <div class="col-lg-6">
                    <h4 class="mb-3">Today's Sales & Transactions</h4>

                    <!-- Sales Summary -->
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="bg-success text-white text-center p-3 rounded">
                                <strong>${{ number_format($today_sales['summary']['total_sales'] ?? 0, 2) }}</strong>
                                <br><small>Total Sales</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-primary text-white text-center p-3 rounded">
                                <strong>{{ $today_sales['summary']['transaction_count'] ?? 0 }}</strong>
                                <br><small>Transactions</small>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method Breakdown -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Payment Methods</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-2">
                                <div class="col-4">
                                    <div class="text-center">
                                        <strong class="d-block">${{ number_format($today_sales['summary']['cash_payments'] ?? 0, 2) }}</strong>
                                        <small class="text-muted">Cash</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="text-center">
                                        <strong class="d-block">${{ number_format($today_sales['summary']['card_payments'] ?? 0, 2) }}</strong>
                                        <small class="text-muted">Card</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="text-center">
                                        <strong class="d-block">${{ number_format($today_sales['summary']['online_payments'] ?? 0, 2) }}</strong>
                                        <small class="text-muted">Online</small>
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <div class="text-center">
                                <small class="text-muted">Growth vs Yesterday</small>
                                <br>
                                @if(($today_sales['summary']['sales_growth'] ?? 0) > 0)
                                    <span class="text-success">
                                        <strong>+{{ $today_sales['summary']['sales_growth'] ?? 0 }}%</strong>
                                    </span>
                                @else
                                    <span class="text-danger">
                                        <strong>{{ ($today_sales['summary']['sales_growth'] ?? 0) }}%</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Recent Sales -->
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Recent Transactions</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                @forelse($today_sales['recent_sales'] ?? [] as $sale)
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $sale['patient_name'] }}</strong>
                                            <br><small class="text-muted">Invoice #{{ $sale['invoice_id'] }}</small>
                                        </div>
                                        <div class="text-end">
                                            <strong class="text-success">${{ number_format($sale['amount'], 2) }}</strong>
                                            <br><small class="text-muted">{{ $sale['payment_method'] }} at {{ $sale['time'] }}</small>
                                        </div>
                                    </div>
                                </li>
                                @empty
                                <li class="list-group-item text-center text-muted">No transactions today</li>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Supplier/Restock Alerts -->
                <div class="col-12">
                    <h4 class="mb-3">Supplier & Restock Alerts</h4>

                    <!-- Supplier Summary Stats -->
                    <div class="row g-3 mb-4">
                        <div class="col-lg-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <i class="bi bi-truck fs-1"></i>
                                    <h5>Total Suppliers</h5>
                                    <h3>{{ $supplier_alerts['summary']['total_suppliers'] ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <i class="bi bi-check-circle-fill fs-1"></i>
                                    <h5>Active Suppliers</h5>
                                    <h3>{{ $supplier_alerts['summary']['active_suppliers'] ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <i class="bi bi-clock fs-1"></i>
                                    <h5>Pending Suppliers</h5>
                                    <h3>{{ $supplier_alerts['summary']['pending_suppliers'] ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center">
                                    <i class="bi bi-receipt fs-1"></i>
                                    <h5>Restock Needed</h5>
                                    <h3>{{ $supplier_alerts['summary']['restock_alerts_count'] ?? 0 }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Supplier Alerts -->
                    <div class="card">
                        <div class="card-body">
                            @if(count($supplier_alerts['alerts'] ?? []) > 0)
                                <div class="row">
                                    @foreach($supplier_alerts['alerts'] as $alert)
                                        <div class="col-md-6 mb-3">
                                            <div class="alert {{ $alert['alert_type'] == 'supplier_status' ? 'alert-warning' : 'alert-info' }}">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <strong>{{ $alert['supplier_name'] }}</strong>
                                                        <br>
                                                        @if($alert['alert_type'] == 'supplier_status')
                                                            <small>Status: <span class="text-uppercase">{{ $alert['status'] }}</span></small>
                                                        @else
                                                            <small>Last order: {{ $alert['days_since_order'] }} days ago</small>
                                                        @endif
                                                    </div>
                                                    <button class="btn btn-sm btn-outline-primary">Contact</button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center text-muted">
                                    <i class="bi bi-check-circle fs-2 text-success"></i>
                                    <p>All suppliers are active and orders are up to date</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>