<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') | Purple Blend</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/purple-blend-admin.css') }}">
</head>
<body class="admin-body">
@if(session('admin_id'))
    <header class="site-header admin-header">
        <a class="brand" href="{{ route('admin.dashboard') }}">
            <img class="admin-logo" src="{{ asset('images/purple-blend-logo.jpg') }}" alt="The Purple Blend">
            <span>Admin portal<small>{{ session('admin_username') }}</small></span>
        </a>
        <button class="admin-menu-toggle" type="button" aria-controls="admin-navigation" aria-expanded="false">
            <span aria-hidden="true">☰</span>
            <span class="sr-only">Open admin navigation</span>
        </button>
        <nav id="admin-navigation" aria-label="Admin navigation">
            <a href="{{ route('admin.dashboard') }}" @class(['active' => request()->routeIs('admin.dashboard')])>Dashboard</a>
            <a href="{{ route('admin.products.index') }}" @class(['active' => request()->routeIs('admin.products.*')])>Menu</a>
            <a href="{{ route('admin.inventory.index') }}" @class(['active' => request()->routeIs('admin.inventory.*')])>Stock</a>
            <a href="{{ route('admin.orders.index') }}" @class(['active' => request()->routeIs('admin.orders.*')])>Orders</a>
            <a href="{{ route('admin.customers.index') }}" @class(['active' => request()->routeIs('admin.customers.*')])>Customers</a>
            <a href="{{ route('admin.sales') }}" @class(['active' => request()->routeIs('admin.sales', 'admin.reports')])>Sales</a>
            @if(session('admin_role') === 'manager')
                <a href="{{ route('admin.accounts.index') }}" @class(['active' => request()->routeIs('admin.accounts.*')])>Accounts</a>
            @endif
            <form method="post" action="{{ route('admin.logout') }}" class="inline">@csrf<button class="nav-button">Sign out</button></form>
        </nav>
    </header>
    <button class="admin-navigation-backdrop" type="button" aria-label="Close admin navigation" hidden></button>
@endif
<main class="page admin-page">
    @if(session('status'))<div class="notice">{{ session('status') }}</div>@endif
    @if($errors->any())<div class="notice error"><strong>Please check the form:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    @yield('content')
</main>
@vite('resources/js/app.js')
</body>
</html>
