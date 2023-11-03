<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mutation extends Model
{
    use HasFactory, SoftDeletes;    

    protected $fillable = [
        'nomor_mutasi',
        'tanggal_permintaan',
        'tanggal_mutasi',
        'unit',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($mutation) {
            $latest = static::orderBy('id', 'desc')->first();
            $nextNumber = $latest ? $latest->id+1 : 1;

            $mutation->nomor_mutasi = 'M-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

            $posInventory = PosInventory::where('unit', $mutation->unit)->first();

            if (!$posInventory) {
                $mutation->gudang = "Rawat Inap";
            }else{
                $mutation->gudang = $posInventory->gudang;
            }
        });
    }
    
    public function mutationDetail()
    {
        return $this->hasMany(MutationDetail::class);
    }


}