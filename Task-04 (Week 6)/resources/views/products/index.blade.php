<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Products List</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      padding: 20px;
    }

    .container {
      max-width: 1200px;
      margin: 0 auto;
      background-color: white;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    h1 {
      color: #333;
      margin-bottom: 20px;
    }

    .alert {
      padding: 15px;
      margin-bottom: 20px;
      border-radius: 4px;
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }

    .btn {
      display: inline-block;
      padding: 10px 20px;
      text-decoration: none;
      border-radius: 4px;
      border: none;
      cursor: pointer;
      font-size: 14px;
      transition: background-color 0.3s;
    }

    .btn-primary {
      background-color: #007bff;
      color: white;
    }

    .btn-primary:hover {
      background-color: #0056b3;
    }

    .btn-warning {
      background-color: #ffc107;
      color: #212529;
    }

    .btn-warning:hover {
      background-color: #e0a800;
    }

    .btn-danger {
      background-color: #dc3545;
      color: white;
    }

    .btn-danger:hover {
      background-color: #c82333;
    }

    .btn-sm {
      padding: 5px 10px;
      font-size: 12px;
    }

    .mb-3 {
      margin-bottom: 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    table thead {
      background-color: #f8f9fa;
    }

    table th,
    table td {
      padding: 12px;
      text-align: left;
      border-bottom: 1px solid #dee2e6;
    }

    table th {
      font-weight: 600;
      color: #495057;
    }

    table tbody tr:hover {
      background-color: #f8f9fa;
    }

    .actions {
      display: flex;
      gap: 10px;
    }

    .empty-state {
      text-align: center;
      padding: 40px;
      color: #6c757d;
    }

    .supplier-badge {
      display: inline-block;
      background-color: #e9ecef;
      padding: 4px 8px;
      margin: 2px;
      border-radius: 4px;
      font-size: 12px;
    }

    .supplier-count {
      background-color: #007bff;
      color: white;
      padding: 2px 8px;
      border-radius: 12px;
      font-size: 12px;
    }
  </style>
</head>

<body>
  <div class="container">
    <h1>Products List</h1>

    @if(session('success'))
    <div class="alert">
      {{ session('success') }}
    </div>
    @endif

    <div class="mb-3">
      @auth
      <a href="{{ route('products.create') }}" class="btn btn-primary">Add New Product</a>
      @else
      <a href="{{ route('login') }}" class="btn btn-primary">Login to Add Products</a>
      @endauth
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
</body>

</html>