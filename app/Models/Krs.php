<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Krs extends Model
{
    protected $table='krs'; protected $primaryKey='id_krs'; public $timestamps=false; protected $fillable=['npm','kode_semester','tgl_pengisian','status_persetujuan_pa','catatan_pa'];
    protected function casts(): array { return ['tgl_pengisian'=>'datetime']; }
    public function mahasiswa(): BelongsTo { return $this->belongsTo(Mahasiswa::class,'npm','npm'); }
    public function tahunAkademik(): BelongsTo { return $this->belongsTo(TahunAkademik::class,'kode_semester','kode_semester'); }
    public function details(): HasMany { return $this->hasMany(KrsDetail::class,'id_krs','id_krs'); }
}
