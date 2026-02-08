@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container">
    <h1 style="color: #1f2937; margin-bottom: 24px; font-size: 24px; font-weight: 700;">Dashboard</h1>

    <!-- Summary Cards -->
    <div
        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; margin-bottom: 32px;">
        <div class="card" style="display: flex; flex-direction: column; align-items: center; text-align: center;">
            <span style="font-size: 36px; font-weight: 700; color: #4f46e5;">{{ $totalProducts }}</span>
            <span style="font-size: 14px; color: #6b7280; margin-top: 4px;">Total Products</span>
            <a href="{{ route('products.index') }}" class="btn btn-primary btn-sm" style="margin-top: 12px;">View
                All</a>
        </div>
        <div class="card" style="display: flex; flex-direction: column; align-items: center; text-align: center;">
            <span style="font-size: 36px; font-weight: 700; color: #059669;">{{ $totalCategories }}</span>
            <span style="font-size: 14px; color: #6b7280; margin-top: 4px;">Total Categories</span>
            <a href="{{ route('categories.index') }}" class="btn btn-sm"
                style="margin-top: 12px; background-color: #059669; color: white;">View All</a>
        </div>
        <div class="card" style="display: flex; flex-direction: column; align-items: center; text-align: center;">
            <span style="font-size: 36px; font-weight: 700; color: #d97706;">{{ $totalSuppliers }}</span>
            <span style="font-size: 14px; color: #6b7280; margin-top: 4px;">Total Suppliers</span>
            <a href="{{ route('suppliers.index') }}" class="btn btn-sm"
                style="margin-top: 12px; background-color: #d97706; color: white;">View All</a>
        </div>
    </div>

    <!-- Latest Products -->
    <div class="card">
        <h2 style="color: #1f2937; margin-bottom: 16px; font-size: 18px; font-weight: 600;">Latest Products</h2>

        @if($latestProducts->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Owner</th>
                    <th>Suppliers</th>
                    <th>Created</th>
                </tr>
            </thead>
            <tbody>
                @foreach($latestProducts as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->category->name ?? 'N/A' }}</td>
                    <td>${{ number_format($product->price, 2) }}</td>
                    <td>{{ $product->user->name ?? 'N/A' }}</td>
                    <td>
                        @foreach($product->suppliers as $supplier)
                        <span class="supplier-badge">{{ $supplier->name }}</span>
                        @endforeach
                        @if($product->suppliers->isEmpty())
                        <span style="color: #9ca3af;">None</span>
                        @endif
                    </td>
                    <td>{{ $product->created_at->format('M d, Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div style="margin-top: 16px;">
            <a href="{{ route('products.index') }}" class="btn btn-primary btn-sm">View All Products</a>
        </div>
        @else
        <div class="empty-state">
            <p>No products yet. <a href="{{ route('products.create') }}" style="color: #4f46e5;">Create one!</a></p>
        </div>
        @endif
    </div>
</div>
@endsection