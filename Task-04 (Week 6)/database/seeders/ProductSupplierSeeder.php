<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class ProductSupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();
        $suppliers = Supplier::all();

        if ($suppliers->isEmpty()) {
            return;
        }

        foreach ($products as $product) {
            // Attach 1-3 random suppliers to each product
            $randomSuppliers = $suppliers->random(rand(1, min(3, $suppliers->count())));

            foreach ($randomSuppliers as $supplier) {
                $product->suppliers()->attach($supplier->id, [
                    'cost_price' => rand(50, 500) + (rand(0, 99) / 100),
                    'lead_time_days' => rand(1, 30),
                ]);
            }
        }
    }
}
