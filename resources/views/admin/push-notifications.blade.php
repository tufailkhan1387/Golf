@extends('layouts.app')

@section('title', 'Push Notifications')

@section('content')

<body>
    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <div class="content">

            <!-- Page Header -->
            <div class="page-header mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <h3>Push Notifications (Firebase)</h3>
                </div>
            </div>

            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Push Notification Form -->
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.push-notifications.send') }}" method="POST" id="pushNotificationForm">
                        @csrf

                        <!-- Notification Type Selection -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Select Recipient Method</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="recipient_method" id="method_user" value="user" checked onchange="toggleRecipientMethod()">
                                <label class="form-check-label" for="method_user">
                                    Select User (with device token)
                                </label>
                            </div>
                            {{-- <div class="form-check">
                                <input class="form-check-input" type="radio" name="recipient_method" id="method_token" value="token" onchange="toggleRecipientMethod()">
                                <label class="form-check-label" for="method_token">
                                    Enter Device Token Manually
                                </label>
                            </div> --}}
                        </div>

                        <!-- User Selection (Method 1) -->
                        <div class="mb-3" id="user_selection">
                            <label for="user_id" class="form-label">Select User <span class="text-danger">*</span></label>
                            <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror">
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
                            <small class="form-text text-muted">Only users with device tokens are shown.</small>
                        </div>

                        <!-- Manual Device Token Entry (Method 2) -->
                        <div class="mb-3" id="token_selection" style="display: none;">
                            <label for="device_token" class="form-label">Device Token <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="device_token" 
                                   id="device_token" 
                                   class="form-control @error('device_token') is-invalid @enderror" 
                                   value="{{ old('device_token') }}" 
                                   placeholder="Enter Firebase device token">
                            @error('device_token')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Paste the Firebase device token here.</small>
                        </div>

                        <!-- Title -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Notification Title <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="title" 
                                   id="title" 
                                   class="form-control @error('title') is-invalid @enderror" 
                                   value="{{ old('title') }}" 
                                   placeholder="Enter notification title"
                                   required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Message -->
                        <div class="mb-3">
                            <label for="message" class="form-label">Notification Message <span class="text-danger">*</span></label>
                            <textarea name="message" 
                                      id="message" 
                                      class="form-control @error('message') is-invalid @enderror" 
                                      rows="6" 
                                      placeholder="Enter your notification message here..."
                                      required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">This message will appear in the push notification.</small>
                        </div>

                        <!-- Submit Button -->
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Send Push Notification
                            </button>
                            <button type="reset" class="btn btn-secondary" onclick="resetForm()">
                                <i class="fas fa-redo me-2"></i>Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Info Card -->
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-info-circle me-2"></i>Information</h5>
                    <ul class="mb-0">
                        <li>Push notifications are sent via Firebase Cloud Messaging (FCM)</li>
                        <li>Only users with device tokens can receive push notifications</li>
                        <li>You can select a user from the list or enter a device token manually</li>
                        <li>Device tokens are obtained when users register or log in to the mobile app</li>
                        <li>Make sure the device token is valid and the user's device is connected to the internet</li>
                    </ul>
                </div>
            </div>

        </div>
    </div>

</body>

<script>
function toggleRecipientMethod() {
    const userMethod = document.getElementById('method_user').checked;
    const userSelection = document.getElementById('user_selection');
    const tokenSelection = document.getElementById('token_selection');
    const userIdField = document.getElementById('user_id');
    const deviceTokenField = document.getElementById('device_token');

    if (userMethod) {
        userSelection.style.display = 'block';
        tokenSelection.style.display = 'none';
        userIdField.required = true;
        deviceTokenField.required = false;
        deviceTokenField.value = '';
    } else {
        userSelection.style.display = 'none';
        tokenSelection.style.display = 'block';
        userIdField.required = false;
        deviceTokenField.required = true;
        userIdField.value = '';
    }
}

function resetForm() {
    document.getElementById('pushNotificationForm').reset();
    toggleRecipientMethod();
}
</script>

@endsection
