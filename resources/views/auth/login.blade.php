@extends('layouts.app')

@section('main_class', '')

@section('content')
<div class="auth-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-xl-8">
                <div class="auth-card">
                    <div class="row g-0">
                        <div class="col-md-5 auth-panel d-flex flex-column justify-content-between">
                            <div>
                                <div class="brand-mark mb-4">metal<span class="brand-accent">manauto</span></div>
                                <h1 class="h3 fw-bold mb-3">Purchase order portal</h1>
                                <p class="mb-0">Sign in to manage vendors, purchase orders, and approvals.</p>
                            </div>
                        </div>
                        <div class="col-md-7 auth-form">
                            <h2 class="h4 fw-bold mb-1">Welcome back</h2>
                            <p class="text-secondary mb-4">Use your account credentials to continue.</p>

                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus>
                                    @error('email')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
                                    @error('password')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                                <div class="mb-3 form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                    <label class="form-check-label" for="remember">Remember Me</label>
                                </div>
                                <button type="submit" class="btn btn-metal w-100">Login</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
