<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Product</title>
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
      max-width: 600px;
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

    .form-group {
      margin-bottom: 20px;
    }

    label {
      display: block;
      margin-bottom: 5px;
      color: #333;
      font-weight: 600;
    }

    input[type="text"],
    input[type="number"],
    select {
      width: 100%;
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 4px;
      font-size: 14px;
    }

    input[type="text"]:focus,
    input[type="number"]:focus,
    select:focus {
      outline: none;
      border-color: #007bff;
    }

    input.is-invalid,
    select.is-invalid {
      border-color: #dc3545;
    }

    .alert {
      padding: 15px;
      margin-bottom: 20px;
      border-radius: 4px;
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }

    .alert ul {
      margin: 0;
      padding-left: 20px;
    }

    .error {
      color: #dc3545;
      font-size: 12px;
      margin-top: 5px;
      display: block;
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

    .btn-secondary {
      background-color: #6c757d;
      color: white;
    }

    .btn-secondary:hover {
      background-color: #5a6268;
    }

    .form-actions {
      display: flex;
      gap: 10px;
      margin-top: 20px;
    }
  </style>
</head>

<body>
  <div class="container">
    <h1>Edit Product</h1>

    @if ($errors->any())
    <div class="alert">
      <strong>Please fix the following errors:</strong>
      <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
    @endif

    <form action="{{ route('products.update', $product) }}" method="POST">
      @csrf
      @method('PUT')

      <div class="form-group">
        <label for="name">Product Name</label>
        <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}"
          class="@error('name') is-invalid @enderror">
        @error('name')
        <div class="error">{{ $message }}</div>
        @enderror
      </div>

      <div class="form-group">
        <label for="price">Price</label>
        <input type="number" id="price" name="price" step="0.01" min="0" value="{{ old('price', $product->price) }}"
          class="@error('price') is-invalid @enderror">
        @error('price')
        <div class="error">{{ $message }}</div>
        @enderror
      </div>

      <div class="form-group">
        <label for="category_id">Category</label>
        <select id="category_id" name="category_id" class="@error('category_id') is-invalid @enderror">
          <option value="">-- Select Category --</option>
          @foreach($categories as $category)
          <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected'
            : '' }}>
            {{ $category->name }}
          </option>
          @endforeach
        </select>
        @error('category_id')
        <div class="error">{{ $message }}</div>
        @enderror
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-primary">Update Product</button>
        <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</body>

</html>