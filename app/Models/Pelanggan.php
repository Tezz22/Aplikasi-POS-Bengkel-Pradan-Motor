<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pelanggan extends Model
{
    protected $table = 'ms_pelanggan';

    protected $guarded = ['id'];

    public function penjualan(): HasMany
    {
        return $this->hasMany(Penjualan::class, 'pelanggan_id');
    }
}
