<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StatusMahasiswa extends Model
{
    protected $table = 'status_mahasiswa';
    protected $primaryKey = 'id_status';
    public $timestamps = false;
    protected $fillable = ['kode_status', 'nama_status', 'is_aktif_kuliah'];
    protected function casts(): array { return ['is_aktif_kuliah' => 'boolean']; }
    public function mahasiswa(): HasMany { return $this->hasMany(Mahasiswa::class, 'id_status', 'id_status'); }
}
