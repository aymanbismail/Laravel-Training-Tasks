@extends('layouts.app')

@section('title', 'Trash')

@section('content')
<div class="container">
  <div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
      <h1 style="color: #1f2937; font-size: 24px; font-weight: 700; margin: 0;">
        <span style="color: #ef4444;">&#128465;</span> Trash
      </h1>
      <a href="{{ route('products.index') }}" class="btn btn-secondary">Back to Products</a>
    </div>

    <!-- Search / Filter / Sort Toolbar -->
    <form method="GET" action="{{ route('products.trash') }}" style="margin-bottom: 24px;">
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
          <a href="{{ route('products.trash') }}" class="btn btn-secondary">Clear</a>
        </div>
      </div>
    </form>

    @if($products->count() > 0)
    <!-- Bulk Actions Bar -->
    <div id="bulk-actions" style="display: none; margin-bottom: 16px; padding: 12px 16px; background: #fef3c7; border: 1px solid #fbbf24; border-radius: 6px;">
      <span id="selected-count" style="font-weight: 600; color: #92400e; margin-right: 16px;">0 selected</span>
      <button type="button" onclick="bulkRestore()" class="btn btn-primary btn-sm" style="margin-right: 8px;">Bulk Restore</button>
      <button type="button" onclick="confirmBulkForceDelete()" class="btn btn-danger btn-sm">Bulk Force Delete</button>
    </div>

    <table>
      <thead>
        <tr>
          <th style="width: 40px;">
            <input type="checkbox" id="select-all" onchange="toggleSelectAll(this)" style="width: 18px; height: 18px;">
          </th>
          <th>Name</th>
          <th>Category</th>
          <th>Owner</th>
          <th>Suppliers</th>
          <th>Deleted At</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($products as $product)
        <tr>
          <td>
            @if(auth()->id() === $product->user_id)
            <input type="checkbox" class="product-checkbox" value="{{ $product->id }}" onchange="updateBulkBar()" style="width: 18px; height: 18px;">
            @endif
          </td>
          <td>{{ $product->name }}</td>
          <td>{{ $product->category->name ?? 'N/A' }}</td>
          <td>{{ $product->user->name ?? 'N/A' }}</td>
          <td>
            @foreach($product->suppliers as $supplier)
            <span class="supplier-badge">{{ $supplier->name }}</span>
            @endforeach
          </td>
          <td>{{ $product->deleted_at->format('Y-m-d H:i') }}</td>
          <td>
            <div class="actions">
              @can('restore', $product)
              <form action="{{ route('products.restore', $product->id) }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-primary btn-sm">Restore</button>
              </form>
              @endcan
              @can('forceDelete', $product)
              <button type="button" class="btn btn-danger btn-sm"
                onclick="confirmForceDelete('{{ $product->id }}', '{{ addslashes($product->name) }}')">
                Force Delete
              </button>
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
      <p style="font-size: 18px; margin-bottom: 8px;">&#128465; Trash is empty</p>
      <p>No deleted products found{{ request()->hasAny(['search', 'category_id', 'supplier_id']) ? ' matching your criteria' : '' }}.</p>
      @if(request()->hasAny(['search', 'category_id', 'supplier_id']))
      <a href="{{ route('products.trash') }}" class="btn btn-secondary" style="margin-top: 12px;">Clear Filters</a>
      @endif
    </div>
    @endif
  </div>
</div>

<!-- Force Delete Confirmation Modal -->
<div id="force-delete-modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; display: none; align-items: center; justify-content: center;">
  <div style="background: white; border-radius: 12px; padding: 32px; max-width: 440px; width: 90%; box-shadow: 0 25px 50px rgba(0,0,0,0.25);">
    <h2 style="color: #ef4444; font-size: 20px; font-weight: 700; margin-bottom: 12px;">&#9888; Permanent Deletion</h2>
    <p style="color: #374151; margin-bottom: 16px;">
      Are you sure you want to <strong>permanently delete</strong> <span id="modal-product-name" style="font-weight: 700;"></span>?
      This action <strong>cannot be undone</strong>.
    </p>
    <p style="color: #6b7280; font-size: 13px; margin-bottom: 20px;">
      Type <strong>DELETE</strong> to confirm:
    </p>
    <input type="text" id="confirm-input" placeholder="Type DELETE" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; margin-bottom: 16px;">
    <div style="display: flex; gap: 8px; justify-content: flex-end;">
      <button type="button" onclick="closeModal()" class="btn btn-secondary">Cancel</button>
      <form id="force-delete-form" method="POST" style="display: inline;">
        @csrf
        @method('DELETE')
        <button type="submit" id="confirm-delete-btn" class="btn btn-danger" disabled>Delete Permanently</button>
      </form>
    </div>
  </div>
