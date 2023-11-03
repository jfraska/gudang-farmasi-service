<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Potype extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'kode';
    protected $keyType = 'string';

    protected $fillable = [
        'state_number',
    ];

    public function purchaseOrder()
    {
        return $this->hasMany(PurchaseOrder::class);
    }
    
}
