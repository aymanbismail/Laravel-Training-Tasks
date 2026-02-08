<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Demo user (known credentials for testing/review)
        User::factory()->create([
            'name' => 'Demo Admin',
            'email' => 'admin@demo.com',
            'password' => bcrypt('password'),
        ]);

        // Second demo user for authorization testing
        User::factory()->create([
            'name' => 'Jane Reviewer',
            'email' => 'jane@demo.com',
            'password' => bcrypt('password'),
        ]);

        // Call seeders in order
        $this->call([
            CategorySeeder::class,
            SupplierSeeder::class,
            ProductSeeder::class,
            ProductSupplierSeeder::class,
        ]);
    }
}
