<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PurchaseOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];
    
    protected static function boot()
    {
        parent::boot();

        static::created(function ($purchaseOrder) {
            $purchaseOrder->potype()->increment('state_number');
        });
    }
    
    public function purchaseOrderDetail()
    {
        return $this->hasMany(PurchaseOrderDetail::class);
    }

    public function receive()
    {
        return $this->hasMany(Receive::class);
    }

    public function potype()
    {
        return $this->belongsTo(Potype::class, 'potype', 'kode');
    }

}