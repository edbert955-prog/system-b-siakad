<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JalurMasuk extends Model
{
    protected $table = 'jalur_masuk';
    protected $primaryKey = 'id_jalur';
    public $timestamps = false;
    protected $fillable = ['kode_jalur', 'nama_jalur'];
    public function mahasiswa(): HasMany { return $this->hasMany(Mahasiswa::class, 'id_jalur', 'id_jalur'); }
}
