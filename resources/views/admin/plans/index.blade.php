@extends('layouts.app')

@section('title', 'Subscription Plans')

@section('content')

<body>
    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <div class="content">

            <!-- Page Header -->
            <div class="page-header mb-4">
                <div class="row align-items-center">
                    <div class="col">
                        <h3>Subscription Plans</h3>
                    </div>
                </div>
            </div>

            <!-- Plans Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Amount</th>
                                    <th>Interval</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($plans as $plan)
                                    <tr>
                                        <td>
                                            <strong>{{ $plan->name }}</strong>
                                            @if($plan->description)
                                                <br><small class="text-muted">{{ Str::limit($plan->description, 50) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            ${{ number_format($plan->amount, 2) }} {{ strtoupper($plan->currency) }}
                                        </td>
                                        <td>{{ ucfirst($plan->interval) }}</td>
                                        <td>
                                            @if($plan->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.plans.edit', $plan->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No subscription plans found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

</body>

@endsection
