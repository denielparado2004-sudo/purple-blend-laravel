@extends('layouts.store')
@section('title', 'My orders')
@section('content')
<p class="eyebrow">YOUR PURPLE BLEND ORDERS</p><h1>My orders</h1>
<div class="table-wrap"><table><thead><tr><th>Order</th><th>Placed</th><th>Status</th><th>Total</th><th></th></tr></thead><tbody>
@forelse($orders as $order)<tr><td><strong>{{ $order->order_code }}</strong></td><td>{{ $order->created_at->format('M j, Y g:i A') }}</td><td><span class="status status-{{ $order->order_status }}">{{ str($order->order_status)->headline() }}</span></td><td>₱{{ number_format($order->total_amount, 2) }}</td><td><a href="{{ route('orders.show', $order) }}">View order</a></td></tr>@empty<tr><td colspan="5" class="empty-cell">You haven't placed an order yet.</td></tr>@endforelse
</tbody></table></div><div class="pagination">{{ $orders->links() }}</div>
@endsection
