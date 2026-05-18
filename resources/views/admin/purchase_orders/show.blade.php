@extends('layouts.app')

@section('content')
<div class="container">
    <a href="{{ route('admin.purchase-orders.index') }}" class="btn btn-link mb-3">&larr; Back to All POs</a>
    <h1>PO Details: {{ $purchaseOrder->po_number }}</h1>
    <div class="card mb-4">
        <div class="card-header">Summary</div>
        <div class="card-body">
            <p><strong>Status:</strong> {{ Str::title(str_replace('_', ' ', $purchaseOrder->status)) }}</p>
            <p><strong>Total Required:</strong> {{ $purchaseOrder->items->sum('quantity') }}</p>
            <p><strong>Total Fulfilled:</strong> {{ $purchaseOrder->items->sum('fulfilled_quantity') }}</p>
        </div>
    </div>
    <div class="card mb-4">
        <div class="card-header">Items</div>
        <ul class="list-group list-group-flush">
            @foreach($purchaseOrder->items as $item)
                <li class="list-group-item">{{ $item->item_name }} - Required: {{ $item->quantity }}, Fulfilled: {{ $item->fulfilled_quantity }}</li>
            @endforeach
        </ul>
    </div>
    <div class="card">
        <div class="card-header">Vendor Routing History</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Vendor</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Qty Assigned</th>
                            <th>Qty Fulfilled</th>
                            <th>Responded At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchaseOrder->vendors as $vendor)
                        <tr>
                            <td>{{ $vendor->name }}</td>
                            <td>{{ $vendor->priority }}</td>
                            <td><span class="badge bg-secondary">{{ Str::title(str_replace('_', ' ', $vendor->pivot->status)) }}</span></td>
                            <td>{{ $vendor->pivot->quantity_assigned }}</td>
                            <td>{{ $vendor->pivot->quantity_fulfilled ?? 'N/A' }}</td>
                            <td>{{ $vendor->pivot->status !== 'pending' ? $vendor->pivot->updated_at->format('M d, Y H:i') : 'Pending' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center">No vendors have been assigned yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
