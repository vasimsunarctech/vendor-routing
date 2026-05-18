@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ $action }}" method="POST">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif
    <div class="card">
        <div class="card-body">
            <div class="mb-3">
                <label for="name" class="form-label">Vendor Name</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $vendor?->name) }}" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Login Email</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $vendor?->user?->email) }}" required>
            </div>
            <div class="mb-3">
                <label for="priority" class="form-label">Routing Priority</label>
                <input type="number" class="form-control" id="priority" name="priority" min="1" value="{{ old('priority', $vendor?->priority) }}">
                <div class="form-text">Lower numbers are assigned first. Leave blank to exclude from automatic routing.</div>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">{{ $vendor ? 'New Password' : 'Password' }}</label>
                <input type="password" class="form-control" id="password" name="password" {{ $vendor ? '' : 'required' }}>
                @if($vendor)
                    <div class="form-text">Leave blank to keep the current password.</div>
                @endif
            </div>
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" {{ $vendor ? '' : 'required' }}>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">{{ $vendor ? 'Update Vendor' : 'Create Vendor' }}</button>
        </div>
    </div>
</form>
