<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fakultas extends Model
{
    protected $table = 'fakultas';
    protected $primaryKey = 'id_fakultas';
    public $timestamps = false;
    protected $fillable = ['kode_fakultas', 'nama_fakultas'];

    public function prodi(): HasMany
    {
        return $this->hasMany(Prodi::class, 'id_fakultas', 'id_fakultas');
    }

    public function ruangan(): HasMany
    {
        return $this->hasMany(Ruangan::class, 'id_fakultas', 'id_fakultas');
    }
}
