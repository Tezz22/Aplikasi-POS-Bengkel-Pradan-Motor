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

    public function getIdAttribute()
    {
        return $this->id_opname;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function details(): HasMany
    {
        return $this->hasMany(OpnameDetail::class, 'id_opname', 'id_opname');
    }
}
