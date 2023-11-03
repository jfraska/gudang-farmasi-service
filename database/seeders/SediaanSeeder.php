<?php

namespace Database\Seeders;

use App\Models\Sediaan;
use Illuminate\Database\Seeder;

class SediaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sediaans = [
            [
                'id' => '1',
                'name' => 'tablet',
            ],
            [
                'id' => '2',
                'name' => 'cair',
            ],
            [
                'id' => '3',
                'name' => 'ml',
            ],
            [
                'id' => '4',
                'name' => 'mg',
            ],
            [
                'id' => '5',
                'name' => 'buah',
            ],
        ];

        foreach ($sediaans as $data) {
            Sediaan::create($data);
        }
    }
}
