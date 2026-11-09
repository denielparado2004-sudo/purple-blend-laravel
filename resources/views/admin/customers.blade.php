@extends('layouts.admin')
@section('title', 'Customers')
@section('content')
<p class="eyebrow">CUSTOMER DIRECTORY</p><h1>Customers</h1>
<div class="table-wrap"><table><thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Area</th><th>Orders</th><th>Joined</th></tr></thead><tbody>@forelse($customers as $customer)<tr><td>{{ $customer->first_name }} {{ $customer->last_name }}</td><td>{{ $customer->email }}</td><td>{{ $customer->phone ?: '—' }}</td><td>{{ $customer->area ?: '—' }}</td><td>{{ $customer->orders_count }}</td><td>{{ $customer->created_at->format('M j, Y') }}</td></tr>@empty<tr><td colspan="6" class="empty-cell">No customers registered yet.</td></tr>@endforelse</tbody></table></div><div class="pagination">{{ $customers->links() }}</div>
@endsection
