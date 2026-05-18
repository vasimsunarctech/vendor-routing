<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'metalmanauto') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --metal-dark: #111827;
            --metal-ink: #1f2937;
            --metal-gold: #d9a441;
        }

        body {
            min-height: 100vh;
            background: #f5f7fb;
            color: var(--metal-ink);
        }

        .brand-mark {
            color: var(--metal-dark);
            font-family: Georgia, "Times New Roman", serif;
            font-size: 1.45rem;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: lowercase;
        }

        .brand-mark .brand-accent {
            color: var(--metal-gold);
            font-style: italic;
        }

        .auth-page {
            min-height: calc(100vh - 73px);
            background:
                linear-gradient(135deg, rgba(17, 24, 39, .92), rgba(31, 41, 55, .82)),
                radial-gradient(circle at 20% 20%, rgba(217, 164, 65, .36), transparent 34%),
                linear-gradient(120deg, #d8dee8, #f8fafc);
            display: flex;
            align-items: center;
            padding: 48px 0;
        }

        .auth-card {
            border: 0;
            border-radius: 8px;
            box-shadow: 0 24px 70px rgba(15, 23, 42, .24);
            overflow: hidden;
        }

        .auth-panel {
            background: linear-gradient(160deg, #111827, #334155);
            color: #fff;
            padding: 42px;
        }

        .auth-panel .brand-mark {
            color: #fff;
            font-size: 2rem;
        }

        .auth-panel p {
            color: #d7dee8;
        }

        .auth-form {
            padding: 42px;
            background: #fff;
        }

        .btn-metal {
            background: var(--metal-dark);
            border-color: var(--metal-dark);
            color: #fff;
            font-weight: 600;
        }

        .btn-metal:hover,
        .btn-metal:focus {
            background: #000;
            border-color: #000;
            color: #fff;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-md navbar-light bg-white border-bottom">
        <div class="container">
            <a class="navbar-brand brand-mark" href="{{ url('/') }}">metal<span class="brand-accent">manauto</span></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto">
                    @auth
                        @if(auth()->user()->isAdmin())
                            <li class="nav-item"><a class="nav-link" href="{{ route('admin.purchase-orders.index') }}">Purchase Orders</a></li>
                            <li class="nav-item"><a class="nav-link" href="{{ route('admin.vendors.index') }}">Vendors</a></li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.notifications.index') }}">
                                    Notifications
                                    @if(auth()->user()->unreadNotifications()->count() > 0)
                                        <span class="badge bg-danger">{{ auth()->user()->unreadNotifications()->count() }}</span>
                                    @endif
                                </a>
                            </li>
                        @else
                            <li class="nav-item"><a class="nav-link" href="{{ route('vendor.dashboard') }}">Dashboard</a></li>
                        @endif
                    @endauth
                </ul>
                <ul class="navbar-nav ms-auto">
                    @guest
                        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Register</a></li>
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">{{ auth()->user()->name }}</a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Logout</button>
                                </form>
                            </div>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>
    <main class="@yield('main_class', 'py-4')">
        @yield('content')
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
