@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Vendor Dashboard</h1>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if(!auth()->user()->vendor)
        <div class="alert alert-warning">No vendor profile is linked to this user.</div>
    @endif
    <div class="card">
        <div class="card-header"><h3 class="mb-0">Pending Purchase Orders</h3></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>PO Number</th>
                            <th>Required Date</th>
                            <th>Quantity Assigned</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchaseOrders as $po)
                        <tr>
                            <td>{{ $po->po_number }}</td>
                            <td>{{ \Carbon\Carbon::parse($po->required_date)->format('M d, Y') }}</td>
                            <td>{{ $po->pivot->quantity_assigned }}</td>
                            <td class="table-actions"><a href="{{ route('vendor.purchase-orders.show', $po) }}" class="btn btn-sm btn-primary">View &amp; Respond</a></td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center">You have no pending purchase orders.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if(method_exists($purchaseOrders, 'hasPages') && $purchaseOrders->hasPages())
        <div class="card-footer">{{ $purchaseOrders->links() }}</div>
        @endif
    </div>
</div>
@endsection
