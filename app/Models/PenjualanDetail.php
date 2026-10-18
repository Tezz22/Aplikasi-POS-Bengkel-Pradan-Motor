<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenjualanDetail extends Model
{
    protected $table = 'detail_penjualan';
    protected $primaryKey = 'id_detail';
    public $timestamps = false;

    protected $fillable = [
        'id_transaksi',
        'id_barang',
        'id_jasa',
        'jenis_item',
        'nama_item',
        'harga_satuan',
        'qty',
        'subtotal',
    ];

    public function getIdAttribute()
    {
        return $this->id_detail;
    }

    public function getHargaAttribute()
    {
        return $this->harga_satuan;
    }

    public function getJumlahAttribute()
    {
        return $this->qty;
    }

    public function getTipeAttribute()
    {
        return $this->jenis_item;
    }

    public function penjualan(): BelongsTo
    {
        return $this->belongsTo(Penjualan::class, 'id_transaksi', 'id_transaksi');
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }

    public function jasa(): BelongsTo
    {
        return $this->belongsTo(Jasa::class, 'id_jasa', 'id_jasa');
    }
}
