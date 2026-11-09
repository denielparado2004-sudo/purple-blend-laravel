@extends('layouts.store')
@section('title', $product->name)
@push('styles')
<link rel="stylesheet" href="{{ asset('css/purple-blend-product.css') }}">
@endpush
@section('content')
<main class="product-page">
    <a class="back-link" href="{{ route('home') }}#menu">← Back to menu</a>
    <div class="product-layout">
        <section class="product-details">
            <div class="product-image-container"><img class="product-image" src="{{ $product->image_src ?? asset('images/Milktea.jpg') }}" alt="{{ $product->name }}"></div>
            <div class="product-information">
                <p class="product-category">{{ $product->category === 'Drinks' ? 'Beverage' : ($product->category === 'Meryenda / Dessert' ? 'Meryenda' : $product->category) }}</p>
                <h1>{{ $product->name }}</h1>
                <p class="product-price">₱{{ number_format($product->price, 2) }}</p>
                <p class="product-description">A freshly prepared {{ strtolower($product->name) }} available from Purple Blend.</p>
                @if($variants->isNotEmpty())
                    <div class="size-variant-field"><label for="size-variant-select">Choose size</label><select id="size-variant-select" onchange="if (this.value) window.location.href = this.value">
                        @foreach($variants as $variant)<option value="{{ route('products.show', $variant) }}" @selected($variant->id === $product->id)>{{ $variant->size_label }}{{ $variant->stock < 1 ? ' (Out of stock)' : '' }}</option>@endforeach
                    </select></div>
                @endif
                <p class="stock-label">{{ $product->stock > 0 ? "In stock: {$product->stock}" : 'Currently out of stock' }}</p>
                @if($product->stock > 0)
                    <form method="post" action="{{ route('cart.add') }}">@csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <div class="quantity-section"><label for="quantity">Quantity</label><div class="quantity-control"><input type="number" name="quantity" id="quantity" value="1" min="1" max="{{ min(99, $product->stock) }}"></div></div>
                        <p class="subtotal">Price <strong>₱{{ number_format($product->price, 2) }}</strong></p>
                        <button class="add-to-cart-button" type="submit">Add to Cart</button>
                    </form>
                @else
                    <button class="add-to-cart-button" type="button" disabled>Out of Stock</button>
                @endif
            </div>
        </section>
        <aside class="related-products">
            <h2>You Might Also Like</h2>
            <p class="related-subtitle">More from {{ $product->category === 'Drinks' ? 'Beverage' : $product->category }}</p>
            @if($relatedProducts->isNotEmpty())
                <div class="related-list">
                    @foreach($relatedProducts as $related)
                        <article class="related-card">
                            <a class="related-image-link" href="{{ route('products.show', $related) }}"><img class="related-image" src="{{ $related->image_src ?? asset('images/Milktea.jpg') }}" alt="{{ $related->name }}" loading="lazy"></a>
                            <div class="related-info"><a class="related-name" href="{{ route('products.show', $related) }}">{{ $related->name }}</a><p class="related-price">₱{{ number_format($related->price, 2) }}</p><a class="view-product-button" href="{{ route('products.show', $related) }}">View Product <span>→</span></a></div>
                        </article>
                    @endforeach
                </div>
            @else
                <p class="no-related">No other items in this category yet.</p>
            @endif
        </aside>
    </div>
</main>
@endsection
