<?php

namespace Database\Seeders;

use App\Models\Receive;
use Illuminate\Database\Seeder;

class ReceiveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $receives = [
            [
                'nomor_faktur' => '202301455',
                'purchase_order_id' => 2,
                'tanggal_pembelian' => '2023-08-10',
                'tanggal_jatuh_tempo' => '2023-12-10',
                'ppn' => 10,
            ],
            [
                'nomor_faktur' => '202301277',
                'purchase_order_id' => 1,
                'tanggal_pembelian' => '2023-08-20',
                'tanggal_jatuh_tempo' => '2023-12-10',
                'ppn' => 5,
            ],
        ];

        foreach ($receives as $data) {
            Receive::create($data);
        }
    }
}
