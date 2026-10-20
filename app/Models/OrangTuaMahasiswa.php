<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class OrangTuaMahasiswa extends Model
{
    protected $table='orang_tua_mahasiswa'; protected $primaryKey='id_ortu'; public $timestamps=false;
    protected $fillable=['npm','hubungan','nama_lengkap','pekerjaan','penghasilan_bulanan','no_hp'];
    protected function casts(): array { return ['penghasilan_bulanan'=>'decimal:2']; }
    public function mahasiswa(): BelongsTo { return $this->belongsTo(Mahasiswa::class,'npm','npm'); }
}
