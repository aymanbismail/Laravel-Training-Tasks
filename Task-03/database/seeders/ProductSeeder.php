<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get categories
        $electronics = Category::where('name', 'Electronics')->first();
        $fashion = Category::where('name', 'Fashion')->first();
        $home = Category::where('name', 'Home & Kitchen')->first();
        $sports = Category::where('name', 'Sports & Outdoors')->first();

        Product::create([
            'name' => 'Laptop',
            'price' => 999.99,
            'category_id' => $electronics->id,
        ]);

        Product::create([
            'name' => 'Smartphone',
            'price' => 599.50,
            'category_id' => $electronics->id,
        ]);

        Product::create([
            'name' => 'Headphones',
            'price' => 149.99,
            'category_id' => $electronics->id,
        ]);

        Product::create([
            'name' => 'Wireless Mouse',
            'price' => 29.99,
            'category_id' => $electronics->id,
        ]);

        Product::create([
            'name' => 'Mechanical Keyboard',
            'price' => 89.99,
            'category_id' => $electronics->id,
        ]);
    }
}
