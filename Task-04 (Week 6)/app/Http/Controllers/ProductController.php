<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;

class ProductController extends Controller
{
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
        $product = Product::create(array_merge(
            $request->validated(),
            ['user_id' => auth()->id()]
        ));

        // Attach selected suppliers with pivot data
        $this->syncSuppliers($product, $request->input('suppliers', []));

        return redirect()->route('products.index')->with('success', 'Product created successfully!');
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

        $product->update($request->validated());

        // Sync suppliers with pivot data
        $this->syncSuppliers($product, $request->input('suppliers', []));

        return redirect()->route('products.index')->with('success', 'Product updated successfully!');
    }

    /**
     * Remove the specified product from the database.
     */
    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully!');
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
