@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="container">
  <div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
      <h1 style="color: #1f2937; font-size: 24px; font-weight: 700; margin: 0;">Products List</h1>
      <a href="{{ route('products.create') }}" class="btn btn-primary">Add New Product</a>
    </div>

    <!-- Search / Filter / Sort Toolbar -->
    <form method="GET" action="{{ route('products.index') }}" style="margin-bottom: 24px;">
      <div style="display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end;">
        <!-- Search -->
        <div style="flex: 1; min-width: 200px;">
          <label
            style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Search</label>
          <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name..."
            style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
        </div>

        <!-- Category Filter -->
        <div style="min-width: 180px;">
          <label
            style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Category</label>
          <select name="category_id"
            style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
            <option value="">All Categories</option>
            @foreach($categories as $category)
            <option value="{{ $category->id }}" {{ request('category_id')==$category->id ? 'selected' : '' }}>
              {{ $category->name }}
            </option>
            @endforeach
          </select>
        </div>

        <!-- Supplier Filter -->
        <div style="min-width: 180px;">
          <label
            style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Supplier</label>
          <select name="supplier_id"
            style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
            <option value="">All Suppliers</option>
            @foreach($suppliers as $supplier)
            <option value="{{ $supplier->id }}" {{ request('supplier_id')==$supplier->id ? 'selected' : '' }}>
              {{ $supplier->name }}
            </option>
            @endforeach
          </select>
        </div>

        <!-- Sort -->
        <div style="min-width: 180px;">
          <label style="display: block; font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 4px;">Sort
            By</label>
          <select name="sort"
            style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
            @foreach($sortOptions as $key => $label)
            <option value="{{ $key }}" {{ $sortKey===$key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
          </select>
        </div>

        <!-- Buttons -->
        <div style="display: flex; gap: 8px;">
          <button type="submit" class="btn btn-primary">Apply</button>
          <a href="{{ route('products.index') }}" class="btn btn-secondary">Clear</a>
        </div>
      </div>
    </form>

    @if($products->count() > 0)
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Category</th>
          <th>Price</th>
          <th>Owner</th>
          <th>Suppliers</th>
          <th>Created At</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($products as $product)
        <tr>
          <td>{{ $product->id }}</td>
          <td>{{ $product->name }}</td>
          <td>{{ $product->category->name ?? 'N/A' }}</td>
          <td>${{ number_format($product->price, 2) }}</td>
          <td>{{ $product->user->name ?? 'N/A' }}</td>
          <td>
            <span class="supplier-count">{{ $product->suppliers_count }}</span>
            @foreach($product->suppliers as $supplier)
            <span class="supplier-badge">
              {{ $supplier->name }} (cost: ${{ number_format($supplier->pivot->cost_price, 2) }}, lead: {{
              $supplier->pivot->lead_time_days }} days)
            </span>
            @endforeach
          </td>
          <td>{{ $product->created_at->format('Y-m-d H:i') }}</td>
          <td>
            <div class="actions">
              @can('update', $product)
              <a href="{{ route('products.edit', $product) }}" class="btn btn-warning btn-sm">Edit</a>
              @endcan
              @can('delete', $product)
              <form action="{{ route('products.destroy', $product) }}" method="POST" style="display: inline;"
                onsubmit="return confirm('Are you sure you want to delete this product?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
              </form>
              @endcan
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>

    <!-- Pagination -->
    <div style="margin-top: 20px;">
      {{ $products->links() }}
    </div>
    @else
    <div class="empty-state">
      <p>No products found matching your criteria.</p>
      @if(request()->hasAny(['search', 'category_id', 'supplier_id']))
      <a href="{{ route('products.index') }}" class="btn btn-secondary" style="margin-top: 12px;">Clear Filters</a>
      @endif
    </div>
    @endif
  </div>
</div>
@endsection