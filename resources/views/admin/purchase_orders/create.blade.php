@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create Purchase Order</h1>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('admin.purchase-orders.store') }}" method="POST">
        @csrf
        <div class="card">
            <div class="card-body">
                <div class="mb-3">
                    <label for="required_date" class="form-label">Required Date</label>
                    <input type="date" class="form-control" id="required_date" name="required_date" value="{{ old('required_date') }}" required>
                </div>
                <hr>
                <h4>Items</h4>
                <div id="items-container"></div>
                <button type="button" id="add-item-btn" class="btn btn-success mt-2">Add Item</button>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Create and Route PO</button>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let itemIndex = 0;
    const container = document.getElementById('items-container');
    const addItemBtn = document.getElementById('add-item-btn');

    function addNewItem() {
        const newItemRow = document.createElement('div');
        newItemRow.className = 'row mb-2 item-row align-items-end';
        newItemRow.innerHTML = `
            <div class="col-md-6">
                <label class="form-label">Item Name</label>
                <input type="text" name="items[${itemIndex}][name]" class="form-control" placeholder="Item Name" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Quantity</label>
                <input type="number" name="items[${itemIndex}][quantity]" class="form-control" placeholder="Quantity" min="1" required>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger remove-item-btn w-100">Remove</button>
            </div>
        `;
        container.appendChild(newItemRow);
        itemIndex++;
    }

    addItemBtn.addEventListener('click', addNewItem);
    container.addEventListener('click', function (e) {
        if (e.target && e.target.classList.contains('remove-item-btn')) {
            e.target.closest('.item-row').remove();
        }
    });
    addNewItem();
});
</script>
@endsection
