<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Allowed sort fields and directions for trash (whitelist).
     */
    private const TRASH_SORTS = [
        'deleted_at_desc' => ['field' => 'deleted_at', 'direction' => 'desc', 'label' => 'Newest Deleted'],
        'deleted_at_asc'  => ['field' => 'deleted_at', 'direction' => 'asc',  'label' => 'Oldest Deleted'],
    ];

    /**
     * Allowed sort fields and directions (whitelist).
     */
    private const ALLOWED_SORTS = [
        'created_at_desc' => ['field' => 'created_at', 'direction' => 'desc', 'label' => 'Newest First'],
        'created_at_asc'  => ['field' => 'created_at', 'direction' => 'asc',  'label' => 'Oldest First'],
        'price_asc'       => ['field' => 'price',      'direction' => 'asc',  'label' => 'Price: Low → High'],
        'price_desc'      => ['field' => 'price',      'direction' => 'desc', 'label' => 'Price: High → Low'],
        'name_asc'        => ['field' => 'name',       'direction' => 'asc',  'label' => 'Name: A → Z'],
        'name_desc'       => ['field' => 'name',       'direction' => 'desc', 'label' => 'Name: Z → A'],
    ];

    /**
     * Display a listing of the products with search, filter, sort, and pagination.
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'suppliers', 'user'])
            ->withCount('suppliers');

        // Search by name
        if ($search = $request->input('search')) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        // Filter by category
        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        // Filter by supplier (products that have this supplier)
        if ($supplierId = $request->input('supplier_id')) {
            $query->whereHas('suppliers', function ($q) use ($supplierId) {
                $q->where('suppliers.id', $supplierId);
            });
        }

        // Sorting (whitelist-based)
        $sortKey = $request->input('sort', 'created_at_desc');
        if (!array_key_exists($sortKey, self::ALLOWED_SORTS)) {
            $sortKey = 'created_at_desc';
        }
        $sort = self::ALLOWED_SORTS[$sortKey];
        $query->orderBy($sort['field'], $sort['direction']);

        // Paginate and preserve query string
        $products = $query->paginate(10)->withQueryString();

        // Data for filter dropdowns
        $categories = Category::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();
        $sortOptions = collect(self::ALLOWED_SORTS)->mapWithKeys(fn ($v, $k) => [$k => $v['label']]);

        return view('products.index', compact(
            'products',
            'categories',
            'suppliers',
            'sortOptions',
            'sortKey'
        ));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = Category::all();
        $suppliers = Supplier::all();

        return view('products.create', compact('categories', 'suppliers'));
    }

    /**
     * Store a newly created product in the database.
     */
    public function store(StoreProductRequest $request)
    {
        $data = array_merge(
            $request->validated(),
            ['user_id' => auth()->id()]
        );

        // Handle image upload
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        // Remove 'image' key (it's the uploaded file, not a column)
        unset($data['image'], $data['suppliers']);

        $product = Product::create($data);

        // Attach selected suppliers with pivot data
        $this->syncSuppliers($product, $request->input('suppliers', []));

        return redirect()->route('products.index')->with('success', 'Product created successfully!');
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        $product->load(['category', 'suppliers', 'user']);

        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        $this->authorize('update', $product);

        $categories = Category::all();
        $suppliers = Supplier::all();

        // Load existing supplier relationships
        $product->load('suppliers');

        return view('products.edit', compact('product', 'categories', 'suppliers'));
    }

    /**
     * Update the specified product in the database.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $this->authorize('update', $product);

        $data = $request->validated();

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if it exists
            if ($product->image_path && Storage::disk('public')->exists($product->image_path)) {
                Storage::disk('public')->delete($product->image_path);
            }
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        // Remove keys that are not columns
        unset($data['image'], $data['suppliers']);

        $product->update($data);

        // Sync suppliers with pivot data
        $this->syncSuppliers($product, $request->input('suppliers', []));

        return redirect()->route('products.index')->with('success', 'Product updated successfully!');
    }

    /**
     * Soft-delete the specified product (move to trash).
     */
    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product moved to trash.');
    }

    /**
     * Display the trash page with soft-deleted products, search, filters, and sorting.
     */
    public function trash(Request $request)
    {
        $query = Product::onlyTrashed()
            ->with(['category', 'suppliers', 'user']);

        // Search by name
        if ($search = $request->input('search')) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        // Filter by category
        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        // Filter by supplier
        if ($supplierId = $request->input('supplier_id')) {
            $query->whereHas('suppliers', function ($q) use ($supplierId) {
                $q->where('suppliers.id', $supplierId);
            });
        }

        // Sorting (whitelist-based)
        $sortKey = $request->input('sort', 'deleted_at_desc');
        if (!array_key_exists($sortKey, self::TRASH_SORTS)) {
            $sortKey = 'deleted_at_desc';
        }
        $sort = self::TRASH_SORTS[$sortKey];
        $query->orderBy($sort['field'], $sort['direction']);

        $products = $query->paginate(10)->withQueryString();

        $categories = Category::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();
        $sortOptions = collect(self::TRASH_SORTS)->mapWithKeys(fn ($v, $k) => [$k => $v['label']]);

        return view('products.trash', compact(
            'products',
            'categories',
            'suppliers',
            'sortOptions',
            'sortKey'
        ));
    }

    /**
     * Restore a soft-deleted product.
     */
    public function restore(string $id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $this->authorize('restore', $product);

        $product->restore();

        return redirect()->route('products.trash')->with('success', 'Product restored successfully!');
    }

    /**
     * Permanently delete a soft-deleted product.
     */
    public function forceDelete(string $id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $this->authorize('forceDelete', $product);

        // Safety: prevent force delete if trashed less than 5 minutes ago
        if ($product->deleted_at->diffInMinutes(now()) < 5) {
            return redirect()->route('products.trash')
                ->with('error', 'Cannot permanently delete a product trashed less than 5 minutes ago. Please wait.');
        }

        // Delete image if it exists
        if ($product->image_path && Storage::disk('public')->exists($product->image_path)) {
            Storage::disk('public')->delete($product->image_path);
        }

        $product->forceDelete();

        return redirect()->route('products.trash')->with('success', 'Product permanently deleted.');
    }

    /**
     * Bulk restore selected soft-deleted products.
     */
    public function bulkRestore(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return redirect()->route('products.trash')->with('error', 'No products selected.');
        }

        $products = Product::onlyTrashed()->whereIn('id', $ids)->get();
        $restored = 0;

        foreach ($products as $product) {
            if (auth()->id() === $product->user_id) {
                $product->restore();
                $restored++;
            }
        }

        return redirect()->route('products.trash')
            ->with('success', "{$restored} product(s) restored successfully!");
    }

    /**
     * Bulk force-delete selected soft-deleted products.
     */
    public function bulkForceDelete(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return redirect()->route('products.trash')->with('error', 'No products selected.');
        }

        $products = Product::onlyTrashed()->whereIn('id', $ids)->get();
        $deleted = 0;

        foreach ($products as $product) {
            if (auth()->id() !== $product->user_id) {
                continue;
            }

            // Safety cooldown: skip products trashed less than 5 minutes ago
            if ($product->deleted_at->diffInMinutes(now()) < 5) {
                continue;
            }

            if ($product->image_path && Storage::disk('public')->exists($product->image_path)) {
                Storage::disk('public')->delete($product->image_path);
            }

            $product->forceDelete();
            $deleted++;
        }

        return redirect()->route('products.trash')
            ->with('success', "{$deleted} product(s) permanently deleted.");
    }

    /**
     * Sync suppliers with pivot data for a product.
     */
    private function syncSuppliers(Product $product, array $suppliers): void
    {
        $syncData = [];

        foreach ($suppliers as $supplierId => $data) {
            // Only sync if supplier is selected
            if (!empty($data['selected'])) {
                $syncData[$supplierId] = [
                    'cost_price' => $data['cost_price'],
                    'lead_time_days' => $data['lead_time_days'],
                ];
            }
        }

        $product->suppliers()->sync($syncData);
    }
}
