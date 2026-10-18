<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penjualan extends Model
{
    protected $table = 'tr_penjualan';
    protected $primaryKey = 'id_transaksi';
    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'id_pelanggan',
        'id_kendaraan',
        'no_transaksi',
        'tanggal',
        'total',
        'nominal_bayar',
        'kembalian',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
    ];

    public function getIdAttribute()
    {
        return $this->id_transaksi;
    }

    public function getNoNotaAttribute()
    {
        return $this->no_transaksi;
    }

    public function getTotalHargaAttribute()
    {
        return $this->total;
    }

    public function getBayarAttribute()
    {
        return $this->nominal_bayar;
    }

    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function details(): HasMany
    {
        return $this->hasMany(PenjualanDetail::class, 'id_transaksi', 'id_transaksi');
    }
}
