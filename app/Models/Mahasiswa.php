<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mahasiswa extends Model
{
    protected $table = 'mahasiswa';
    protected $primaryKey = 'npm';
    public $incrementing = false;
    protected $keyType = 'string';
    // tabel mahasiswa memiliki created_at dan updated_at.
    public $timestamps = true;
    protected $fillable = ['npm','raw_npm_siakadu','nik','nisn','nama','jenis_kelamin','tempat_lahir','tgl_lahir','agama','email_kampus','email_pribadi','no_hp','alamat_domisili','kode_pos','id_prodi','id_status','id_jalur','id_dosen_pa','angkatan','tgl_masuk','tgl_lulus','semester_berjalan','ipk','total_sks_lulus','golongan_ukt','nominal_ukt'];
    protected function casts(): array { return ['tgl_lahir'=>'date','tgl_masuk'=>'date','tgl_lulus'=>'date','ipk'=>'decimal:2','nominal_ukt'=>'decimal:2','semester_berjalan'=>'integer','total_sks_lulus'=>'integer','angkatan'=>'integer']; }
    public function prodi(): BelongsTo { return $this->belongsTo(Prodi::class, 'id_prodi', 'id_prodi'); }
    public function statusMahasiswa(): BelongsTo { return $this->belongsTo(StatusMahasiswa::class, 'id_status', 'id_status'); }
    public function jalurMasuk(): BelongsTo { return $this->belongsTo(JalurMasuk::class, 'id_jalur', 'id_jalur'); }
    public function dosenPa(): BelongsTo { return $this->belongsTo(Dosen::class, 'id_dosen_pa', 'id_dosen'); }
    public function orangTua(): HasMany { return $this->hasMany(OrangTuaMahasiswa::class, 'npm', 'npm'); }
    public function riwayatStatusSemester(): HasMany { return $this->hasMany(RiwayatStatusSemester::class, 'npm', 'npm'); }
    public function krs(): HasMany { return $this->hasMany(Krs::class, 'npm', 'npm'); }
    public function pembayaranUkt(): HasMany { return $this->hasMany(PembayaranUkt::class, 'npm', 'npm'); }
}
