<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>@yield('title', 'Admin') | Purple Blend</title><link rel="stylesheet" href="{{ asset('css/app.css') }}"></head>
<body class="admin-body">
@if(session('admin_id'))
<header class="site-header admin-header"><a class="brand" href="{{ route('admin.dashboard') }}"><img class="admin-logo" src="{{ asset('Images/purple-blend-logo.jpg') }}" alt="The Purple Blend"><span>Admin portal<small>{{ session('admin_username') }}</small></span></a><nav><a href="{{ route('admin.dashboard') }}">Dashboard</a><a href="{{ route('admin.products.index') }}">Products</a><a href="{{ route('admin.orders.index') }}">Orders</a><a href="{{ route('admin.customers.index') }}">Customers</a><a href="{{ route('admin.sales') }}">Sales</a>@if(session('admin_role') === 'manager')<a href="{{ route('admin.accounts.index') }}">Accounts</a>@endif<form method="post" action="{{ route('admin.logout') }}" class="inline">@csrf<button class="nav-button">Sign out</button></form></nav></header>
@endif
<main class="page admin-page">
    @if(session('status'))<div class="notice">{{ session('status') }}</div>@endif
    @if($errors->any())<div class="notice error"><strong>Please check the form:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    @yield('content')
</main>
</body>
</html>
