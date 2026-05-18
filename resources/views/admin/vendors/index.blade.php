@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3 page-toolbar">
        <h1>Vendors</h1>
        <a href="{{ route('admin.vendors.create') }}" class="btn btn-primary">Add Vendor</a>
    </div>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Priority</th>
                            <th>Vendor</th>
                            <th>Email</th>
                            <th>Assigned POs</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vendors as $vendor)
                        <tr>
                            <td>{{ $vendor->priority ?? 'N/A' }}</td>
                            <td>{{ $vendor->name }}</td>
                            <td>{{ $vendor->user?->email ?? 'N/A' }}</td>
                            <td>{{ $vendor->purchase_orders_count }}</td>
                            <td class="text-end table-actions">
                                <a href="{{ route('admin.vendors.edit', $vendor) }}" class="btn btn-sm btn-secondary">Edit</a>
                                <form action="{{ route('admin.vendors.destroy', $vendor) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this vendor and linked user account?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center">No vendors found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($vendors->hasPages())
        <div class="card-footer">{{ $vendors->links() }}</div>
        @endif
    </div>
</div>
@endsection
