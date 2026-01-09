<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index()
    {
        // Eager load suppliers and count them to avoid N+1
        $products = Product::with(['category', 'suppliers'])
            ->withCount('suppliers')
            ->get();

        return view('products.index', compact('products'));
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
        $product = Product::create($request->validated());

        // Attach selected suppliers with pivot data
        $this->syncSuppliers($product, $request->input('suppliers', []));

        return redirect()->route('products.index')->with('success', 'Product created successfully!');
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
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
