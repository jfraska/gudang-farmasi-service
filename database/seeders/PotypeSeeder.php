<?php

namespace Database\Seeders;

use App\Models\Potype;
use Illuminate\Database\Seeder;

class PotypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $potypes = [
            [
                'kode' => 'PS',
                'name' => 'Psikotropika',
            ],
            [
                'kode' => 'N',
                'name' => 'Narkotika',
            ],
            [
                'kode' => 'R',
                'name' => 'Reguler',
            ],
            [
                'kode' => 'J',
                'name' => 'JKN',
            ],
            [
                'kode' => 'PR',
                'name' => 'Prekusor',
            ],
            [
                'kode' => 'OOT',
                'name' => 'Obat Obat Tertentu',
            ],
            [
                'kode' => 'AK',
                'name' => 'Alat Kesehatan',
            ],
        ];

        foreach ($potypes as $data) {
            Potype::create($data);
        }
    }
}
