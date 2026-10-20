<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class KrsDetail extends Model
{
    protected $table='krs_detail'; protected $primaryKey='id_krs_detail'; public $timestamps=false; protected $fillable=['id_krs','id_kelas','nilai_angka','nilai_huruf','bobot_mutu'];
    protected function casts(): array { return ['nilai_angka'=>'decimal:2','bobot_mutu'=>'decimal:2']; }
    public function krs(): BelongsTo { return $this->belongsTo(Krs::class,'id_krs','id_krs'); }
    public function kelasKuliah(): BelongsTo { return $this->belongsTo(KelasKuliah::class,'id_kelas','id_kelas'); }
}
