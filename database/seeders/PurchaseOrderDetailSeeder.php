<?php

namespace Database\Seeders;

use App\Models\PurchaseOrderDetail;
use Illuminate\Database\Seeder;

class PurchaseOrderDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $purchaseOrderDetails = [
            [
                'purchase_order_id' => 1,
                'item' => '1',
                'sediaan' => '1',
                'jumlah' => 100,
            ],
            [
                'purchase_order_id' => 1,
                'item' => '1',
                'sediaan' => '1',
                'jumlah' => 300,
            ],
            [
                'purchase_order_id' => 2,
                'item' => '2',
                'sediaan' => '2',
                'jumlah' => 100,
            ],
            [
                'purchase_order_id' => 2,
                'item' => '1',
                'sediaan' => '1',
                'jumlah' => 200,
            ],
        ];

        foreach ($purchaseOrderDetails as $data) {
            PurchaseOrderDetail::create($data);
        }
    }
}
