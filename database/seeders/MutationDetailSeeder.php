<?php

namespace Database\Seeders;

use App\Models\MutationDetail;
use Illuminate\Database\Seeder;

class MutationDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $mutationDetails = [
            [
                'mutation_id' => 1,
                'item' => '1',
                'jumlah' => 30
            ],
            [
                'mutation_id' => 2,
                'item' => '2',
                'jumlah' => 20
            ],
        ];

        foreach ($mutationDetails as $data) {
            MutationDetail::create($data);
        }
    }
}
