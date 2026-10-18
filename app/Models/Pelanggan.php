<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pelanggan extends Model
{
    protected $table = 'ms_pelanggan';
    protected $primaryKey = 'id_pelanggan';
    const UPDATED_AT = null;

    protected $fillable = [
        'nama',
        'plat_nomor',
        'no_telepon',
    ];

    public function getIdAttribute()
    {
        return $this->id_pelanggan;
    }

    public function penjualan(): HasMany
    {
        return $this->hasMany(Penjualan::class, 'id_pelanggan', 'id_pelanggan');
    }
}
