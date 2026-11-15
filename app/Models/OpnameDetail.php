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

    public function getIdAttribute(): int
    {
        return (int) $this->id_detail;
    }

    public function getSelisihAttribute(): int
    {
        return (int) ($this->stok_fisik - $this->stok_sistem);
    }

    public function getStatusSelisihAttribute(): string
    {
        $selisih = $this->selisih;
        if ($selisih > 0) {
            return 'Lebih +' . $selisih;
        } elseif ($selisih < 0) {
            return 'Kurang ' . $selisih;
        }
        return 'Sesuai';
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
