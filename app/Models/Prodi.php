<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prodi extends Model
{
    protected $table = 'prodi';
    protected $primaryKey = 'id_prodi';
    public $timestamps = false;
    protected $fillable = ['kode_prodi', 'id_fakultas', 'nama_prodi', 'jenjang', 'akreditasi', 'gelar_lulusan', 'sks_minimal'];

    protected function casts(): array { return ['sks_minimal' => 'integer']; }

    public function fakultas(): BelongsTo { return $this->belongsTo(Fakultas::class, 'id_fakultas', 'id_fakultas'); }
    public function mahasiswa(): HasMany { return $this->hasMany(Mahasiswa::class, 'id_prodi', 'id_prodi'); }
    public function dosen(): HasMany { return $this->hasMany(Dosen::class, 'id_prodi_homebase', 'id_prodi'); }
    public function mataKuliah(): HasMany { return $this->hasMany(MataKuliah::class, 'id_prodi', 'id_prodi'); }
}
