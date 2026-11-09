@extends('layouts.store')
@section('title', 'Your cart')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/purple-blend-cart.css') }}">
@endpush
@section('content')
<main class="cart-page">
    <div class="cart-heading">
        <h1>My Cart</h1>
        <p>Review your selected food before checkout.</p>
    </div>
    <div class="cart-layout">
        <section class="cart-items-section">
            <div class="cart-section-heading"><h2>Your Items</h2><a href="{{ route('home') }}#menu">Continue shopping</a></div>
            @forelse($items as $item)
                <article class="cart-item">
                    <a href="{{ route('products.show', $item) }}"><img class="cart-item-image" src="{{ $item->image_src ?? asset('Images/Milktea.jpg') }}" alt="{{ $item->name }}"></a>
                    <div class="cart-item-info">
                        <h3>{{ $item->name }}</h3>
                        <p class="cart-item-price">₱{{ number_format($item->price, 2) }}</p>
                        <p class="cart-item-stock">{{ $item->stock }} in stock</p>
                        <div class="cart-item-controls">
                            <form method="post" action="{{ route('cart.update', $item) }}" class="quantity-control">@csrf @method('PATCH')
                                <input type="number" name="quantity" value="{{ $item->cart_quantity }}" min="1" max="{{ min(50, $item->stock) }}" aria-label="Quantity for {{ $item->name }}">
                                <button type="submit" class="text-button">Update</button>
                            </form>
                            <form method="post" action="{{ route('cart.remove', $item) }}">@csrf @method('DELETE')<button class="remove-item-button" type="submit">Remove</button></form>
                        </div>
                    </div>
                    <strong class="cart-item-total">₱{{ number_format($item->line_total, 2) }}</strong>
                </article>
            @empty
                <div class="empty-cart">
                    <div class="empty-cart-icon">🛒</div>
                    <h3>Your cart is empty</h3>
                    <p>You haven't added any food yet.</p>
                    <a href="{{ route('home') }}#menu" class="browse-menu-button">Browse Menu</a>
                </div>
            @endforelse
        </section>
        <aside class="cart-summary">
            <h2>Order Summary</h2>
            <div class="summary-row"><span>Items</span><span>{{ $items->sum('cart_quantity') }}</span></div>
            <div class="summary-row"><span>Subtotal</span><span>₱{{ number_format($items->sum('line_total'), 2) }}</span></div>
            <div class="summary-divider"></div>
            <div class="summary-row summary-total"><span>Total</span><span>₱{{ number_format($items->sum('line_total'), 2) }}</span></div>
            @if($items->isNotEmpty())
                <a id="checkout-button" href="{{ route('checkout') }}">Proceed to Checkout</a>
            @else
                <button id="checkout-button" type="button" disabled>Proceed to Checkout</button>
            @endif
            <p class="cart-note">Your order total will update when you change quantities.</p>
        </aside>
    </div>
</main>
@endsection
