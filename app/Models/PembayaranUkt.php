<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class PembayaranUkt extends Model
{
    protected $table='pembayaran_ukt'; protected $primaryKey='id_pembayaran'; public $timestamps=false; protected $fillable=['npm','kode_semester','nomor_va','nominal_tagihan','status_bayar','tgl_bayar','bank_mitra'];
    protected function casts(): array { return ['nominal_tagihan'=>'decimal:2','tgl_bayar'=>'datetime']; }
    public function mahasiswa(): BelongsTo { return $this->belongsTo(Mahasiswa::class,'npm','npm'); }
    public function tahunAkademik(): BelongsTo { return $this->belongsTo(TahunAkademik::class,'kode_semester','kode_semester'); }
}
