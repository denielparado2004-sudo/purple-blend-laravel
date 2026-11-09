@extends('layouts.admin')
@section('title', 'Dashboard')
@section('content')
<p class="eyebrow">PURPLE BLEND MANAGEMENT</p><h1>Dashboard</h1>
<div class="stats-grid"><article class="stat-card"><span>Today's orders</span><strong>{{ $ordersToday }}</strong></article><article class="stat-card"><span>Active orders</span><strong>{{ $pendingOrders }}</strong></article><article class="stat-card"><span>Customers</span><strong>{{ $customersCount }}</strong></article><article class="stat-card"><span>Menu items</span><strong>{{ $productsCount }}</strong></article><article class="stat-card"><span>Today's sales</span><strong>₱{{ number_format($salesToday, 2) }}</strong></article></div>
<section class="admin-section"><div class="section-heading"><h2>Recent orders</h2><a href="{{ route('admin.orders.index') }}">All orders →</a></div>
<div class="table-wrap"><table><thead><tr><th>Order</th><th>Customer</th><th>Date</th><th>Status</th><th>Total</th></tr></thead><tbody>@forelse($recentOrders as $order)<tr><td>{{ $order->order_code }}</td><td>{{ $order->customer_name }}</td><td>{{ $order->created_at->format('M j, g:i A') }}</td><td><span class="status status-{{ $order->order_status }}">{{ str($order->order_status)->headline() }}</span></td><td>₱{{ number_format($order->total_amount, 2) }}</td></tr>@empty<tr><td colspan="5" class="empty-cell">No orders yet.</td></tr>@endforelse</tbody></table></div></section>
@endsection
