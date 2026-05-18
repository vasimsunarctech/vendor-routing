@extends('layouts.app')

@section('content')
<div class="container">
    <a href="{{ route('vendor.dashboard') }}" class="btn btn-link mb-3">&larr; Back to Dashboard</a>
    <h1>Respond to PO: {{ $purchaseOrder->po_number }}</h1>
    <div class="card">
        <div class="card-header">PO Details</div>
        <div class="card-body">
            <p><strong>Required Date:</strong> {{ \Carbon\Carbon::parse($purchaseOrder->required_date)->format('M d, Y') }}</p>
            <p><strong>Quantity Assigned to You:</strong> {{ $purchaseOrder->pivot->quantity_assigned }}</p>
            <hr>
            <form action="{{ route('vendor.purchase-orders.update', $purchaseOrder->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="fulfilled_quantity" class="form-label"><h4>How much can you fulfill?</h4></label>
                    <input type="number" class="form-control @error('fulfilled_quantity') is-invalid @enderror" id="fulfilled_quantity" name="fulfilled_quantity" min="0" max="{{ $purchaseOrder->pivot->quantity_assigned }}" required>
                    <div class="form-text">Enter a number between 0 and {{ $purchaseOrder->pivot->quantity_assigned }}.</div>
                    @error('fulfilled_quantity')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Submit Response</button>
            </form>
        </div>
    </div>
</div>
@endsection
