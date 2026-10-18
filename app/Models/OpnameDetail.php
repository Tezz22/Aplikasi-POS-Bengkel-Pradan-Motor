<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OpnameDetail extends Model
{
    protected $table = 'ms_penyesuaian_detail';
    protected $primaryKey = 'id_detail';
    public $timestamps = false;

    protected $fillable = [
        'id_opname',
        'id_barang',
        'stok_sistem',
        'stok_fisik',
        'alasan',
    ];

    public function getIdAttribute()
    {
        return $this->id_detail;
    }

    public function getSelisihAttribute()
    {
        return $this->stok_fisik - $this->stok_sistem;
    }

    public function opname(): BelongsTo
    {
        return $this->belongsTo(Opname::class, 'id_opname', 'id_opname');
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }
}
