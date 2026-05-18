@extends('layouts.app')

@section('content')
<div class="container">
    <a href="{{ route('admin.vendors.index') }}" class="btn btn-link mb-3">&larr; Back to Vendors</a>
    <h1>Add Vendor</h1>
    @include('admin.vendors.partials.form', [
        'action' => route('admin.vendors.store'),
        'method' => 'POST',
        'vendor' => null,
    ])
</div>
@endsection
