<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Products Listing - Search, Filter, Sort, Pagination
|--------------------------------------------------------------------------
*/

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('products index displays paginated results', function () {
    $category = Category::factory()->create();
    Product::factory()->count(15)->create(['category_id' => $category->id, 'user_id' => $this->user->id]);

    $response = $this->actingAs($this->user)->get(route('products.index'));

    $response->assertStatus(200);
    $response->assertViewHas('products');
    // 10 per page, so first page has 10
    expect($response->viewData('products')->count())->toBe(10);
});

test('products index search filters by name', function () {
    $category = Category::factory()->create();
    Product::factory()->create(['name' => 'Alpha Widget', 'category_id' => $category->id, 'user_id' => $this->user->id]);
    Product::factory()->create(['name' => 'Beta Gadget', 'category_id' => $category->id, 'user_id' => $this->user->id]);

    $response = $this->actingAs($this->user)
        ->get(route('products.index', ['search' => 'Alpha']));

    $response->assertStatus(200);
    $products = $response->viewData('products');
    expect($products->count())->toBe(1);
    expect($products->first()->name)->toBe('Alpha Widget');
});

test('products index filters by category', function () {
    $cat1 = Category::factory()->create(['name' => 'Electronics']);
    $cat2 = Category::factory()->create(['name' => 'Books']);
    Product::factory()->create(['name' => 'Laptop', 'category_id' => $cat1->id, 'user_id' => $this->user->id]);
    Product::factory()->create(['name' => 'Novel', 'category_id' => $cat2->id, 'user_id' => $this->user->id]);

    $response = $this->actingAs($this->user)
        ->get(route('products.index', ['category_id' => $cat1->id]));

    $products = $response->viewData('products');
    expect($products->count())->toBe(1);
    expect($products->first()->name)->toBe('Laptop');
});

test('products index filters by supplier', function () {
    $category = Category::factory()->create();
    $supplierA = Supplier::create(['name' => 'Supplier A', 'email' => 'a@test.com']);
    $supplierB = Supplier::create(['name' => 'Supplier B', 'email' => 'b@test.com']);

    $product1 = Product::factory()->create(['name' => 'Prod 1', 'category_id' => $category->id, 'user_id' => $this->user->id]);
    $product2 = Product::factory()->create(['name' => 'Prod 2', 'category_id' => $category->id, 'user_id' => $this->user->id]);

    $product1->suppliers()->attach($supplierA->id, ['cost_price' => 10, 'lead_time_days' => 3]);
    $product2->suppliers()->attach($supplierB->id, ['cost_price' => 20, 'lead_time_days' => 5]);

    $response = $this->actingAs($this->user)
        ->get(route('products.index', ['supplier_id' => $supplierA->id]));

    $products = $response->viewData('products');
    expect($products->count())->toBe(1);
    expect($products->first()->name)->toBe('Prod 1');
});

test('products index sorts by price ascending', function () {
    $category = Category::factory()->create();
    Product::factory()->create(['name' => 'Expensive', 'price' => 999, 'category_id' => $category->id, 'user_id' => $this->user->id]);
    Product::factory()->create(['name' => 'Cheap', 'price' => 5, 'category_id' => $category->id, 'user_id' => $this->user->id]);

    $response = $this->actingAs($this->user)
        ->get(route('products.index', ['sort' => 'price_asc']));

    $products = $response->viewData('products');
    expect($products->first()->name)->toBe('Cheap');
    expect($products->last()->name)->toBe('Expensive');
});

test('products index sorts by price descending', function () {
    $category = Category::factory()->create();
    Product::factory()->create(['name' => 'Expensive', 'price' => 999, 'category_id' => $category->id, 'user_id' => $this->user->id]);
    Product::factory()->create(['name' => 'Cheap', 'price' => 5, 'category_id' => $category->id, 'user_id' => $this->user->id]);

    $response = $this->actingAs($this->user)
        ->get(route('products.index', ['sort' => 'price_desc']));

    $products = $response->viewData('products');
    expect($products->first()->name)->toBe('Expensive');
    expect($products->last()->name)->toBe('Cheap');
});

test('products index defaults to newest first', function () {
    $category = Category::factory()->create();
    $old = Product::factory()->create(['name' => 'Old', 'category_id' => $category->id, 'user_id' => $this->user->id, 'created_at' => now()->subDays(5)]);
    $new = Product::factory()->create(['name' => 'New', 'category_id' => $category->id, 'user_id' => $this->user->id, 'created_at' => now()]);

    $response = $this->actingAs($this->user)
        ->get(route('products.index'));

    $products = $response->viewData('products');
    expect($products->first()->name)->toBe('New');
});

test('products index rejects invalid sort and defaults gracefully', function () {
    $category = Category::factory()->create();
    Product::factory()->create(['category_id' => $category->id, 'user_id' => $this->user->id]);

    $response = $this->actingAs($this->user)
        ->get(route('products.index', ['sort' => 'malicious_injection']));

    $response->assertStatus(200);
    expect($response->viewData('sortKey'))->toBe('created_at_desc');
});

test('products index combines search with category filter', function () {
    $cat1 = Category::factory()->create(['name' => 'Electronics']);
    $cat2 = Category::factory()->create(['name' => 'Books']);
    Product::factory()->create(['name' => 'Pro Laptop', 'category_id' => $cat1->id, 'user_id' => $this->user->id]);
    Product::factory()->create(['name' => 'Pro Book', 'category_id' => $cat2->id, 'user_id' => $this->user->id]);
    Product::factory()->create(['name' => 'Basic Mouse', 'category_id' => $cat1->id, 'user_id' => $this->user->id]);

    $response = $this->actingAs($this->user)
        ->get(route('products.index', ['search' => 'Pro', 'category_id' => $cat1->id]));

    $products = $response->viewData('products');
    expect($products->count())->toBe(1);
    expect($products->first()->name)->toBe('Pro Laptop');
});

test('products index shows empty state for no results', function () {
    $response = $this->actingAs($this->user)
        ->get(route('products.index', ['search' => 'nonexistent_xyz']));

    $response->assertStatus(200);
    $response->assertSee('No products found matching your criteria.');
});

test('pagination preserves query string', function () {
    $category = Category::factory()->create();
    Product::factory()->count(15)->create(['category_id' => $category->id, 'user_id' => $this->user->id]);

    $response = $this->actingAs($this->user)
        ->get(route('products.index', ['category_id' => $category->id, 'page' => 2]));

    $response->assertStatus(200);
    $products = $response->viewData('products');
    // Page 2 should have the remaining 5
    expect($products->count())->toBe(5);
});
