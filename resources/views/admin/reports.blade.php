@extends('layouts.app')

@section('title', 'Revenue Reports')

@section('content')

<body>
    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <div class="content">

            <!-- Page Header -->
            <div class="page-header mb-4">
                <div class="row align-items-center">
                    <div class="col">
                        <h3>Revenue Reports</h3>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="card mb-4">
                <div class="card-body">
                    <form action="{{ route('admin.reports') }}" method="GET">
                        <div class="row align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">Start Date</label>
                                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">End Date</label>
                                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                                <a href="{{ route('admin.reports') }}" class="btn btn-secondary">
                                    <i class="fas fa-undo"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Total Revenue Card -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h4 class="text-white">Total Revenue (Selected Period)</h4>
                            <h2 class="text-white font-weight-bold">${{ number_format($totalRevenue, 2) }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transactions Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Transaction History</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>User</th>
                                    <th>Plan</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $transaction)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($transaction->created_at)->format('M d, Y h:i A') }}</td>
                                        <td>
                                            <div>{{ $transaction->user_name }}</div>
                                            <small class="text-muted">{{ $transaction->user_email }}</small>
                                        </td>
                                        <td>{{ $transaction->plan_name }}</td>
                                        <td>
                                            <span class="text-success fw-bold">
                                                ${{ number_format($transaction->amount, 2) }}
                                            </span>
                                            <small class="text-muted">{{ strtoupper($transaction->currency) }}</small>
                                        </td>
                                        <td><span class="badge bg-success">Paid</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No transactions found for the selected period</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $transactions->appends(['start_date' => $startDate, 'end_date' => $endDate])->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>

</body>

@endsection
