@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="container" style="max-width: 900px;">
  <div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
      <h1 style="color: #1f2937; font-size: 24px; font-weight: 700; margin: 0;">{{ $product->name }}</h1>
      <div style="display: flex; gap: 8px;">
        @can('update', $product)
        <a href="{{ route('products.edit', $product) }}" class="btn btn-warning btn-sm">Edit</a>
        @endcan
        <a href="{{ route('products.index') }}" class="btn btn-secondary btn-sm">Back to List</a>
      </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 32px;">
      <!-- Image Column -->
      <div>
        @if($product->image_path)
        <img src="{{ asset('storage/' . $product->image_path) }}" alt="{{ $product->name }}" class="product-image">
        @else
        <div class="image-placeholder-large">
          <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="1.5">
            <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
            <circle cx="8.5" cy="8.5" r="1.5" />
            <polyline points="21 15 16 10 5 21" />
          </svg>
          <p style="color: #9ca3af; margin-top: 12px; font-size: 14px;">No image uploaded</p>
        </div>
        @endif
      </div>

      <!-- Details Column -->
      <div>
        <table class="detail-table">
          <tr>
            <th>ID</th>
            <td>{{ $product->id }}</td>
          </tr>
          <tr>
            <th>Price</th>
            <td>${{ number_format($product->price, 2) }}</td>
          </tr>
          <tr>
            <th>Category</th>
            <td>{{ $product->category->name ?? 'N/A' }}</td>
          </tr>
          <tr>
            <th>Owner</th>
            <td>{{ $product->user->name ?? 'N/A' }}</td>
          </tr>
          <tr>
            <th>Created</th>
            <td>{{ $product->created_at->format('M d, Y \a\t H:i') }}</td>
          </tr>
          <tr>
            <th>Updated</th>
            <td>{{ $product->updated_at->format('M d, Y \a\t H:i') }}</td>
          </tr>
        </table>

        <!-- Suppliers -->
        <h2 style="font-size: 16px; font-weight: 600; color: #1f2937; margin-top: 24px; margin-bottom: 12px;">
          Suppliers ({{ $product->suppliers->count() }})
        </h2>
        @if($product->suppliers->count() > 0)
        <div style="display: flex; flex-direction: column; gap: 8px;">
          @foreach($product->suppliers as $supplier)
          <div class="supplier-badge" style="display: block; padding: 8px 12px;">
            <strong>{{ $supplier->name }}</strong>
            <span style="color: #6b7280; font-size: 13px;">
              &mdash; Cost: ${{ number_format($supplier->pivot->cost_price, 2) }},
              Lead: {{ $supplier->pivot->lead_time_days }} days
            </span>
          </div>
          @endforeach
        </div>
        @else
        <p style="color: #6b7280; font-size: 14px;">No suppliers assigned.</p>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection