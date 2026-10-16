<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kategori extends Model
{
    protected $table = 'ms_kategori';

    protected $guarded = ['id'];

    public function barang(): HasMany
    {
        return $this->hasMany(Barang::class, 'kategori_id');
    }
}
