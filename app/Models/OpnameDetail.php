<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OpnameDetail extends Model
{
    protected $table = 'ms_opname_detail';

    protected $guarded = ['id'];

    public function opname(): BelongsTo
    {
        return $this->belongsTo(Opname::class, 'opname_id');
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
}
