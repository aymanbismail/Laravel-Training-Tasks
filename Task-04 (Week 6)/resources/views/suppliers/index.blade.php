@extends('layouts.app')

@section('title', 'Suppliers')

@section('content')
<div class="container">
  <h1 style="color: #1f2937; margin-bottom: 24px; font-size: 24px; font-weight: 700;">Suppliers</h1>

  <div class="card">
    @if($suppliers->count() > 0)
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Products Count</th>
          <th>Created At</th>
        </tr>
      </thead>
      <tbody>
        @foreach($suppliers as $supplier)
        <tr>
          <td>{{ $supplier->id }}</td>
          <td>{{ $supplier->name }}</td>
          <td>{{ $supplier->email }}</td>
          <td>
            <span class="supplier-count">{{ $supplier->products_count }}</span>
          </td>
          <td>{{ $supplier->created_at->format('M d, Y') }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
    @else
    <div class="empty-state">
      <p>No suppliers found.</p>
    </div>
    @endif
  </div>
</div>
@endsection