@extends('layouts.app')

@section('title', 'Categories')

@section('content')
<div class="container">
  <h1 style="color: #1f2937; margin-bottom: 24px; font-size: 24px; font-weight: 700;">Categories</h1>

  <div class="card">
    @if($categories->count() > 0)
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Products Count</th>
          <th>Created At</th>
        </tr>
      </thead>
      <tbody>
        @foreach($categories as $category)
        <tr>
          <td>{{ $category->id }}</td>
          <td>{{ $category->name }}</td>
          <td>
            <span class="supplier-count">{{ $category->products_count }}</span>
          </td>
          <td>{{ $category->created_at->format('M d, Y') }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
    @else
    <div class="empty-state">
      <p>No categories found.</p>
    </div>
    @endif
  </div>
</div>
@endsection