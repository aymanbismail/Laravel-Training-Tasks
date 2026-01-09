<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            ['name' => 'Tech Parts Co.', 'email' => 'contact@techparts.com'],
            ['name' => 'Global Supply Inc.', 'email' => 'sales@globalsupply.com'],
            ['name' => 'Prime Distributors', 'email' => 'orders@primedist.com'],
            ['name' => 'Quality Goods Ltd.', 'email' => 'info@qualitygoods.com'],
            ['name' => 'Express Wholesale', 'email' => 'wholesale@express.com'],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}
