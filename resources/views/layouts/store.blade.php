<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Purple Blend') | Purple Blend</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/purple-blend-original.css') }}">
    @stack('styles')
</head>
<body class="storefront-body">
<div class="customer-site-header">
    <header>
        <nav class="navbar" aria-label="Main navigation">
            <a href="{{ route('home') }}" class="brand" aria-label="The Purple Blend home">
                <img class="brand-logo" src="{{ asset('Images/purple-blend-logo.jpg') }}" alt="The Purple Blend">
            </a>
            <div class="header-actions">
        @if(session('customer_id'))
            <a href="{{ route('account') }}" class="account-button">👤 Account</a>
        @else
            <a href="{{ route('login') }}" class="login-button">Log In</a>
        @endif
                @if(!request()->routeIs('cart', 'checkout'))
                <a class="cart-button" href="{{ route('cart') }}">🛒 Cart ({{ array_sum(session('cart', [])) }})</a>
                @endif
            </div>
        </nav>
    </header>
    <nav class="category-nav" aria-label="Menu categories">
        <div class="nav-links">
            @foreach(['All' => 'All Items', 'Ulam' => 'Ulam', 'Meryenda' => 'Meryenda', 'Beverage' => 'Beverage', 'Milk Tea' => 'Milk Tea', 'Fruity Soda' => 'Fruity Soda', 'Yakult Series' => 'Yakult Series', 'Coke Float' => 'Coke Float', 'Donut' => 'Donut', 'Snacks' => 'Snacks', "Candy's" => "Candy's", 'Biscuits' => 'Biscuits'] as $category => $label)
                <a href="{{ route('home', $category === 'All' ? [] : ['category' => $category]) }}#menu" @if(request('category', 'All') === $category) class="active" aria-current="page" @endif>{{ $label }}</a>
            @endforeach
        </div>
    </nav>
</div>
@if(session('status') || $errors->any())
    <div class="store-notices">
    @if(session('status'))<div class="notice">{{ session('status') }}</div>@endif
    @if($errors->any())<div class="notice error"><strong>Please check the form:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    </div>
@endif
@yield('content')
<footer class="site-footer"><strong>The Purple Blend</strong><p>Your campus favorites, all in one place.</p><p><a href="{{ route('admin.login') }}">Admin</a></p></footer>
</body>
</html>
