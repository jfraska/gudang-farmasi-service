<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventory extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $guarded = [];

    public function posInventory()
    {
        return $this->belongsTo(PosInventory::class, 'pos_inventory', 'id');
    }

    public function Gudang()
    {
        return $this->belongsTo(Gudang::class, 'gudang');
    }
}
