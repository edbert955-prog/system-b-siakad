<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class RiwayatStatusSemester extends Model
{
    protected $table='riwayat_status_semester'; protected $primaryKey='id_riwayat'; public $timestamps=false;
    protected $fillable=['npm','kode_semester','id_status','ips','sks_diambil'];
    protected function casts(): array { return ['ips'=>'decimal:2','sks_diambil'=>'integer']; }
    public function mahasiswa(): BelongsTo { return $this->belongsTo(Mahasiswa::class,'npm','npm'); }
    public function statusMahasiswa(): BelongsTo { return $this->belongsTo(StatusMahasiswa::class,'id_status','id_status'); }
}
