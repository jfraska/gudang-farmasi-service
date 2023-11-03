<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [
            [
                'id' => '1',
                'kode' => 'ASW-788',
                'name' => 'Obat Kuat',
                'sediaan' => '1',
                'kategori' => 'PS',
                'minimum_stok' => 5,
            ],
            [
                'id' => '2',
                'kode' => 'BCT-1029',
                'name' => 'Obat Lemah',
                'sediaan' => '2',
                'kategori' => 'N',
                'minimum_stok' => '10',
            ],
        ];

        foreach ($items as $data) {
            Item::create($data);
        }
    }
}
