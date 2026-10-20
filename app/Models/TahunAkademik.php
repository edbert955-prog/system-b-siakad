<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class TahunAkademik extends Model
{
    protected $table='tahun_akademik'; protected $primaryKey='kode_semester'; public $incrementing=false; protected $keyType='string'; public $timestamps=false;
    protected $fillable=['kode_semester','nama_semester','tahun_ajaran','jenis_semester','tgl_mulai_krs','tgl_selesai_krs','is_aktif'];
    protected function casts(): array { return ['tgl_mulai_krs'=>'date','tgl_selesai_krs'=>'date','is_aktif'=>'boolean']; }
    public function kelasKuliah(): HasMany { return $this->hasMany(KelasKuliah::class,'kode_semester','kode_semester'); }
    public function krs(): HasMany { return $this->hasMany(Krs::class,'kode_semester','kode_semester'); }
    public function pembayaranUkt(): HasMany { return $this->hasMany(PembayaranUkt::class,'kode_semester','kode_semester'); }
}
