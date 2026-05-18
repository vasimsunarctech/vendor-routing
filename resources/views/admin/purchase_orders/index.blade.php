@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3 page-toolbar">
        <h1>Purchase Orders</h1>
        <a href="{{ route('admin.purchase-orders.create') }}" class="btn btn-primary">Create New PO</a>
    </div>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>PO Number</th>
                            <th>Required Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchaseOrders as $po)
                        <tr>
                            <td>{{ $po->po_number }}</td>
                            <td>{{ \Carbon\Carbon::parse($po->required_date)->format('M d, Y') }}</td>
                            <td><span class="badge bg-info text-dark">{{ Str::title(str_replace('_', ' ', $po->status)) }}</span></td>
                            <td class="table-actions"><a href="{{ route('admin.purchase-orders.show', $po) }}" class="btn btn-sm btn-secondary">View</a></td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center">No purchase orders found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($purchaseOrders->hasPages())
        <div class="card-footer">{{ $purchaseOrders->links() }}</div>
        @endif
    </div>
</div>
@endsection
