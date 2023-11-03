<?php

namespace Database\Seeders;

use App\Models\PosInventory;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PosInventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $posInventories = [
            [
                'id' => '1',
                'unit' => '192d9ecb-18c8-410b-9571-b4ad99f2271b',
                'gudang' => 'Rawat Jalan',
            ],
            [
                'id' => '2',
                'unit' => '151930f0-3019-4718-b9e2-25cc2fa500cc',
                'gudang' => 'Rawat Inap',
            ],
            [
                'id' => '3',
                'unit' => '1959c3df-b0e0-4791-a47f-f4819a62c6ad',
                'gudang' => 'Rawat Inap',
            ],
        ];

        foreach ($posInventories as $data) {
            PosInventory::create($data);
        }
    }
}
