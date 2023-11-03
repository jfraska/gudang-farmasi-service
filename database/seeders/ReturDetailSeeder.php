<?php

namespace Database\Seeders;

use App\Models\ReturDetail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReturDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $returDetails = [
            [
                'retur_id' => 1,
                'gudang' => 1,
                'jumlah' => 10,
                'alasan' => 'Barang jelek, kamu juga jelek',
            ],
            [
                'retur_id' => 2,
                'gudang' => 2,
                'jumlah' => 10,
                'alasan' => 'Barang jelek, kamu juga jelek',
            ],
        ];

        foreach ($returDetails as $data) {
            ReturDetail::create($data);
        }
    }
}
