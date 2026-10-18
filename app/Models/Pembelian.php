<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pembelian extends Model
{
    protected $table = 'tr_pembelian';
    protected $primaryKey = 'id_tr_pembelian';
    const UPDATED_AT = null;

    protected $fillable = [
        'id_barang',
        'id_supplier',
        'id_user',
        'no_referensi',
        'tanggal',
        'keterangan',
        'jumlah',
        'harga_beli',
        'subtotal',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function getIdAttribute()
    {
        return $this->id_tr_pembelian;
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'id_supplier', 'id_supplier');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }
}
