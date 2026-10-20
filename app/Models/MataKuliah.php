<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class MataKuliah extends Model
{
    protected $table='mata_kuliah'; protected $primaryKey='id_mk'; public $timestamps=false;
    protected $fillable=['kode_mk','nama_mk','id_prodi','sks_teori','sks_praktikum','total_sks','semester_paket','jenis_mk'];
    protected function casts(): array { return ['sks_teori'=>'integer','sks_praktikum'=>'integer','total_sks'=>'integer','semester_paket'=>'integer']; }
    public function prodi(): BelongsTo { return $this->belongsTo(Prodi::class,'id_prodi','id_prodi'); }
    public function kelasKuliah(): HasMany { return $this->hasMany(KelasKuliah::class,'id_mk','id_mk'); }
}
