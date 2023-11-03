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
        $this->call([
            SediaanSeeder::class,
            PotypeSeeder::class,
            ItemSeeder::class,
            PosInventorySeeder::class,
            PurchaseOrderSeeder::class,
            PurchaseOrderDetailSeeder::class,
            ReceiveSeeder::class,
            MutationSeeder::class,
            MutationDetailSeeder::class,
            GudangSeeder::class,
            ReturSeeder::class,
            ReturDetailSeeder::class,
            InventorySeeder::class,
        ]);
    }
}
