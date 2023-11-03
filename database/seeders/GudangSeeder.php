<?php

namespace Database\Seeders;

use App\Models\Gudang;
use Illuminate\Database\Seeder;

class GudangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $gudangs = [
            [
                'nomor_batch' => 'A015042',
                'gudang' => "Rawat Jalan", 
                'receive_id' => 1,
                'stok' => 100,
                'harga_beli_satuan' => 20000,
                'harga_jual_satuan' => 35000,
                'item' => '2',
                'tanggal_ed' => '2023-07-10',
                'diskon' => 10,
                'margin' => 5,
                'total_pembelian' => 2000000,
            ],
            [
                'nomor_batch' => 'B015042',
                'gudang' => "Rawat Jalan", 
                'receive_id' => 1,
                'stok' => 150,
                'harga_beli_satuan' => 20000,
                'harga_jual_satuan' => 35000,
                'item' => '1',
                'tanggal_ed' => '2024-08-15',
                'diskon' => 10,
                'margin' => 5,
                'total_pembelian' => 2000000,
            ],
            [
                'nomor_batch' => 'A012042',
                'gudang' => "Rawat Inap",
                'receive_id' => 2,
                'stok' => 100,
                'harga_beli_satuan' => 20000,
                'harga_jual_satuan' => 35000,
                'item' => '1',
                'tanggal_ed' => '2023-09-10',
                'diskon' => 10,
                'margin' => 5,
                'total_pembelian' => 2000000,
            ],
            [
                'nomor_batch' => 'A012045',
                'gudang' => "Rawat Inap",
                'receive_id' => 2,
                'stok' => 100,
                'harga_beli_satuan' => 20000,
                'harga_jual_satuan' => 35000,
                'item' => '2',
                'tanggal_ed' => '2023-08-10',
                'diskon' => 10,
                'margin' => 5,
                'total_pembelian' => 2000000,
            ],
        ];

        foreach ($gudangs as $data) {
            Gudang::create($data);
        }
    }
}
