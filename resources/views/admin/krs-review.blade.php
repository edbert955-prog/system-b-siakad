@extends('layouts.admin')
@section('title', 'Review KRS')
@section('page-label', 'Review KRS')
@section('content')
<div class="page-head"><div><div class="eyebrow">Pemeriksaan akademik</div><h1>Review &amp; Persetujuan KRS</h1><p>Tinjau mata kuliah yang diambil, total SKS, dan catatan mahasiswa sebelum memperbarui status KRS.</p></div><div class="actions"><a class="btn" href="{{ route('admin.krs-management.index') }}">← Kembali ke daftar KRS</a></div></div>
<div class="grid-two">
    <section class="card panel"><div class="panel-head"><h2>Identitas Mahasiswa</h2><span class="badge badge-info">{{ $krs->status_persetujuan_pa }}</span></div><div class="panel-body">
        <div class="detail-list">
            <div class="detail-item"><div class="detail-label">Nama</div><div class="detail-value">{{ $krs->mahasiswa?->nama ?? '—' }}</div></div>
            <div class="detail-item"><div class="detail-label">NPM</div><div class="detail-value">{{ $krs->npm }}</div></div>
            <div class="detail-item"><div class="detail-label">Program Studi</div><div class="detail-value">{{ $krs->mahasiswa?->prodi?->nama_prodi ?? '—' }}</div></div>
            <div class="detail-item"><div class="detail-label">Dosen PA</div><div class="detail-value">{{ $krs->mahasiswa?->dosenPa?->nama_dosen ?? 'Belum ditetapkan' }}</div></div>
            <div class="detail-item"><div class="detail-label">Periode</div><div class="detail-value">{{ $krs->tahunAkademik?->nama_semester ?? $krs->kode_semester }} · {{ $krs->tahunAkademik?->tahun_ajaran }}</div></div>
            <div class="detail-item"><div class="detail-label">Tanggal Pengajuan</div><div class="detail-value">{{ $krs->tgl_pengisian?->format('d M Y H:i') ?? '—' }}</div></div>
        </div>
        <div style="margin-top:15px"><div class="detail-label">Catatan PA tersimpan</div><div style="margin-top:6px;white-space:pre-wrap">{{ $krs->catatan_pa ?: 'Belum ada catatan.' }}</div></div>
    </div></section>
    <section class="card panel"><div class="panel-head"><h2>Keputusan Akademik</h2></div><div class="panel-body">
        <form method="POST" action="{{ route('admin.krs-management.status',$krs->id_krs) }}">
            @csrf @method('PATCH')
            <div class="field"><label for="status_persetujuan_pa">Status KRS</label><select id="status_persetujuan_pa" name="status_persetujuan_pa" required>@foreach(['Diajukan','Disetujui','Ditolak'] as $status)<option value="{{ $status }}" @selected(old('status_persetujuan_pa',$krs->status_persetujuan_pa)===$status)>{{ $status }}</option>@endforeach</select></div>
            <div class="field" style="margin-top:14px"><label for="catatan_pa">Catatan / Alasan</label><textarea id="catatan_pa" name="catatan_pa" rows="5" placeholder="Wajib diisi jika KRS ditolak.">{{ old('catatan_pa',$krs->catatan_pa) }}</textarea></div>
            <p class="muted" style="font-size:12px">KRS hanya bisa disetujui jika sudah mempunyai minimal satu mata kuliah. Jika ditolak, catatan wajib diisi.</p>
            <div class="form-actions"><button type="submit" class="btn btn-primary" onclick="return confirm('Simpan keputusan KRS ini?')">Simpan Keputusan</button></div>
        </form>
    </div></section>
</div>
<section class="card panel" style="margin-top:20px">
<div class="panel-head"><div><h2>Daftar Mata Kuliah yang Diajukan</h2><div class="secondary-text">{{ $krs->details->count() }} mata kuliah pada KRS ini</div></div><span class="badge badge-info">Total {{ $totalSks }} SKS</span></div>
<div class="table-wrap"><table class="data-table"><thead><tr><th>Kode MK</th><th>Mata Kuliah</th><th>SKS</th><th>Kelas</th><th>Dosen</th><th>Jadwal</th><th>Ruangan</th></tr></thead><tbody>
@forelse($krs->details as $detail)
@php($class = $detail->kelasKuliah)
<tr><td>{{ $class?->mataKuliah?->kode_mk ?? '—' }}</td><td><span class="primary-text">{{ $class?->mataKuliah?->nama_mk ?? 'Mata kuliah tidak tersedia' }}</span></td><td>{{ $class?->mataKuliah?->total_sks ?? 0 }}</td><td>{{ $class?->nama_kelas ?? '—' }}</td><td>{{ $class?->dosenPengampu?->nama_dosen ?? '—' }}</td><td>{{ $class?->hari ?? '—' }}<div class="secondary-text">{{ substr((string)($class?->jam_mulai ?? ''),0,5) }}–{{ substr((string)($class?->jam_selesai ?? ''),0,5) }}</div></td><td>{{ $class?->ruangan?->kode_ruangan ?? '—' }}</td></tr>
@empty<tr><td colspan="7"><div class="empty"><strong>Belum ada mata kuliah</strong>KRS ini belum memiliki rincian mata kuliah.</div></td></tr>@endforelse
</tbody></table></div>
</section>
@endsection
