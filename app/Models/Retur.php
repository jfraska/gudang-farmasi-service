<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Retur extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nomor_retur',
        'tanggal_retur',
        'receive_id'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($retur) {
            $latest = static::orderBy('id', 'desc')->first();
            $nextNumber = $latest ? $latest->id+1 : 1;

            $retur->nomor_retur = 'R-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
        });
    }
    
    public function receive()
    {
        return $this->belongsTo(receive::class);
    }

    public function returDetail()
    {
        return $this->hasMany(ReturDetail::class);
    }

}
