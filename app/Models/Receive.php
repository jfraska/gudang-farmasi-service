<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Receive extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class)->withTrashed();
    }

    public function gudang()
    {
        return $this->hasMany(Gudang::class);
    }

    public function retur()
    {
        return $this->hasOne(Retur::class);
    }
}
