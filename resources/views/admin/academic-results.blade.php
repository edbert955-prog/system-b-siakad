@extends('layouts.admin')
@section('title', 'KHS & Transkrip')
@section('page-label', 'KHS & Transkrip')
@section('content')
<div class="page-head"><div><div class="eyebrow">Hasil studi</div><h1>KHS &amp; Transkrip Akademik</h1><p>Cari mahasiswa berdasarkan NPM untuk melihat nilai semester dan riwayat nilai yang tersedia pada database.</p></div></div>
<section class="card panel"><div class="panel-body">
<form class="filter-bar" style="margin:0;padding:0;border:0;box-shadow:none" method="GET" action="{{ route('admin.academic-results.index') }}">
    <div class="field field-grow"><label for="npm">NPM Mahasiswa</label><input id="npm" name="npm" value="{{ request('npm') }}" placeholder="Masukkan NPM mahasiswa" required></div>
    <div class="field field-small"><label for="kode_semester">Periode KHS</label><select id="kode_semester" name="kode_semester"><option value="">Semester terbaru</option>@foreach($terms as $term)<option value="{{ $term->kode_semester }}" @selected($selectedTerm===$term->kode_semester)>{{ $term->kode_semester }} — {{ $term->nama_semester }}</option>@endforeach</select></div>
    <button type="submit" class="btn btn-primary">Tampilkan</button><a class="btn" href="{{ route('admin.academic-results.index') }}">Reset</a>
</form>
</div></section>
@if(request()->filled('npm') && !$student)
<section class="card"><div class="empty"><strong>Mahasiswa tidak ditemukan</strong>Periksa kembali NPM yang dimasukkan.</div></section>
@endif
@if($student)
<section class="card panel" style="margin-top:18px"><div class="panel-head"><div><h2>{{ $student->nama }}</h2><div class="secondary-text">{{ $student->npm }} · {{ $student->prodi?->nama_prodi ?? 'Program studi tidak tersedia' }}</div></div><span class="badge badge-info">{{ $student->angkatan }}</span></div>
<div class="panel-body"><div class="stat-grid" style="margin-bottom:0;grid-template-columns:repeat(3,minmax(0,1fr))">
<div class="stat-card"><div class="stat-label">IPK tersimpan</div><div class="stat-value">{{ number_format((float)$student->ipk,2) }}</div><div class="stat-note">Nilai dari tabel mahasiswa</div></div>
<div class="stat-card"><div class="stat-label">IPS periode terpilih</div><div class="stat-value">{{ $ips === null ? '—' : number_format($ips,2) }}</div><div class="stat-note">Dihitung dari nilai yang tersedia</div></div>
<div class="stat-card"><div class="stat-label">SKS dinilai</div><div class="stat-value">{{ $totalSks }}</div><div class="stat-note">Pada periode terpilih</div></div>
</div></div></section>
<section class="card panel" style="margin-top:18px"><div class="panel-head"><div><h2>Rincian Nilai Hasil Studi Semester</h2><div class="secondary-text">{{ $selectedKrs?->tahunAkademik?->nama_semester ?? $selectedKrs?->kode_semester ?? 'Belum ada KRS' }} {{ $selectedKrs?->tahunAkademik?->tahun_ajaran }}</div></div><span class="badge {{ $selectedKrs?->status_persetujuan_pa==='Disetujui'?'badge-success':'badge-warning' }}">{{ $selectedKrs?->status_persetujuan_pa ?? 'Tidak ada KRS' }}</span></div>
<div class="table-wrap"><table class="data-table"><thead><tr><th>Kode</th><th>Mata Kuliah</th><th>SKS</th><th>Nilai Angka</th><th>Huruf</th><th>Bobot Mutu</th><th>Dosen</th></tr></thead><tbody>
@forelse($details as $detail)
<tr><td>{{ $detail->kelasKuliah?->mataKuliah?->kode_mk ?? '—' }}</td><td>{{ $detail->kelasKuliah?->mataKuliah?->nama_mk ?? '—' }}</td><td>{{ $detail->kelasKuliah?->mataKuliah?->total_sks ?? 0 }}</td><td>{{ $detail->nilai_angka ?? 'Belum ada' }}</td><td>{{ $detail->nilai_huruf ?? '—' }}</td><td>{{ $detail->bobot_mutu ?? '—' }}</td><td>{{ $detail->kelasKuliah?->dosenPengampu?->nama_dosen ?? '—' }}</td></tr>
@empty<tr><td colspan="7"><div class="empty"><strong>Belum ada rincian nilai</strong>Pastikan KRS dan nilai sudah tersedia pada database.</div></td></tr>@endforelse
</tbody></table></div>
</section>
<section class="card panel" style="margin-top:18px"><div class="panel-head"><div><h2>Transkrip Nilai yang Tersedia</h2><div class="secondary-text">Semua rincian nilai dari KRS mahasiswa ini.</div></div><button class="btn btn-sm" type="button" onclick="window.print()">Cetak / Simpan PDF</button></div>
<div class="table-wrap"><table class="data-table"><thead><tr><th>Semester</th><th>Kode</th><th>Mata Kuliah</th><th>SKS</th><th>Nilai</th><th>Bobot</th></tr></thead><tbody>
@forelse($transcript as $detail)
<tr><td>{{ $detail->academic_term?->nama_semester ?? '—' }}<div class="secondary-text">{{ $detail->academic_term?->tahun_ajaran }}</div></td><td>{{ $detail->kelasKuliah?->mataKuliah?->kode_mk ?? '—' }}</td><td>{{ $detail->kelasKuliah?->mataKuliah?->nama_mk ?? '—' }}</td><td>{{ $detail->kelasKuliah?->mataKuliah?->total_sks ?? 0 }}</td><td>{{ $detail->nilai_huruf ?? 'Belum dinilai' }}</td><td>{{ $detail->bobot_mutu ?? '—' }}</td></tr>
@empty<tr><td colspan="6"><div class="empty"><strong>Belum ada riwayat transkrip</strong></div></td></tr>@endforelse
</tbody></table></div></section>
@endif
@endsection
