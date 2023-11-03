<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gudang extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "gudang",
        "nomor_batch",
        "item",
        "stok",
        "harga_beli_satuan",
        "harga_jual_satuan",
        "tanggal_ed",
        "diskon",
        "margin",
        "total_pembelian",
    ];

    public function reduceStock($amount)
    {
        if ($this->stok >= $amount) {
            $this->stok -= $amount;
            $this->save();
        } else {
            return;
        }
    }

    public function storeInventory($data) 
    {
        $posInventory = PosInventory::where('unit', $data['unit'])->first();
        
        if (!$posInventory) {
            return;
        }else{
            $result = Inventory::where('pos_inventory', $posInventory->id)
            ->where('gudang_id', $this->id)->first();

            if ($result) {
                $result->stok += $data['jumlah'];
                $result->save();
            } else {
                Inventory::create([
                    'gudang_id' => $this->id,
                    'pos_inventory' => $posInventory->id,
                    'stok' => $data['jumlah']
                ]);
            }
        }
    }

    public function receive()
    {
        return $this->belongsTo(Receive::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item');
    }

    public function mutationDetail()
    {
        return $this->hasMany(MutationDetail::class);
    }

    public function returDetail()
    {
        return $this->hasMany(ReturDetail::class);
    }

    public function inventory()
    {
        return $this->hasMany(Inventory::class);
    }
}
