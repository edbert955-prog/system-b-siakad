<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class KelasKuliah extends Model
{
    protected $table='kelas_kuliah'; protected $primaryKey='id_kelas'; public $timestamps=false; protected $fillable=['id_mk','kode_semester','nama_kelas','id_dosen_pengampu','id_ruangan','hari','jam_mulai','jam_selesai','kuota'];
    public function mataKuliah(): BelongsTo { return $this->belongsTo(MataKuliah::class,'id_mk','id_mk'); }
    public function tahunAkademik(): BelongsTo { return $this->belongsTo(TahunAkademik::class,'kode_semester','kode_semester'); }
    public function dosenPengampu(): BelongsTo { return $this->belongsTo(Dosen::class,'id_dosen_pengampu','id_dosen'); }
    public function ruangan(): BelongsTo { return $this->belongsTo(Ruangan::class,'id_ruangan','id_ruangan'); }
    public function krsDetails(): HasMany { return $this->hasMany(KrsDetail::class,'id_kelas','id_kelas'); }
}
