<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sediaan extends Model
{
    use Uuids, HasFactory, SoftDeletes;

    protected $guarded = [];

    public function purchaseOrderDetail()
    {
        return $this->hasMany(PurchaseOrderDetail::class, 'sediaan');
    }

    public function item()
    {
        return $this->hasMany(Item::class, 'sediaan');
    }

}
