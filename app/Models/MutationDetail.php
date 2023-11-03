<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MutationDetail extends Model
{
    use HasFactory, SoftDeletes;    

    protected $fillable = [
        'mutation_id',
        'gudang',
        'item',
        'sediaan',
        'jumlah',
    ];

    public function afterUpdate($data) {
        $this->Gudang->reduceStock($this->jumlah);
        $this->Gudang->storeInventory($data); 
    }

    public function mutation()
    {
        return $this->belongsTo(Mutation::class);
    }
    
    public function item()
    {
        return $this->belongsTo(Item::class, 'item');
    }

    public function Gudang()
    {
        return $this->belongsTo(Gudang::class, 'gudang');
    }
}