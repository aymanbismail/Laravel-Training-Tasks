<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Image Upload Tests
|--------------------------------------------------------------------------
*/

beforeEach(function () {
    Storage::fake('public');
});

test('user can create a product with an image', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $supplier = Supplier::create(['name' => 'IMG Supplier', 'email' => 'img@test.com']);

    $image = UploadedFile::fake()->create('product.jpg', 1024, 'image/jpeg');

    $this->actingAs($user)
        ->post(route('products.store'), [
            'name' => 'Product With Image',
            'price' => 29.99,
            'category_id' => $category->id,
            'image' => $image,
            'suppliers' => [
                $supplier->id => [
                    'selected' => '1',
                    'cost_price' => '15.00',
                    'lead_time_days' => '3',
                ],
            ],
        ])
        ->assertRedirect(route('products.index'));

    $product = Product::where('name', 'Product With Image')->first();
    expect($product)->not->toBeNull();
    expect($product->image_path)->not->toBeNull();
    Storage::disk('public')->assertExists($product->image_path);
});

test('user can create a product without an image', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $supplier = Supplier::create(['name' => 'No IMG Supplier', 'email' => 'noimg@test.com']);

    $this->actingAs($user)
        ->post(route('products.store'), [
            'name' => 'Product Without Image',
            'price' => 19.99,
            'category_id' => $category->id,
            'suppliers' => [
                $supplier->id => [
                    'selected' => '1',
                    'cost_price' => '10.00',
                    'lead_time_days' => '5',
                ],
            ],
        ])
        ->assertRedirect(route('products.index'));

    $product = Product::where('name', 'Product Without Image')->first();
    expect($product)->not->toBeNull();
    expect($product->image_path)->toBeNull();
});

test('image upload rejects non-image files', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $supplier = Supplier::create(['name' => 'Val Supplier', 'email' => 'val@test.com']);

    $file = UploadedFile::fake()->create('document.pdf', 500, 'application/pdf');

    $this->actingAs($user)
        ->post(route('products.store'), [
            'name' => 'Bad File Product',
            'price' => 9.99,
            'category_id' => $category->id,
            'image' => $file,
            'suppliers' => [
                $supplier->id => [
                    'selected' => '1',
                    'cost_price' => '5.00',
                    'lead_time_days' => '2',
                ],
            ],
        ])
        ->assertSessionHasErrors('image');
});

test('image upload rejects files exceeding 2MB', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $supplier = Supplier::create(['name' => 'Big Supplier', 'email' => 'big@test.com']);

    $image = UploadedFile::fake()->create('huge.jpg', 3000, 'image/jpeg'); // 3MB

    $this->actingAs($user)
        ->post(route('products.store'), [
            'name' => 'Big Image Product',
            'price' => 9.99,
            'category_id' => $category->id,
            'image' => $image,
            'suppliers' => [
                $supplier->id => [
                    'selected' => '1',
                    'cost_price' => '5.00',
                    'lead_time_days' => '2',
                ],
            ],
        ])
        ->assertSessionHasErrors('image');
});

test('user can update a product image and old image is deleted', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['user_id' => $user->id]);
    $supplier = Supplier::create(['name' => 'Upd Supplier', 'email' => 'upd@test.com']);

    // Upload initial image
    $oldImage = UploadedFile::fake()->create('old.jpg', 500, 'image/jpeg');
    $oldPath = $oldImage->store('products', 'public');
    $product->update(['image_path' => $oldPath]);
    Storage::disk('public')->assertExists($oldPath);

    // Upload new image
    $newImage = UploadedFile::fake()->create('new.jpg', 800, 'image/jpeg');

    $this->actingAs($user)
        ->put(route('products.update', $product), [
            'name' => $product->name,
            'price' => $product->price,
            'category_id' => $product->category_id,
            'image' => $newImage,
            'suppliers' => [
                $supplier->id => [
                    'selected' => '1',
                    'cost_price' => '10.00',
                    'lead_time_days' => '5',
                ],
            ],
        ])
        ->assertRedirect(route('products.index'));

    $product->refresh();
    expect($product->image_path)->not->toEqual($oldPath);
    Storage::disk('public')->assertExists($product->image_path);
    Storage::disk('public')->assertMissing($oldPath);
});

test('deleting a product removes its image from storage', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['user_id' => $user->id]);

    // Upload an image
    $image = UploadedFile::fake()->create('delete-me.jpg', 500, 'image/jpeg');
    $imagePath = $image->store('products', 'public');
    $product->update(['image_path' => $imagePath]);
    Storage::disk('public')->assertExists($imagePath);

    $this->actingAs($user)
        ->delete(route('products.destroy', $product))
        ->assertRedirect(route('products.index'));

    $this->assertDatabaseMissing('products', ['id' => $product->id]);
    Storage::disk('public')->assertMissing($imagePath);
});

/*
|--------------------------------------------------------------------------
| Show Page Tests
|--------------------------------------------------------------------------
*/

test('authenticated user can view the product show page', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create();

    $this->actingAs($user)
        ->get(route('products.show', $product))
        ->assertStatus(200)
        ->assertSee($product->name)
        ->assertSee('$' . number_format($product->price, 2));
});

test('product show page displays product image when present', function () {
    $user = User::factory()->create();

    $image = UploadedFile::fake()->create('show-test.jpg', 500, 'image/jpeg');
    $imagePath = $image->store('products', 'public');

    $product = Product::factory()->create(['image_path' => $imagePath]);

    $this->actingAs($user)
        ->get(route('products.show', $product))
        ->assertStatus(200)
        ->assertSee('storage/' . $imagePath);
});

test('product show page displays placeholder when no image', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['image_path' => null]);

    $this->actingAs($user)
        ->get(route('products.show', $product))
        ->assertStatus(200)
        ->assertSee('No image uploaded');
});

test('products index shows thumbnail when product has image', function () {
    $user = User::factory()->create();

    $image = UploadedFile::fake()->create('thumb-test.jpg', 500, 'image/jpeg');
    $imagePath = $image->store('products', 'public');

    $product = Product::factory()->create(['image_path' => $imagePath]);

    $this->actingAs($user)
        ->get(route('products.index'))
        ->assertStatus(200)
        ->assertSee('product-thumbnail');
});

test('guest cannot view product show page', function () {
    $product = Product::factory()->create();

    $this->get(route('products.show', $product))
        ->assertRedirect(route('login'));
});
