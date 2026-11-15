<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Opname extends Model
{
    protected $table = 'ms_penyesuaian';

    protected $primaryKey = 'id_opname';

    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'tanggal',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
    ];

    public function getIdAttribute(): int
    {
        return (int) $this->id_opname;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function details(): HasMany
    {
        return $this->hasMany(OpnameDetail::class, 'id_opname', 'id_opname');
    }

    public function getTotalSelisihCountAttribute(): int
    {
        return $this->details->filter(fn ($d) => (int) $d->stok_fisik !== (int) $d->stok_sistem)->count();
    }

    public function getJumlahItemAttribute(): int
    {
        return $this->details->count();
    }

    public function getJumlahSelisihAttribute(): int
    {
        return $this->total_selisih_count;
    }

    public function getPunyaSelisihAttribute(): bool
    {
        return $this->total_selisih_count > 0;
    }
}
