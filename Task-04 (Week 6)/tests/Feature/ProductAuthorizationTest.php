<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Guest Access Tests
|--------------------------------------------------------------------------
*/

test('guest can view products index', function () {
    $this->get(route('products.index'))
        ->assertStatus(200);
});

test('guest cannot access product create page', function () {
    $this->get(route('products.create'))
        ->assertRedirect(route('login'));
});

test('guest cannot store a product', function () {
    $category = Category::factory()->create();

    $this->post(route('products.store'), [
        'name' => 'Test Product',
        'price' => 9.99,
        'category_id' => $category->id,
    ])->assertRedirect(route('login'));
});

test('guest cannot access product edit page', function () {
    $product = Product::factory()->create();

    $this->get(route('products.edit', $product))
        ->assertRedirect(route('login'));
});

test('guest cannot update a product', function () {
    $product = Product::factory()->create();

    $this->put(route('products.update', $product), [
        'name' => 'Updated Product',
        'price' => 19.99,
        'category_id' => $product->category_id,
    ])->assertRedirect(route('login'));
});

test('guest cannot delete a product', function () {
    $product = Product::factory()->create();

    $this->delete(route('products.destroy', $product))
        ->assertRedirect(route('login'));
});

/*
|--------------------------------------------------------------------------
| Authenticated User - Own Products
|--------------------------------------------------------------------------
*/

test('logged in user can access product create page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('products.create'))
        ->assertStatus(200);
});

test('logged in user can store a product and ownership is assigned', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $supplier = Supplier::create(['name' => 'Test Supplier', 'email' => 'supplier@test.com']);

    $this->actingAs($user)
        ->post(route('products.store'), [
            'name' => 'My Product',
            'price' => 25.50,
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

    $this->assertDatabaseHas('products', [
        'name' => 'My Product',
        'user_id' => $user->id,
    ]);
});

test('logged in user can update their own product', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['user_id' => $user->id]);
    $supplier = Supplier::create(['name' => 'Test Supplier', 'email' => 'supplier@test.com']);

    $this->actingAs($user)
        ->put(route('products.update', $product), [
            'name' => 'Updated Product Name',
            'price' => 99.99,
            'category_id' => $product->category_id,
            'suppliers' => [
                $supplier->id => [
                    'selected' => '1',
                    'cost_price' => '10.00',
                    'lead_time_days' => '5',
                ],
            ],
        ])
        ->assertRedirect(route('products.index'));

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => 'Updated Product Name',
    ]);
});

test('logged in user can delete their own product', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->delete(route('products.destroy', $product))
        ->assertRedirect(route('products.index'));

    $this->assertDatabaseMissing('products', [
        'id' => $product->id,
    ]);
});

/*
|--------------------------------------------------------------------------
| Authorization - Other Users' Products (403)
|--------------------------------------------------------------------------
*/

test('logged in user cannot update another users product', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $product = Product::factory()->create(['user_id' => $owner->id]);
    $supplier = Supplier::create(['name' => 'Test Supplier', 'email' => 'supplier@test.com']);

    $this->actingAs($otherUser)
        ->put(route('products.update', $product), [
            'name' => 'Hacked Product',
            'price' => 1.00,
            'category_id' => $product->category_id,
            'suppliers' => [
                $supplier->id => [
                    'selected' => '1',
                    'cost_price' => '10.00',
                    'lead_time_days' => '5',
                ],
            ],
        ])
        ->assertForbidden();
});

test('logged in user cannot delete another users product', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $product = Product::factory()->create(['user_id' => $owner->id]);

    $this->actingAs($otherUser)
        ->delete(route('products.destroy', $product))
        ->assertForbidden();

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
    ]);
});

test('logged in user cannot access edit page for another users product', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $product = Product::factory()->create(['user_id' => $owner->id]);

    $this->actingAs($otherUser)
        ->get(route('products.edit', $product))
        ->assertForbidden();
});
