<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class PosInventory extends Model
{
    use Uuids, HasFactory, SoftDeletes;
    
    protected $guarded = [];

    public function mutation()
    {
        return $this->hasMany(Mutation::class, 'pos_inventory');
    }
}
