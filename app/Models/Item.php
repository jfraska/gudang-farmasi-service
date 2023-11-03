<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    protected $keyType = 'string';

    protected $guarded = [];

    public function purchaseOrderDetail()
    {
        return $this->hasMany(PurchaseOrderDetail::class);
    }

    public function gudang()
    {
        return $this->hasMany(Gudang::class);
    }
    
    public function mutationDetail()
    {
        return $this->hasMany(MutationDetail::class);
    }

    public function kategori()
    {
        return $this->belongsTo(Potype::class, 'kategori', 'kode');
    }

    public function sediaan()
    {
        return $this->belongsTo(Sediaan::class, 'sediaan');
    }
}
