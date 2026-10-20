<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dosen extends Model
{
    protected $table = 'dosen';
    protected $primaryKey = 'id_dosen';
    public $timestamps = false;
    protected $fillable = ['nidn', 'nip', 'nama_dosen', 'gelar_depan', 'gelar_belakang', 'jenis_kelamin', 'email_dosen', 'no_hp', 'jabatan_fungsional', 'id_prodi_homebase'];
    public function prodiHomebase(): BelongsTo { return $this->belongsTo(Prodi::class, 'id_prodi_homebase', 'id_prodi'); }
    public function mahasiswaBimbingan(): HasMany { return $this->hasMany(Mahasiswa::class, 'id_dosen_pa', 'id_dosen'); }
    public function kelasDiampu(): HasMany { return $this->hasMany(KelasKuliah::class, 'id_dosen_pengampu', 'id_dosen'); }
}
