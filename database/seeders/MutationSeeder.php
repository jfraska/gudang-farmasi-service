<?php

namespace Database\Seeders;

use App\Models\Mutation;
use Illuminate\Database\Seeder;

class MutationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $mutation = [
            [
                'tanggal_permintaan' => '2023-07-10',
                'unit' => '192d9ecb-18c8-410b-9571-b4ad99f2271b',
            ],
            [
                'tanggal_permintaan' => '2023-07-10',
                'unit' => '1959c3df-b0e0-4791-a47f-f4819a62c6ad',
            ],
        ];

        foreach ($mutation as $data) {
            Mutation::create($data);
        }
    }
}
