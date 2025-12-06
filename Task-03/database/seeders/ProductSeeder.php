<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'name' => 'Laptop',
            'price' => 999.99,
        ]);

        Product::create([
            'name' => 'Smartphone',
            'price' => 599.50,
        ]);

        Product::create([
            'name' => 'Headphones',
            'price' => 149.99,
        ]);
    }
}
