<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReturDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'gudang',
        'jumlah',
        'alasan'
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($returDetail) {
            $returDetail->Gudang->reduceStock($returDetail->jumlah);
        });
    }

    public function retur()
    {
        return $this->belongsTo(Retur::class);
    }

    public function Gudang()
    {
        return $this->belongsTo(Gudang::class, 'gudang');
    }
}
