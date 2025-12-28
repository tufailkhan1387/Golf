@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')

<body>
    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <div class="content">

            <!-- Page Header -->
            <div class="page-header mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <h3>Admin Dashboard</h3>
                  
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <div class="card text-white bg-primary shadow-sm border-0">
                        <div class="card-body text-center">
                            <h5 class="card-title">Total Users</h5>
                            <p class="card-text display-6 fw-bold">{{ $totalUsers }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card text-white bg-success shadow-sm border-0">
                        <div class="card-body text-center">
                            <h5 class="card-title">Total Plans</h5>
                            <p class="card-text display-6 fw-bold">{{ $totalPlans }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card text-white bg-warning shadow-sm border-0">
                        <div class="card-body text-center">
                            <h5 class="card-title">Subscribed Users</h5>
                            <p class="card-text display-6 fw-bold">{{ $subscribedUsers }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card text-white bg-info shadow-sm border-0">
                        <div class="card-body text-center">
                            <h5 class="card-title">Total Revenue</h5>
                            <p class="card-text display-6 fw-bold">${{ number_format($totalRevenue, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <!-- Monthly Registered Users -->
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0">New Registered Users (This Month)</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($monthlyUsers as $user)
                                            <tr>
                                                <td>{{ $user->name }}</td>
                                                <td>{{ $user->email }}</td>
                                                <td>{{ $user->created_at->format('M d') }}</td>
                                                <td>
                                                    <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">No new users this month</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Registration Graph -->
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0">User Registration Trend ({{ date('Y') }})</h5>
                        </div>
                        <div class="card-body">
                            <div style="height: 300px;">
                                <canvas id="registrationChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('registrationChart').getContext('2d');
            const registrationChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'New Users',
                        data: @json($chartData),
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>

@endsection
