<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Ruangan extends Model
{
    protected $table='ruangan'; protected $primaryKey='id_ruangan'; public $timestamps=false; protected $fillable=['kode_ruangan','nama_gedung','kapasitas','id_fakultas'];
    protected function casts(): array { return ['kapasitas'=>'integer']; }
    public function fakultas(): BelongsTo { return $this->belongsTo(Fakultas::class,'id_fakultas','id_fakultas'); }
    public function kelasKuliah(): HasMany { return $this->hasMany(KelasKuliah::class,'id_ruangan','id_ruangan'); }
}
