<?php

namespace Database\Seeders;

use App\Models\Inventory;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $inventories = [
            [
                'pos_inventory' => "1",
                'gudang' => 1,
                'stok' => 17,
            ],
            [
                'pos_inventory' => "2",
                'gudang' => 2,
                'stok' => 17,
            ],
            [
                'pos_inventory' => "2",
                'gudang' => 1,
                'stok' => 100,
            ],
            [
                'pos_inventory' => "2",
                'gudang' => 3,
                'stok' => 100,
            ],
            [
                'pos_inventory' => "3",
                'gudang' => 2,
                'stok' => 99,
            ],
            [
                'pos_inventory' => "3",
                'gudang' => 4,
                'stok' => 100,
            ],
        ];

        foreach ($inventories as $data) {
            Inventory::create($data);
        }
    }
}
