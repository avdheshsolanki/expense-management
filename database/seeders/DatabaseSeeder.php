<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Seed categories first (required for expenses)
        $this->call([
            CategorySeeder::class,
            UserSeeder::class, // This will also create wallets and demo expenses
        ]);
    }
}
