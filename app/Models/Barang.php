<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barang extends Model
{
    protected $table = 'ms_barang';
    protected $primaryKey = 'id_barang';

    protected $fillable = [
        'id_kategori',
        'nama_barang',
        'satuan',
        'harga_jual',
        'stok',
    ];

    public function getIdAttribute()
    {
        return $this->id_barang;
    }

    public function getNamaAttribute()
    {
        return $this->nama_barang;
    }

    public function getHargaAttribute()
    {
        return $this->harga_jual;
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }

    public function detailPenjualan(): HasMany
    {
        return $this->hasMany(PenjualanDetail::class, 'id_barang', 'id_barang');
    }

    public function pembelian(): HasMany
    {
        return $this->hasMany(Pembelian::class, 'id_barang', 'id_barang');
    }

    public function opnameDetail(): HasMany
    {
        return $this->hasMany(OpnameDetail::class, 'id_barang', 'id_barang');
    }
}
