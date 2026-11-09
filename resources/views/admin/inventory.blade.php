@extends('layouts.admin')
@section('title', 'Stock')
@section('content')
<p class="eyebrow">INVENTORY MANAGEMENT</p>
<h1>Stock</h1>
<p class="inventory-intro">Check how many items are left and update the count when needed.</p>

<section class="stats-grid inventory-summary" aria-label="Inventory summary">
    <article class="stat-card">
        <span>Running low</span>
        <strong>{{ number_format($lowStockCount) }}</strong>
        <small>Menu items with 1–10 pieces left</small>
    </article>
    <article class="stat-card">
        <span>Sold out</span>
        <strong>{{ number_format($outOfStockCount) }}</strong>
        <small>Menu items with no pieces left</small>
    </article>
</section>

<section class="admin-section inventory-section">
    <div class="section-heading">
        <h2>Stock by menu item</h2>
        <form class="search-form admin-search" method="get" action="{{ route('admin.inventory.index') }}">
            <input type="search" name="search" value="{{ request('search') }}" maxlength="100" placeholder="Find a menu item..." aria-label="Find a menu item">
            <button class="button" type="submit">Find item</button>
        </form>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Menu item</th>
                    <th>Category</th>
                    <th>Pieces left</th>
                    <th>Stock level</th>
                    <th>Shown to customers</th>
                    <th>Update pieces left</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    @php
                        $stockStatus = (int) $product->stock === 0 ? 'out' : ((int) $product->stock <= 10 ? 'low' : 'good');
                        $category = in_array(strtolower(trim($product->category)), ['drinks', 'beverage'], true) ? 'Beverage' : $product->category;
                    @endphp
                    <tr>
                        <td>
                            <strong>{{ $product->name }}@if($product->size_label) · {{ $product->size_label }}@endif</strong>
                            <small class="block muted">{{ $product->product_code }}</small>
                        </td>
                        <td>{{ $category }}</td>
                        <td>{{ number_format($product->stock) }}</td>
                        <td><span class="stock-status stock-status-{{ $stockStatus }}">{{ $stockStatus === 'out' ? 'Out of stock' : ($stockStatus === 'low' ? 'Low stock' : 'In stock') }}</span></td>
                        <td>{{ $product->is_available ? 'Yes' : 'No' }}</td>
                        <td>
                            <form class="stock-update-form" method="post" action="{{ route('admin.inventory.update', $product) }}">
                                @csrf
                                @method('PATCH')
                                <input type="number" name="stock" min="0" max="100000" step="1" value="{{ $product->stock }}" required aria-label="Stock quantity for {{ $product->name }}">
                                <button class="button small" type="submit">Update</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty-cell">No menu items found. Add items on the Menu page first.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $products->links() }}</div>
</section>
@endsection
