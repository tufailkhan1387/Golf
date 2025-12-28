@extends('layouts.app')

@section('title', 'Email Notifications')

@section('content')

<body>
    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <div class="content">

            <!-- Page Header -->
            <div class="page-header mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <h3>Email Notifications</h3>
                    <a href="{{ route('admin.email-notifications.history') }}" class="btn btn-info">
                        <i class="fas fa-history me-2"></i>View History
                    </a>
                </div>
            </div>

            <!-- Email Notification Form -->
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.email-notifications.send') }}" method="POST">
                        @csrf

                        <!-- User Selection -->
                        <div class="mb-3">
                            <label for="user_id" class="form-label">Select User <span class="text-danger">*</span></label>
                            <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                                <option value="">-- Select a user --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Subject -->
                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="subject" 
                                   id="subject" 
                                   class="form-control @error('subject') is-invalid @enderror" 
                                   value="{{ old('subject') }}" 
                                   placeholder="Enter email subject"
                                   required>
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Message -->
                        <div class="mb-3">
                            <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                            <textarea name="message" 
                                      id="message" 
                                      class="form-control @error('message') is-invalid @enderror" 
                                      rows="10" 
                                      placeholder="Enter your message here..."
                                      required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Send Email Notification
                            </button>
                            <button type="reset" class="btn btn-secondary">
                                <i class="fas fa-redo me-2"></i>Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

</body>

@endsection

