@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')
<div class="container" style="max-width: 800px;">
  <div class="card">
    <h1 style="color: #1f2937; margin-bottom: 20px; font-size: 24px; font-weight: 700;">Edit Product</h1>

    @if ($errors->any())
    <div class="validation-summary">
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
        <div class="field-error">{{ $message }}</div>
        @enderror
      </div>

      <div class="form-group">
        <label for="price">Price</label>
        <input type="number" id="price" name="price" step="0.01" min="0" value="{{ old('price', $product->price) }}"
          class="@error('price') is-invalid @enderror">
        @error('price')
        <div class="field-error">{{ $message }}</div>
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
        <div class="field-error">{{ $message }}</div>
        @enderror
      </div>

      <!-- Suppliers Section -->
      <h2 style="font-size: 18px; margin-top: 20px; padding-top: 20px; border-top: 1px solid #e5e7eb; color: #1f2937;">
        Suppliers</h2>
      <p style="color: #6b7280; margin-bottom: 15px; font-size: 14px;">Select at least one supplier and fill in the cost
        and lead time.</p>

      <div class="suppliers-section">
        @foreach($suppliers as $supplier)
        @php
        $existingSupplier = $product->suppliers->find($supplier->id);
        $oldSupplier = old("suppliers.{$supplier->id}", []);
        $isSelected = isset($oldSupplier['selected']) ? !empty($oldSupplier['selected']) : ($existingSupplier !== null);
        $costPrice = old("suppliers.{$supplier->id}.cost_price", $existingSupplier?->pivot->cost_price ?? '');
        $leadTime = old("suppliers.{$supplier->id}.lead_time_days", $existingSupplier?->pivot->lead_time_days ?? '');
        @endphp
        <div class="supplier-item">
          <div class="supplier-header">
            <input type="checkbox" id="supplier_{{ $supplier->id }}" name="suppliers[{{ $supplier->id }}][selected]"
              value="1" {{ $isSelected ? 'checked' : '' }} onchange="toggleSupplierFields(this, {{ $supplier->id }})">
            <label for="supplier_{{ $supplier->id }}" style="margin-bottom: 0; cursor: pointer;">
              {{ $supplier->name }} ({{ $supplier->email }})
            </label>
          </div>

          <div class="supplier-fields" id="supplier_fields_{{ $supplier->id }}"
            style="{{ $isSelected ? '' : 'display: none;' }}">
            <div>
              <label for="cost_price_{{ $supplier->id }}">Cost Price ($)</label>
              <input type="number" id="cost_price_{{ $supplier->id }}" name="suppliers[{{ $supplier->id }}][cost_price]"
                step="0.01" min="0" value="{{ $costPrice }}" class="@error(" suppliers.{$supplier->id}.cost_price")
              is-invalid @enderror">
              @error("suppliers.{$supplier->id}.cost_price")
              <div class="field-error">{{ $message }}</div>
              @enderror
            </div>
            <div>
              <label for="lead_time_{{ $supplier->id }}">Lead Time (days)</label>
              <input type="number" id="lead_time_{{ $supplier->id }}"
                name="suppliers[{{ $supplier->id }}][lead_time_days]" min="0" value="{{ $leadTime }}" class="@error("
                suppliers.{$supplier->id}.lead_time_days") is-invalid @enderror">
              @error("suppliers.{$supplier->id}.lead_time_days")
              <div class="field-error">{{ $message }}</div>
              @enderror
            </div>
          </div>
        </div>
        @endforeach
      </div>

      @error('suppliers')
      <div class="field-error">{{ $message }}</div>
      @enderror

      <div class="form-actions">
        <button type="submit" class="btn btn-primary">Update Product</button>
        <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
  function toggleSupplierFields(checkbox, supplierId) {
    var fields = document.getElementById('supplier_fields_' + supplierId);
    if (checkbox.checked) {
      fields.style.display = 'grid';
    } else {
      fields.style.display = 'none';
    }
  }
</script>
@endpush