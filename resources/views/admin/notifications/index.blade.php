@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Notifications</h1>
        @if(auth()->user()->unreadNotifications()->count() > 0)
            <form action="{{ route('admin.notifications.mark-all-read') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-secondary">Mark All Read</button>
            </form>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Message</th>
                        <th>Received</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notifications as $notification)
                    <tr class="{{ $notification->read_at ? '' : 'table-warning' }}">
                        <td>
                            @if($notification->read_at)
                                <span class="badge bg-secondary">Read</span>
                            @else
                                <span class="badge bg-danger">New</span>
                            @endif
                        </td>
                        <td>
                            <div>{{ $notification->data['message'] ?? 'Notification received.' }}</div>
                            @if(isset($notification->data['po_number']))
                                <small class="text-muted">PO: {{ $notification->data['po_number'] }}</small>
                            @endif
                        </td>
                        <td>{{ $notification->created_at->diffForHumans() }}</td>
                        <td class="text-end">
                            <form action="{{ route('admin.notifications.read', $notification) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-primary">View PO</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center">No notifications found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($notifications->hasPages())
        <div class="card-footer">{{ $notifications->links() }}</div>
        @endif
    </div>
</div>
@endsection
