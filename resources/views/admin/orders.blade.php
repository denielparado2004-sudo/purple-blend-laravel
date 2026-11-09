@extends('layouts.admin')
@section('title', 'Orders')
@section('content')
<p class="eyebrow">ORDER MANAGEMENT</p><h1>Orders</h1>
<form method="get" class="search-form admin-search"><select name="status"><option value="">All statuses</option>@foreach(['pending','preparing','completed','cancelled'] as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ str($status)->headline() }}</option>@endforeach</select><button class="button secondary">Filter</button></form>
<div class="table-wrap"><table><thead><tr><th>Order</th><th>Customer</th><th>Placed</th><th>Items</th><th>Total</th><th>Status / payment</th></tr></thead><tbody>
@forelse($orders as $order)<tr><td><strong>{{ $order->order_code }}</strong><small class="block muted">{{ str($order->dining_option)->headline() }}</small></td><td>{{ $order->customer_name }}<small class="block muted">{{ $order->customer_phone }}</small></td><td>{{ $order->created_at->format('M j, Y g:i A') }}</td><td>{{ $order->items()->sum('quantity') }}</td><td>₱{{ number_format($order->total_amount, 2) }}</td><td><form method="post" action="{{ route('admin.orders.update', $order) }}" class="mini-form">@csrf @method('PATCH')
    <select name="order_status"><option value="{{ $order->order_status }}">{{ str($order->order_status)->headline() }}</option>@foreach(($order->order_status === 'pending' ? ['preparing','cancelled'] : ($order->order_status === 'preparing' ? ['completed','cancelled'] : [])) as $next)<option value="{{ $next }}">{{ str($next)->headline() }}</option>@endforeach</select><button class="button small">Update</button>
    @if($order->payment?->payment_status !== 'paid')<button class="text-button" name="payment_action" value="mark_paid">Mark paid</button>@else<p class="muted">Paid</p>@endif
    </form></td></tr>@empty<tr><td colspan="6" class="empty-cell">No orders found.</td></tr>@endforelse
</tbody></table></div><div class="pagination">{{ $orders->links() }}</div>
@endsection
