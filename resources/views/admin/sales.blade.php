@extends('layouts.admin')
@section('title', 'Sales')
@section('content')
<p class="eyebrow">SALES OVERVIEW</p><h1>Sales · last 30 days</h1>
<div class="table-wrap"><table><thead><tr><th>Date</th><th>Orders</th><th>Sales</th></tr></thead><tbody>
@foreach($sales as $day)<tr><td>{{ $day['date']->format('D, M j, Y') }}</td><td>{{ $day['count'] }}</td><td>₱{{ number_format($day['total'], 2) }}</td></tr>@endforeach
</tbody></table></div>
@endsection
