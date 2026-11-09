@extends('layouts.store')
@section('title', 'Menu')
@section('content')
<section class="hero" id="heroCarousel">
    <div class="hero-content">
        <span class="hero-label">YOUR CAMPUS FOOD STOP</span>
        <h1>Good food.<span>Good mood.</span></h1>
        <p>Your campus favorites, all in one place. Browse the menu and plan your next meal with Purple Blend.</p>
        <div class="hero-actions">
            <a class="hero-primary-button" href="#menu">Explore Our Menu <span aria-hidden="true">&rarr;</span></a>
            <a class="hero-secondary-button" href="{{ session('customer_id') ? route('orders.index') : route('login') }}">Track My Orders</a>
        </div>
        <div class="hero-note"><span class="hero-note-icon" aria-hidden="true">✦</span><span>Made for your campus days</span></div>
    </div>
    <div class="hero-visual">
        <div class="hero-image-frame">
            <img class="hero-image" src="{{ asset('Images/Milktea.jpg') }}" alt="A Purple Blend favorite" fetchpriority="high">
            <div class="hero-image-overlay"></div>
            <div class="hero-image-caption"><span class="hero-caption-label">THE PURPLE BLEND</span><strong>Your next favorite meal awaits.</strong></div>
        </div>
        <div class="hero-floating-note"><span class="hero-floating-icon" aria-hidden="true">♡</span><div><strong>Craving something?</strong><span>Find it on our menu.</span></div></div>
        <div class="hero-decoration hero-decoration-one"></div>
        <div class="hero-decoration hero-decoration-two"></div>
    </div>
</section>

<div class="page-tabs" aria-label="Store sections">
    <a class="page-tab active" href="#menu" aria-current="page">Menu</a>
    <a class="page-tab" href="{{ session('customer_id') ? route('orders.index') : route('login') }}">My Orders</a>
    <form class="search-box" method="get" action="{{ route('home') }}#menu">
        @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
        <svg class="search-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8" stroke="currentColor" stroke-width="1.8"/><path d="m16 16 4.2 4.2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
        <input type="search" name="search" value="{{ request('search') }}" placeholder="Search food, drinks, and more..." aria-label="Search menu items">
    </form>
</div>

<main id="menu" class="page-section">
    <div class="menu-intro">
        <div class="section-heading">
            <span class="menu-kicker">FRESH FROM OUR MENU</span>
            <h2>Find your favorites.</h2>
            <p>Something delicious for every campus day.</p>
        </div>
        <div class="menu-toolbar">
            <div class="categories" aria-label="Filter menu by category">
                @foreach(['All' => 'All Items', 'Ulam' => 'Ulam', 'Meryenda' => 'Meryenda', 'Beverage' => 'Beverage', 'Milk Tea' => 'Milk Tea', 'Fruity Soda' => 'Fruity Soda', 'Yakult Series' => 'Yakult Series', 'Coke Float' => 'Coke Float', 'Donut' => 'Donut', 'Snacks' => 'Snacks', "Candy's" => "Candy's", 'Biscuits' => 'Biscuits'] as $category => $label)
                    <a class="category-button {{ request('category', 'All') === $category ? 'active' : '' }}" href="{{ route('home', array_filter(['category' => $category === 'All' ? null : $category, 'search' => request('search')])) }}#menu" @if(request('category', 'All') === $category) aria-current="page" @endif>{{ $label }}</a>
                @endforeach
            </div>
        </div>
    </div>

    <div class="product-grid" id="productGrid">
        @forelse($products as $product)
            @php($inStock = $product->stock > 0)
            <article class="product-card {{ $inStock ? '' : 'is-out-of-stock' }}">
                <a href="{{ route('products.show', $product) }}" class="product-detail-link" aria-label="View details for {{ $product->name }}">
                    <img class="product-image" src="{{ $product->image_src ?? asset('Images/Milktea.jpg') }}" alt="{{ $product->name }}" loading="lazy">
                </a>
                <div class="product-info">
                    <span class="product-category">{{ $product->category === 'Drinks' ? 'Beverage' : ($product->category === 'Meryenda / Dessert' ? 'Meryenda' : $product->category) }}</span>
                    <h3><a href="{{ route('products.show', $product) }}" class="product-name-link">{{ $product->name }}</a></h3>
                    @if($product->size_label)<span class="variant-size">{{ $product->size_label }}</span>@endif
                    <p class="product-price">₱{{ number_format($product->price, 2) }}</p>
                    <p class="stock-note">{{ $inStock ? "Stock: {$product->stock} available" : 'Out of stock' }}</p>
                    <div class="product-actions">
                        @if($inStock)<a class="buy-now-button" href="{{ route('products.show', $product) }}">Buy Now</a>@else<button class="buy-now-button" type="button" disabled>Out of Stock</button>@endif
                        <form method="post" action="{{ route('cart.add') }}">@csrf<input type="hidden" name="product_id" value="{{ $product->id }}"><input type="hidden" name="quantity" value="1"><button class="add-button" type="submit" @disabled(!$inStock)>{{ $inStock ? '+ Add to Cart' : 'Out of Stock' }}</button></form>
                    </div>
                </div>
            </article>
        @empty
            <p class="empty-message">No items found. Try another search.</p>
        @endforelse
    </div>
</main>
@endsection
