@extends('layouts.store')
@section('title', 'Order '.$order->order_code)
@section('content')
<a class="back-link" href="{{ route('orders.index') }}">← My orders</a><p class="eyebrow">ORDER DETAILS</p><h1>{{ $order->order_code }}</h1>
<div class="detail-card order-detail"><div><p>Status <span class="status status-{{ $order->order_status }}">{{ str($order->order_status)->headline() }}</span></p><p>Placed {{ $order->created_at->format('M j, Y g:i A') }}</p><p>{{ str($order->dining_option)->headline() }}@if($order->pickup_date) · {{ $order->pickup_date->format('M j, Y') }}@endif @if($order->pickup_time) at {{ substr($order->pickup_time, 0, 5) }}@endif</p>@if($order->notes)<p>Note: {{ $order->notes }}</p>@endif
    @if($order->order_status === 'pending')<form method="post" action="{{ route('orders.cancel', $order) }}" onsubmit="return confirm('Cancel this order?')">@csrf<button class="button secondary">Cancel order</button></form>@endif
    </div><div><h2>Items</h2>@foreach($order->items as $item)<p>{{ $item->product_name }} × {{ $item->quantity }} <strong>₱{{ number_format($item->line_total, 2) }}</strong></p>@endforeach<hr><p class="total-row">Total <strong>₱{{ number_format($order->total_amount, 2) }}</strong></p><p class="muted">Payment: {{ str($order->payment?->payment_status ?? 'pending')->headline() }}</p></div></div>
@endsection
