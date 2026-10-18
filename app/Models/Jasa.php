<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jasa extends Model
{
    protected $table = 'ms_jasa';
    protected $primaryKey = 'id_jasa';
    public $timestamps = false;

    protected $fillable = [
        'nama_jasa',
        'tarif',
    ];

    public function getIdAttribute()
    {
        return $this->id_jasa;
    }

    public function getHargaAttribute()
    {
        return $this->tarif;
    }

    public function getNamaAttribute()
    {
        return $this->nama_jasa;
    }

    public function detailPenjualan(): HasMany
    {
        return $this->hasMany(PenjualanDetail::class, 'id_jasa', 'id_jasa');
    }
}
