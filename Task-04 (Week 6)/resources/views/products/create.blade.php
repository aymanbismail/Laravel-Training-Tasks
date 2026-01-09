<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Product</title>
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
      max-width: 800px;
      margin: 0 auto;
      background-color: white;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    h1, h2 {
      color: #333;
      margin-bottom: 20px;
    }

    h2 {
      font-size: 18px;
      margin-top: 20px;
      padding-top: 20px;
      border-top: 1px solid #ddd;
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

    /* Suppliers section styles */
    .suppliers-section {
      margin-top: 20px;
      padding: 15px;
      background-color: #f8f9fa;
      border-radius: 4px;
    }

    .supplier-item {
      padding: 15px;
      margin-bottom: 10px;
      background-color: white;
      border: 1px solid #ddd;
      border-radius: 4px;
    }

    .supplier-header {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 10px;
    }

    .supplier-header input[type="checkbox"] {
      width: 18px;
      height: 18px;
    }

    .supplier-fields {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 15px;
      margin-top: 10px;
    }

    .supplier-fields input {
      width: 100%;
    }
  </style>
</head>

<body>
  <div class="container">
    <h1>Create New Product</h1>

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

    <form action="{{ route('products.store') }}" method="POST">
      @csrf

      <div class="form-group">
        <label for="name">Product Name</label>
        <input type="text" id="name" name="name" value="{{ old('name') }}" class="@error('name') is-invalid @enderror">
        @error('name')
        <div class="error">{{ $message }}</div>
        @enderror
      </div>

      <div class="form-group">
        <label for="price">Price</label>
        <input type="number" id="price" name="price" step="0.01" min="0" value="{{ old('price') }}"
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
          <option value="{{ $category->id }}" {{ old('category_id')==$category->id ? 'selected' : '' }}>
            {{ $category->name }}
          </option>
          @endforeach
        </select>
        @error('category_id')
        <div class="error">{{ $message }}</div>
        @enderror
      </div>

      <!-- Suppliers Section -->
      <h2>Suppliers</h2>
      <p style="color: #666; margin-bottom: 15px; font-size: 14px;">Select at least one supplier and fill in the cost and lead time.</p>

      <div class="suppliers-section">
        @foreach($suppliers as $supplier)
        @php
          $oldSupplier = old("suppliers.{$supplier->id}", []);
          $isSelected = !empty($oldSupplier['selected']);
        @endphp
        <div class="supplier-item">
          <div class="supplier-header">
            <input type="checkbox"
                   id="supplier_{{ $supplier->id }}"
                   name="suppliers[{{ $supplier->id }}][selected]"
                   value="1"
                   {{ $isSelected ? 'checked' : '' }}
                   onchange="toggleSupplierFields(this, {{ $supplier->id }})">
            <label for="supplier_{{ $supplier->id }}" style="margin-bottom: 0; cursor: pointer;">
              {{ $supplier->name }} ({{ $supplier->email }})
            </label>
          </div>

          <div class="supplier-fields" id="supplier_fields_{{ $supplier->id }}" style="{{ $isSelected ? '' : 'display: none;' }}">
            <div>
              <label for="cost_price_{{ $supplier->id }}">Cost Price ($)</label>
              <input type="number"
                     id="cost_price_{{ $supplier->id }}"
                     name="suppliers[{{ $supplier->id }}][cost_price]"
                     step="0.01"
                     min="0"
                     value="{{ old("suppliers.{$supplier->id}.cost_price") }}"
                     class="@error("suppliers.{$supplier->id}.cost_price") is-invalid @enderror">
              @error("suppliers.{$supplier->id}.cost_price")
              <div class="error">{{ $message }}</div>
              @enderror
            </div>
            <div>
              <label for="lead_time_{{ $supplier->id }}">Lead Time (days)</label>
              <input type="number"
                     id="lead_time_{{ $supplier->id }}"
                     name="suppliers[{{ $supplier->id }}][lead_time_days]"
                     min="0"
                     value="{{ old("suppliers.{$supplier->id}.lead_time_days") }}"
                     class="@error("suppliers.{$supplier->id}.lead_time_days") is-invalid @enderror">
              @error("suppliers.{$supplier->id}.lead_time_days")
              <div class="error">{{ $message }}</div>
              @enderror
            </div>
          </div>
        </div>
        @endforeach
      </div>

      @error('suppliers')
      <div class="error">{{ $message }}</div>
      @enderror

      <div class="form-actions">
        <button type="submit" class="btn btn-primary">Create Product</button>
        <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>

  <script>
    // Show/hide supplier fields when checkbox is toggled
    function toggleSupplierFields(checkbox, supplierId) {
      var fields = document.getElementById('supplier_fields_' + supplierId);
      if (checkbox.checked) {
        fields.style.display = 'grid';
      } else {
        fields.style.display = 'none';
      }
    }
  </script>
</body>

</html>