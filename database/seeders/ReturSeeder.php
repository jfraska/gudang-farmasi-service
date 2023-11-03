<?php

namespace Database\Seeders;

use App\Models\Retur;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReturSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $returs = [
            [
                'receive_id' => 1,
                'tanggal_retur' => '2023-07-19',
            ],
            [
                'receive_id' => 2,
                'tanggal_retur' => '2023-07-24',
            ],
        ];

        foreach ($returs as $data) {
            Retur::create($data);
        }
    }
}
