@extends('layouts.app')

@section('title', 'Email Notifications History')

@section('content')

<body>
    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <div class="content">

            <!-- Page Header -->
            <div class="page-header mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <h3>Email Notifications History</h3>
                    <a href="{{ route('admin.email-notifications') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Send New Email
                    </a>
                </div>
            </div>

            <!-- Email Notifications Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Recipient</th>
                                    <th>Email</th>
                                    <th>Subject</th>
                                    <th>Status</th>
                                    <th>Sent By</th>
                                    <th>Sent At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($emailNotifications as $notification)
                                    <tr>
                                        <td>{{ $notification->id }}</td>
                                        <td>{{ $notification->recipient_name }}</td>
                                        <td>{{ $notification->recipient_email }}</td>
                                        <td>
                                            <span title="{{ $notification->subject }}">
                                                {{ Str::limit($notification->subject, 50) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($notification->status === 'sent')
                                                <span class="badge bg-success">Sent</span>
                                            @else
                                                <span class="badge bg-danger">Failed</span>
                                            @endif
                                        </td>
                                        <td>{{ $notification->sender ? $notification->sender->name : 'N/A' }}</td>
                                        <td>{{ $notification->created_at->format('M d, Y h:i A') }}</td>
                                        <td>
                                            <button type="button" 
                                                    class="btn btn-sm btn-info" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#emailModal{{ $notification->id }}"
                                                    title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Modal for Email Details -->
                                    <div class="modal fade" id="emailModal{{ $notification->id }}" tabindex="-1" aria-labelledby="emailModalLabel{{ $notification->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="emailModalLabel{{ $notification->id }}">Email Details</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <strong>Recipient:</strong> {{ $notification->recipient_name }} ({{ $notification->recipient_email }})
                                                    </div>
                                                    <div class="mb-3">
                                                        <strong>Subject:</strong> {{ $notification->subject }}
                                                    </div>
                                                    <div class="mb-3">
                                                        <strong>Status:</strong> 
                                                        @if($notification->status === 'sent')
                                                            <span class="badge bg-success">Sent</span>
                                                        @else
                                                            <span class="badge bg-danger">Failed</span>
                                                        @endif
                                                    </div>
                                                    @if($notification->error_message)
                                                        <div class="mb-3">
                                                            <strong>Error:</strong> 
                                                            <span class="text-danger">{{ $notification->error_message }}</span>
                                                        </div>
                                                    @endif
                                                    <div class="mb-3">
                                                        <strong>Message:</strong>
                                                        <div class="border p-3 mt-2" style="background-color: #f9f9f9; white-space: pre-wrap; max-height: 400px; overflow-y: auto;">
                                                            {{ $notification->message }}
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <strong>Sent By:</strong> {{ $notification->sender ? $notification->sender->name : 'N/A' }}
                                                    </div>
                                                    <div class="mb-3">
                                                        <strong>Sent At:</strong> {{ $notification->created_at->format('F d, Y h:i A') }}
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No email notifications found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $emailNotifications->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>

</body>

@endsection

