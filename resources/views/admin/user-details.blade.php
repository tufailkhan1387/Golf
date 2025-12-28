@extends('layouts.app')

@section('title', 'User Details')

@section('content')

<body>
    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <div class="content">

            <!-- Page Header -->
            <div class="page-header mb-4">
                <div class="d-flex align-items-center">
                    <div class="col">
                        <h3>User Details</h3>
                    </div>
                   
                </div>
            </div>

            <div class="row">
                <!-- User Profile Card -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 text-white"><i class="fas fa-user"></i> Profile Information</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">User ID:</th>
                                    <td>{{ $user->id }}</td>
                                </tr>
                                <tr>
                                    <th>Name:</th>
                                    <td>{{ $user->name }}</td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <th>Phone:</th>
                                    <td>{{ $user->phone ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Role:</th>
                                    <td>
                                        @if($user->role === 'admin')
                                            <span class="badge bg-danger">Admin</span>
                                        @else
                                            <span class="badge bg-primary">User</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Registered Date:</th>
                                    <td>{{ $user->created_at->format('M d, Y h:i A') }}</td>
                                </tr>
                                <tr>
                                    <th>Email Verified:</th>
                                    <td>
                                        @if($user->email_verified_at)
                                            <span class="badge bg-success">Yes</span>
                                            <small class="text-muted d-block">{{ $user->email_verified_at->format('M d, Y') }}</small>
                                        @else
                                            <span class="badge bg-warning">No</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Subscription Details Card -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0 text-white"><i class="fas fa-credit-card"></i> Subscription Details</h5>
                        </div>
                        <div class="card-body">
                            @if($subscription)
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="40%">Status:</th>
                                        <td>
                                            @if($subscription->isSubscribe)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Free Trial:</th>
                                        <td>
                                            @if($subscription->isFreeTrial)
                                                <span class="badge bg-info">Yes</span>
                                            @else
                                                <span class="badge bg-secondary">No</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @if($subscription->trial_started_at)
                                        <tr>
                                            <th>Trial Started:</th>
                                            <td>{{ $subscription->trial_started_at->format('M d, Y') }}</td>
                                        </tr>
                                    @endif
                                    @if($subscription->trial_ends_at)
                                        <tr>
                                            <th>Trial Ends:</th>
                                            <td>
                                                {{ $subscription->trial_ends_at->format('M d, Y') }}
                                                @if($subscription->hasTrialExpired())
                                                    <span class="badge bg-danger ms-2">Expired</span>
                                                @else
                                                    <span class="badge bg-success ms-2">Active</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                </table>

                                @if($subscriptionPlan)
                                    <hr>
                                    <h6 class="text-muted mb-3"><i class="fas fa-box"></i> Current Plan</h6>
                                    <table class="table table-borderless">
                                        <tr>
                                            <th width="40%">Plan Name:</th>
                                            <td><strong>{{ $subscriptionPlan->name }}</strong></td>
                                        </tr>
                                        <tr>
                                            <th>Amount:</th>
                                            <td>
                                                <strong class="text-success">
                                                    ${{ number_format($subscriptionPlan->amount, 2) }}
                                                </strong>
                                                <small class="text-muted">/ {{ $subscriptionPlan->interval }}</small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Currency:</th>
                                            <td>{{ strtoupper($subscriptionPlan->currency) }}</td>
                                        </tr>
                                        @if($subscriptionPlan->description)
                                            <tr>
                                                <th>Description:</th>
                                                <td>{{ $subscriptionPlan->description }}</td>
                                            </tr>
                                        @endif
                                        @if($subscriptionPlan->features)
                                            <tr>
                                                <th>Features:</th>
                                                <td>
                                                    <ul class="mb-0">
                                                        @foreach($subscriptionPlan->features as $feature)
                                                            <li>{{ $feature }}</li>
                                                        @endforeach
                                                    </ul>
                                                </td>
                                            </tr>
                                        @endif
                                    </table>
                                @else
                                    <div class="alert alert-info mt-3">
                                        <i class="fas fa-info-circle"></i> No active subscription plan found.
                                    </div>
                                @endif
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i> This user has no subscription record.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</body>

@endsection
