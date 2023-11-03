<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseOrderDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'purchase_order_id',
        'item',
        'sediaan',
        'jumlah',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item');
    }

    public function sediaan()
    {
        return $this->belongsTo(Sediaan::class, 'sediaan');
    }
}
