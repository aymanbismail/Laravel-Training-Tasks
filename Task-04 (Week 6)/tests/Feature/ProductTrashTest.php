<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Soft Delete & Trash Tests
|--------------------------------------------------------------------------
*/

test('soft delete moves product to trash and hides from index', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->delete(route('products.destroy', $product))
        ->assertRedirect(route('products.index'));

    // Product should be soft-deleted (not in DB without trashed scope)
    $this->assertSoftDeleted('products', ['id' => $product->id]);

    // Not visible in normal product listing
    $this->actingAs($user)
        ->get(route('products.index'))
        ->assertDontSee($product->name);
});

test('soft-deleted product is visible in trash list', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['user_id' => $user->id]);
    $product->delete();

    $this->actingAs($user)
        ->get(route('products.trash'))
        ->assertStatus(200)
        ->assertSee($product->name);
});

test('restore returns product to the normal list', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['user_id' => $user->id]);
    $product->delete();

    $this->actingAs($user)
        ->post(route('products.restore', $product->id))
        ->assertRedirect(route('products.trash'));

    // Product should no longer be soft-deleted
    expect(Product::find($product->id))->not->toBeNull();

    // Visible in normal product listing again
    $this->actingAs($user)
        ->get(route('products.index'))
        ->assertSee($product->name);
});

test('force delete permanently removes product', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['user_id' => $user->id]);
    // Soft-delete and backdate deleted_at past the 5-minute cooldown
    $product->delete();
    Product::withTrashed()->where('id', $product->id)->update(['deleted_at' => now()->subMinutes(10)]);

    $this->actingAs($user)
        ->delete(route('products.forceDelete', $product->id))
        ->assertRedirect(route('products.trash'));

    // Product should be completely gone
    expect(Product::withTrashed()->find($product->id))->toBeNull();
});

test('force delete is blocked during cooldown period', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['user_id' => $user->id]);
    $product->delete(); // Just deleted — within 5 minute cooldown

    $this->actingAs($user)
        ->delete(route('products.forceDelete', $product->id))
        ->assertRedirect(route('products.trash'))
        ->assertSessionHas('error');

    // Product should still exist in trash
    expect(Product::onlyTrashed()->find($product->id))->not->toBeNull();
});

/*
|--------------------------------------------------------------------------
| Authorization Tests for Trash Actions
|--------------------------------------------------------------------------
*/

test('non-owner cannot restore another users trashed product', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $product = Product::factory()->create(['user_id' => $owner->id]);
    $product->delete();

    $this->actingAs($other)
        ->post(route('products.restore', $product->id))
        ->assertForbidden();
});

test('non-owner cannot force delete another users trashed product', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $product = Product::factory()->create(['user_id' => $owner->id]);
    $product->delete();
    $product->update(['deleted_at' => now()->subMinutes(10)]);

    $this->actingAs($other)
        ->delete(route('products.forceDelete', $product->id))
        ->assertForbidden();
});

test('guest cannot access trash page', function () {
    $this->get(route('products.trash'))
        ->assertRedirect(route('login'));
});

/*
|--------------------------------------------------------------------------
| Bulk Action Tests
|--------------------------------------------------------------------------
*/

test('bulk restore restores multiple products', function () {
    $user = User::factory()->create();
    $products = Product::factory()->count(3)->create(['user_id' => $user->id]);
    $products->each->delete();
    $ids = $products->pluck('id')->toArray();

    $this->actingAs($user)
        ->post(route('products.bulkRestore'), ['ids' => $ids])
        ->assertRedirect(route('products.trash'));

    foreach ($ids as $id) {
        expect(Product::find($id))->not->toBeNull();
    }
});

test('bulk restore respects authorization and skips others products', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $ownProduct = Product::factory()->create(['user_id' => $owner->id]);
    $otherProduct = Product::factory()->create(['user_id' => $other->id]);
    $ownProduct->delete();
    $otherProduct->delete();

    // Owner tries to bulk restore both — only own product should be restored
    $this->actingAs($owner)
        ->post(route('products.bulkRestore'), ['ids' => [$ownProduct->id, $otherProduct->id]])
        ->assertRedirect(route('products.trash'));

    expect(Product::find($ownProduct->id))->not->toBeNull();
    expect(Product::onlyTrashed()->find($otherProduct->id))->not->toBeNull();
});

test('bulk force delete permanently removes multiple products', function () {
    $user = User::factory()->create();
    $products = Product::factory()->count(3)->create(['user_id' => $user->id]);
    $products->each(function ($product) {
        $product->delete();
        Product::withTrashed()->where('id', $product->id)->update(['deleted_at' => now()->subMinutes(10)]);
    });
    $ids = $products->pluck('id')->toArray();

    $this->actingAs($user)
        ->delete(route('products.bulkForceDelete'), ['ids' => $ids])
        ->assertRedirect(route('products.trash'));

    foreach ($ids as $id) {
        expect(Product::withTrashed()->find($id))->toBeNull();
    }
});

test('bulk force delete respects authorization and skips others products', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $ownProduct = Product::factory()->create(['user_id' => $owner->id]);
    $otherProduct = Product::factory()->create(['user_id' => $other->id]);
    $ownProduct->delete();
    Product::withTrashed()->where('id', $ownProduct->id)->update(['deleted_at' => now()->subMinutes(10)]);
    $otherProduct->delete();
    Product::withTrashed()->where('id', $otherProduct->id)->update(['deleted_at' => now()->subMinutes(10)]);

    $this->actingAs($owner)
        ->delete(route('products.bulkForceDelete'), ['ids' => [$ownProduct->id, $otherProduct->id]])
        ->assertRedirect(route('products.trash'));

    expect(Product::withTrashed()->find($ownProduct->id))->toBeNull();
    expect(Product::onlyTrashed()->find($otherProduct->id))->not->toBeNull();
});

/*
|--------------------------------------------------------------------------
| Trash Search & Filter Tests
|--------------------------------------------------------------------------
*/

test('trash search filters by name', function () {
    $user = User::factory()->create();
    $product1 = Product::factory()->create(['user_id' => $user->id, 'name' => 'Alpha Trashed']);
    $product2 = Product::factory()->create(['user_id' => $user->id, 'name' => 'Beta Trashed']);
    $product1->delete();
    $product2->delete();

    $this->actingAs($user)
        ->get(route('products.trash', ['search' => 'Alpha']))
        ->assertSee('Alpha Trashed')
        ->assertDontSee('Beta Trashed');
});

test('trash filters by category', function () {
    $user = User::factory()->create();
    $cat1 = Category::factory()->create(['name' => 'Cat A']);
    $cat2 = Category::factory()->create(['name' => 'Cat B']);
    $product1 = Product::factory()->create(['user_id' => $user->id, 'category_id' => $cat1->id, 'name' => 'InCatA']);
    $product2 = Product::factory()->create(['user_id' => $user->id, 'category_id' => $cat2->id, 'name' => 'InCatB']);
    $product1->delete();
    $product2->delete();

    $this->actingAs($user)
        ->get(route('products.trash', ['category_id' => $cat1->id]))
        ->assertSee('InCatA')
        ->assertDontSee('InCatB');
});

test('trash empty state shows message', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('products.trash'))
        ->assertStatus(200)
        ->assertSee('Trash is empty');
});
