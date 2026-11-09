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
            <a class="hero-secondary-button" href="#my-orders" data-store-tab="orders">Track My Orders</a>
        </div>
        <div class="hero-note"><span class="hero-note-icon" aria-hidden="true">✦</span><span>Made for your campus days</span></div>
    </div>
    <div class="hero-visual">
        <div class="hero-image-frame">
            <img class="hero-image" src="{{ asset('images/Milktea.jpg') }}" alt="A Purple Blend favorite" fetchpriority="high">
            <div class="hero-image-overlay"></div>
            <div class="hero-image-caption"><span class="hero-caption-label">THE PURPLE BLEND</span><strong>Your next favorite meal awaits.</strong></div>
        </div>
        <div class="hero-floating-note"><span class="hero-floating-icon" aria-hidden="true">♡</span><div><strong>Craving something?</strong><span>Find it on our menu.</span></div></div>
        <div class="hero-decoration hero-decoration-one"></div>
        <div class="hero-decoration hero-decoration-two"></div>
    </div>
</section>

<div class="page-tabs" aria-label="Store sections">
    <a class="page-tab active" href="#menu" data-store-tab="menu" aria-current="page">Menu</a>
    <a class="page-tab" href="#my-orders" data-store-tab="orders">My Orders</a>

    <form class="search-box" method="get" action="{{ route('home') }}#menu">
        @if(request('category'))
            <input type="hidden" name="category" value="{{ request('category') }}">
        @endif
        <svg class="search-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <circle cx="10.8" cy="10.8" r="6.8" stroke="currentColor" stroke-width="1.8"/>
            <path d="m16 16 4.2 4.2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
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
                    <img class="product-image" src="{{ $product->image_src ?? asset('images/Milktea.jpg') }}" alt="{{ $product->name }}" loading="lazy">
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


<section id="my-orders" class="page-section orders-section" hidden>
    <div class="menu-intro">
        <div class="section-heading">
            <span class="menu-kicker">YOUR PURPLE BLEND ORDERS</span>
            <h2>My Orders</h2>
            <p>Track your orders and check your pickup details.</p>
        </div>
    </div>

    <div class="orders-list">
        @forelse($orders as $order)
            <article class="order-card">
                <div class="order-card-header">
                    <h3>{{ $order->order_code }}</h3>

                    <span class="order-status {{ $order->order_status }}">
                        {{ str($order->order_status)->headline() }}
                    </span>
                </div>

                <div class="order-details">
                    <p>
                        <span>Order Date</span>
                        <strong>{{ $order->created_at->format('M j, Y g:i A') }}</strong>
                    </p>

                    <p>
                        <span>Pickup Details</span>
                        <strong>
                            {{ str($order->dining_option)->headline() }}
                            @if($order->pickup_date)
                                · {{ $order->pickup_date->format('M j, Y') }}
                            @endif
                            @if($order->pickup_time)
                                at {{ substr($order->pickup_time, 0, 5) }}
                            @endif
                        </strong>
                    </p>

                    <p>
                        <span>Payment</span>
                        <strong>
                            {{ str($order->payment?->payment_status ?? 'pending')->headline() }}
                        </strong>
                    </p>

                    @if($order->notes)
                        <p>
                            <span>Order Note</span>
                            <strong>{{ $order->notes }}</strong>
                        </p>
                    @endif
                </div>

                <ul class="order-items">
                    @foreach($order->items as $item)
                        <li>
                            <span>{{ $item->product_name }} × {{ $item->quantity }}</span>
                            <strong>₱{{ number_format($item->line_total, 2) }}</strong>
                        </li>
                    @endforeach
                </ul>

                <div class="order-card-footer">
                    <span>
                        Total
                        <small>Order amount</small>
                    </span>

                    <strong>₱{{ number_format($order->total_amount, 2) }}</strong>
                </div>

                <div class="order-card-actions">
                    <a class="button secondary" href="{{ route('orders.show', $order) }}">
                        View Order
                    </a>

                    @if($order->order_status === 'pending' && $order->created_at->gte(now()->subMinutes(5)))
                        <form method="post"
                              action="{{ route('orders.cancel', $order) }}"
                              onsubmit="return confirm('Are you sure you want to cancel this order?')">
                            @csrf
                            <button class="cancel-order-button" type="submit">Cancel Order</button>
                        </form>
                    @endif
                </div>
            </article>
        @empty
            <div class="orders-empty">
                <h3>No orders yet</h3>
                <p>You haven't placed an order yet. Browse our menu and find your favorites!</p>
                <a class="orders-browse-button" href="#menu" data-store-tab="menu">
                    Browse Menu
                </a>
            </div>
        @endforelse
    </div>
</section>


@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabs = document.querySelectorAll('[data-store-tab]');
    const menu = document.getElementById('menu');
    const orders = document.getElementById('my-orders');
    const search = document.querySelector('.search-box');

    if (!menu || !orders || !tabs.length) {
        return;
    }

    function activateTab(tabName) {
        const showOrders = tabName === 'orders';

        menu.hidden = showOrders;
        orders.hidden = !showOrders;

        if (search) {
            search.hidden = showOrders;
        }

        tabs.forEach(function (tab) {
            const active = tab.dataset.storeTab === tabName;

            tab.classList.toggle('active', active);

            if (active) {
                tab.setAttribute('aria-current', 'page');
            } else {
                tab.removeAttribute('aria-current');
            }
        });

        history.replaceState(null, '', showOrders ? '#my-orders' : '#menu');
    }

    tabs.forEach(function (tab) {
        tab.addEventListener('click', function (event) {
            event.preventDefault();
            activateTab(tab.dataset.storeTab);
        });
    });

    activateTab(window.location.hash === '#my-orders' ? 'orders' : 'menu');
});
</script>
@endpush