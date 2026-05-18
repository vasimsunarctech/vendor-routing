@extends('layouts.app')

@section('content')
<div class="container">
    <a href="{{ route('admin.vendors.index') }}" class="btn btn-link mb-3">&larr; Back to Vendors</a>
    <h1>Edit Vendor</h1>
    @include('admin.vendors.partials.form', [
        'action' => route('admin.vendors.update', $vendor),
        'method' => 'PUT',
        'vendor' => $vendor,
    ])
</div>
@endsection
