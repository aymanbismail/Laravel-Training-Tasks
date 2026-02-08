<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@demo.com')->first();
        $jane = User::where('email', 'jane@demo.com')->first();

        $electronics = Category::where('name', 'Electronics')->first();
        $fashion = Category::where('name', 'Fashion')->first();
        $home = Category::where('name', 'Home & Kitchen')->first();
        $sports = Category::where('name', 'Sports & Outdoors')->first();
        $books = Category::where('name', 'Books')->first();
        $toys = Category::where('name', 'Toys & Games')->first();
        $health = Category::where('name', 'Health & Beauty')->first();

        $products = [
            // Admin's products
            ['name' => 'Laptop Pro 15"', 'price' => 1299.99, 'category_id' => $electronics->id, 'user_id' => $admin->id],
            ['name' => 'Smartphone X12', 'price' => 599.50, 'category_id' => $electronics->id, 'user_id' => $admin->id],
            ['name' => 'Wireless Headphones', 'price' => 149.99, 'category_id' => $electronics->id, 'user_id' => $admin->id],
            ['name' => 'Bluetooth Mouse', 'price' => 29.99, 'category_id' => $electronics->id, 'user_id' => $admin->id],
            ['name' => 'Mechanical Keyboard', 'price' => 89.99, 'category_id' => $electronics->id, 'user_id' => $admin->id],
            ['name' => 'Running Shoes', 'price' => 119.95, 'category_id' => $sports->id, 'user_id' => $admin->id],
            ['name' => 'Yoga Mat Premium', 'price' => 45.00, 'category_id' => $sports->id, 'user_id' => $admin->id],
            ['name' => 'Stainless Steel Water Bottle', 'price' => 24.99, 'category_id' => $home->id, 'user_id' => $admin->id],
            ['name' => 'LED Desk Lamp', 'price' => 39.99, 'category_id' => $home->id, 'user_id' => $admin->id],
            ['name' => 'Vitamin C Serum', 'price' => 28.50, 'category_id' => $health->id, 'user_id' => $admin->id],

            // Jane's products
            ['name' => 'Winter Jacket', 'price' => 189.00, 'category_id' => $fashion->id, 'user_id' => $jane->id],
            ['name' => 'Leather Handbag', 'price' => 79.99, 'category_id' => $fashion->id, 'user_id' => $jane->id],
            ['name' => 'Classic Novel Collection', 'price' => 34.99, 'category_id' => $books->id, 'user_id' => $jane->id],
            ['name' => 'Board Game Deluxe', 'price' => 49.99, 'category_id' => $toys->id, 'user_id' => $jane->id],
            ['name' => 'Electric Toothbrush', 'price' => 59.99, 'category_id' => $health->id, 'user_id' => $jane->id],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        // Create a few soft-deleted products for demo trash page
        $trashed = [
            ['name' => 'Discontinued Widget', 'price' => 9.99, 'category_id' => $electronics->id, 'user_id' => $admin->id],
            ['name' => 'Old Running Shoes v1', 'price' => 59.99, 'category_id' => $sports->id, 'user_id' => $admin->id],
            ['name' => 'Clearance T-Shirt', 'price' => 12.00, 'category_id' => $fashion->id, 'user_id' => $jane->id],
        ];

        foreach ($trashed as $item) {
            $product = Product::create($item);
            $product->delete(); // soft-delete
        }
    }
}