</div>

<!-- Bulk Force Delete Confirmation Modal -->
<div id="bulk-force-delete-modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
  <div style="background: white; border-radius: 12px; padding: 32px; max-width: 440px; width: 90%; box-shadow: 0 25px 50px rgba(0,0,0,0.25);">
    <h2 style="color: #ef4444; font-size: 20px; font-weight: 700; margin-bottom: 12px;">&#9888; Bulk Permanent Deletion</h2>
    <p style="color: #374151; margin-bottom: 16px;">
      Are you sure you want to <strong>permanently delete</strong> <span id="bulk-count"></span> selected product(s)?
      This action <strong>cannot be undone</strong>.
    </p>
    <div style="display: flex; gap: 8px; justify-content: flex-end;">
      <button type="button" onclick="closeBulkModal()" class="btn btn-secondary">Cancel</button>
      <form id="bulk-force-delete-form" action="{{ route('products.bulkForceDelete') }}" method="POST" style="display: inline;">
        @csrf
        @method('DELETE')
        <div id="bulk-delete-inputs"></div>
        <button type="submit" class="btn btn-danger">Delete Permanently</button>
      </form>
    </div>
  </div>
</div>

<!-- Hidden form for bulk restore -->
<form id="bulk-restore-form" action="{{ route('products.bulkRestore') }}" method="POST" style="display: none;">
  @csrf
  <div id="bulk-restore-inputs"></div>
</form>
@endsection

@push('scripts')
<script>
  function toggleSelectAll(masterCheckbox) {
    var checkboxes = document.querySelectorAll('.product-checkbox');
    checkboxes.forEach(function(cb) { cb.checked = masterCheckbox.checked; });
    updateBulkBar();
  }

  function getSelectedIds() {
    var checkboxes = document.querySelectorAll('.product-checkbox:checked');
    return Array.from(checkboxes).map(function(cb) { return cb.value; });
  }

  function updateBulkBar() {
    var ids = getSelectedIds();
    var bar = document.getElementById('bulk-actions');
    var countEl = document.getElementById('selected-count');
    if (ids.length > 0) {
      bar.style.display = 'flex';
      bar.style.alignItems = 'center';
      countEl.textContent = ids.length + ' selected';
    } else {
      bar.style.display = 'none';
    }
  }

  function bulkRestore() {
    var ids = getSelectedIds();
    if (ids.length === 0) return;
    var container = document.getElementById('bulk-restore-inputs');
    container.innerHTML = '';
    ids.forEach(function(id) {
      var input = document.createElement('input');
      input.type = 'hidden'; input.name = 'ids[]'; input.value = id;
      container.appendChild(input);
    });
    document.getElementById('bulk-restore-form').submit();
  }

  function confirmForceDelete(productId, productName) {
    var modal = document.getElementById('force-delete-modal');
    document.getElementById('modal-product-name').textContent = productName;
    document.getElementById('force-delete-form').action = '/products-trash/' + productId + '/force-delete';
    document.getElementById('confirm-input').value = '';
    document.getElementById('confirm-delete-btn').disabled = true;
    modal.style.display = 'flex';
  }

  function closeModal() {
    document.getElementById('force-delete-modal').style.display = 'none';
  }

  document.getElementById('confirm-input').addEventListener('input', function() {
    document.getElementById('confirm-delete-btn').disabled = (this.value !== 'DELETE');
  });

  function confirmBulkForceDelete() {
    var ids = getSelectedIds();
    if (ids.length === 0) return;
    document.getElementById('bulk-count').textContent = ids.length;
    var container = document.getElementById('bulk-delete-inputs');
    container.innerHTML = '';
    ids.forEach(function(id) {
      var input = document.createElement('input');
      input.type = 'hidden'; input.name = 'ids[]'; input.value = id;
      container.appendChild(input);
    });
    document.getElementById('bulk-force-delete-modal').style.display = 'flex';
  }

  function closeBulkModal() {
    document.getElementById('bulk-force-delete-modal').style.display = 'none';
  }
</script>
@endpush
