@extends('layouts.store')
@section('title', 'Checkout')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/purple-blend-checkout.css') }}">
@endpush
@section('content')
<main class="checkout-page">
    <div class="checkout-container">
        <h1>Checkout</h1>
        <p class="checkout-subtitle">Review your order and choose when you would like to pick it up.</p>
        <div class="checkout-layout">
            <section class="checkout-details">
                <h2>Order Summary</h2>
                @foreach($items as $item)
                    <div class="checkout-item">
                        <div class="checkout-item-product">
                            <div class="checkout-item-image"><img src="{{ $item->image_src ?? asset('images/Milktea.jpg') }}" alt=""></div>
                            <div class="checkout-item-info"><strong>{{ $item->name }}</strong><p>₱{{ number_format($item->price, 2) }} each · Qty {{ $item->cart_quantity }}</p></div>
                        </div>
                        <strong>₱{{ number_format($item->line_total, 2) }}</strong>
                    </div>
                @endforeach
                <div class="checkout-total"><span>Total</span><strong>₱{{ number_format($items->sum('line_total'), 2) }}</strong></div>
            </section>
            <form method="post" action="{{ route('orders.store') }}" class="checkout-payment">@csrf
                <h2>Pickup details</h2>
                <div class="form-stack">
                    <label>Name<input type="text" name="customer_name" value="{{ old('customer_name', $customer->first_name.' '.$customer->last_name) }}" maxlength="201" required></label>
                    <label>Email<input type="email" name="customer_email" value="{{ old('customer_email', $customer->email) }}" required></label>
                    <label>Phone<input type="text" name="customer_phone" value="{{ old('customer_phone', $customer->phone) }}" maxlength="20" required></label>
                    <label>Dining option<select name="dining_option" required><option value="takeaway" @selected(old('dining_option', 'takeaway') === 'takeaway')>Takeaway</option><option value="dine_in" @selected(old('dining_option') === 'dine_in')>Dine in</option></select></label>
                    <div class="schedule-section">
                        <div class="schedule-heading-row"><h2>Pickup schedule</h2><span class="order-notes-optional">Optional</span></div>
                        <div class="schedule-fields">
                            <div class="schedule-field"><label for="pickup-date">Date</label><input id="pickup-date" type="date" name="pickup_date" min="{{ now()->toDateString() }}" value="{{ old('pickup_date') }}"></div>
                            <div class="schedule-field"><label for="pickup-time">Time</label><input id="pickup-time" type="time" name="pickup_time" value="{{ old('pickup_time') }}"></div>
                        </div>
                    </div>
                    <div class="order-notes-section"><div class="order-notes-heading"><h2>Order notes</h2><span class="order-notes-optional">Optional</span></div><textarea name="notes" rows="3" maxlength="1000" placeholder="Anything we should know?">{{ old('notes') }}</textarea></div>
                    <p class="checkout-note">Payment can be arranged when your order is confirmed.</p>
                    <button id="placeOrderButton" type="submit">Place Order</button>
                    <a class="back-to-cart" href="{{ route('cart') }}">← Return to Cart</a>
                </div>
            </form>
        </div>
    </div>
</main>
@endsection
