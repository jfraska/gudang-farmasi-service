<?php

namespace Database\Seeders;

use App\Models\PurchaseOrder;
use Illuminate\Database\Seeder;

class PurchaseOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $purchaseOrders = [
            [
                'tanggal_po' => '2023-07-10',
                'nomor_po' => 'PS202300001',
                'potype' => 'PS',
                'supplier' => '33b608a1-74fc-4951-b96b-0ccf2037947c',
                'keterangan' => 'Gas kirim yo',
                'gudang' => 'Rawat Inap',
            ],
            [
                'tanggal_po' => '2023-07-20',
                'nomor_po' => 'N202300001',
                'potype' => 'N',
                'supplier' => 'c6a87a58-3643-4db8-8ad5-9566ff706455',
                'keterangan' => 'Oke siap beres',
                'gudang' => 'Rawat Jalan',
            ],
        ];

        foreach ($purchaseOrders as $data) {
            PurchaseOrder::create($data);
        }
    }
}
