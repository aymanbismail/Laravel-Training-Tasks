@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="container">
  <div class="card">
    <h1 style="color: #1f2937; margin-bottom: 20px; font-size: 24px; font-weight: 700;">Products List</h1>

    <div style="margin-bottom: 20px;">
      <a href="{{ route('products.create') }}" class="btn btn-primary">Add New Product</a>
    </div>

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
    @else
    <div class="empty-state">
      <p>No products found. Create one to get started!</p>
    </div>
    @endif
  </div>
</div>
@endsection