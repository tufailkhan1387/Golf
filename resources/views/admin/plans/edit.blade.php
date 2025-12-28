@extends('layouts.app')

@section('title', 'Edit Plan')

@section('content')

<body>
    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <div class="content">

            <!-- Page Header -->
            <div class="page-header mb-4">
                <div class="row align-items-center">
                    <div class="col">
                        <h3>Edit Plan: {{ $plan->name }}</h3>
                    </div>
                    <div class="col-auto ms-auto">
                        <a href="{{ route('admin.plans') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Plans
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <h5><i class="fas fa-exclamation-triangle"></i> Please fix the following errors:</h5>
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('admin.plans.update', $plan->stripe_price_id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label class="form-label">Plan Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $plan->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Amount <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" name="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount', $plan->amount) }}" required>
                                        @error('amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Currency <span class="text-danger">*</span></label>
                                        <input type="text" name="currency" class="form-control @error('currency') is-invalid @enderror" value="{{ old('currency', $plan->currency) }}" required>
                                        @error('currency')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Interval <span class="text-danger">*</span></label>
                                        <select name="interval" class="form-select @error('interval') is-invalid @enderror" required>
                                            <option value="day" {{ old('interval', $plan->interval) == 'day' ? 'selected' : '' }}>Day</option>
                                            <option value="week" {{ old('interval', $plan->interval) == 'week' ? 'selected' : '' }}>Week</option>
                                            <option value="month" {{ old('interval', $plan->interval) == 'month' ? 'selected' : '' }}>Month</option>
                                            <option value="year" {{ old('interval', $plan->interval) == 'year' ? 'selected' : '' }}>Year</option>
                                        </select>
                                        @error('interval')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Trial Days <span class="text-danger">*</span></label>
                                        <input type="number" name="trial_days" class="form-control @error('trial_days') is-invalid @enderror" value="{{ old('trial_days', $plan->trial_days ?? 7) }}" required>
                                        @error('trial_days')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Description <span class="text-danger">*</span></label>
                                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" required>{{ old('description', $plan->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Features (One per line)</label>
                                    <textarea name="features" class="form-control @error('features') is-invalid @enderror" rows="5">{{ old('features', is_array($plan->features) ? implode("\n", $plan->features) : $plan->features) }}</textarea>
                                    <small class="text-muted">Enter each feature on a new line.</small>
                                    @error('features')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input @error('ispopular') is-invalid @enderror" id="ispopular" name="ispopular" value="1" {{ old('ispopular', (isset($plan->popular) && $plan->popular)) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="ispopular">Mark as Popular</label>
                                    @error('ispopular')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i> <strong>Note:</strong> Changing the price or interval will create a new Price ID in Stripe and archive the old one.
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary">Update Plan on Stripe</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h5 class="card-title">Stripe Information</h5>
                            <p class="mb-1"><strong>Stripe Price ID:</strong></p>
                            <p class="text-muted small">{{ $plan->stripe_price_id }}</p>
                            
                            <p class="mb-1"><strong>Stripe Product ID:</strong></p>
                            <p class="text-muted small">{{ $plan->stripe_product_id }}</p>
                            
                            <div class="alert alert-info mt-3 mb-0">
                                <i class="fas fa-info-circle"></i> To change the price or billing interval, please create a new plan in Stripe and sync it.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</body>

@endsection
