@extends('layouts.admin')
@section('title', 'Manajemen KRS')
@section('page-label', 'Rencana Studi (KRS)')
@section('content')
<div class="page-head"><div><div class="eyebrow">Rencana studi mahasiswa</div><h1>Manajemen KRS</h1><p>Pantau Kartu Rencana Studi dan tinjau pengajuan yang memerlukan pemeriksaan akademik.</p></div></div>
<div class="stat-grid">
    <div class="stat-card"><div class="stat-label">Total KRS</div><div class="stat-value">{{ number_format($stats['total']) }}</div><div class="stat-note">Seluruh semester</div></div>
    <div class="stat-card"><div class="stat-label">Draft</div><div class="stat-value">{{ number_format($stats['draft']) }}</div><div class="stat-note">Belum diajukan</div></div>
    <div class="stat-card"><div class="stat-label">Menunggu Persetujuan</div><div class="stat-value">{{ number_format($stats['submitted']) }}</div><div class="stat-note">Status Diajukan</div></div>
    <div class="stat-card"><div class="stat-label">Disetujui</div><div class="stat-value">{{ number_format($stats['approved']) }}</div><div class="stat-note">Status Disetujui</div></div>
</div>
<form class="filter-bar" method="GET" action="{{ route('admin.krs-management.index') }}">
    <div class="field field-grow"><label for="search">Cari mahasiswa</label><input id="search" name="search" value="{{ request('search') }}" placeholder="NPM atau nama mahasiswa"></div>
    <div class="field field-small"><label for="kode_semester">Periode Akademik</label><select name="kode_semester" id="kode_semester"><option value="">Semua periode</option>@foreach($terms as $term)<option value="{{ $term->kode_semester }}" @selected(request('kode_semester')===$term->kode_semester)>{{ $term->kode_semester }} — {{ $term->nama_semester }}</option>@endforeach</select></div>
    <div class="field field-small"><label for="id_prodi">Program Studi</label><select name="id_prodi" id="id_prodi"><option value="">Semua prodi</option>@foreach($programs as $program)<option value="{{ $program->id_prodi }}" @selected((string)request('id_prodi')===(string)$program->id_prodi)>{{ $program->nama_prodi }}</option>@endforeach</select></div>
    <div class="field field-small"><label for="status">Status KRS</label><select name="status" id="status"><option value="">Semua status</option>@foreach(['Draft','Diajukan','Disetujui','Ditolak'] as $status)<option value="{{ $status }}" @selected(request('status')===$status)>{{ $status }}</option>@endforeach</select></div>
    <button class="btn btn-primary" type="submit">Terapkan</button><a class="btn" href="{{ route('admin.krs-management.index') }}">Reset</a>
</form>
<section class="card">
<div class="panel-head"><div><h2>Daftar KRS Mahasiswa</h2><div class="secondary-text">{{ number_format($records->total()) }} KRS ditemukan</div></div></div>
<div class="table-wrap"><table class="data-table"><thead><tr><th>Mahasiswa</th><th>Program Studi</th><th>Periode</th><th>Tanggal Isi</th><th>Jumlah MK</th><th>Total SKS</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
@forelse($records as $item)
@php($totalSks = $item->details->sum(fn($detail) => (int)($detail->kelasKuliah?->mataKuliah?->total_sks ?? 0)))
<tr>
<td><div class="primary-text">{{ $item->mahasiswa?->nama ?? 'Mahasiswa tidak ditemukan' }}</div><div class="secondary-text">{{ $item->npm }}</div></td>
<td>{{ $item->mahasiswa?->prodi?->nama_prodi ?? '—' }}</td>
<td>{{ $item->tahunAkademik?->nama_semester ?? $item->kode_semester }}<div class="secondary-text">{{ $item->tahunAkademik?->tahun_ajaran }}</div></td>
<td>{{ $item->tgl_pengisian?->format('d M Y') ?? '—' }}</td>
<td>{{ $item->details->count() }}</td>
<td>{{ $totalSks }}</td>
<td><span class="badge {{ $item->status_persetujuan_pa==='Disetujui'?'badge-success':($item->status_persetujuan_pa==='Ditolak'?'badge-danger':($item->status_persetujuan_pa==='Diajukan'?'badge-warning':'badge-info')) }}">{{ $item->status_persetujuan_pa }}</span></td>
<td><a class="btn btn-sm btn-primary" href="{{ route('admin.krs-management.review',$item->id_krs) }}">Tinjau KRS</a></td>
</tr>
@empty<tr><td colspan="8"><div class="empty"><strong>Belum ada KRS sesuai filter</strong>Data akan tampil setelah KRS tersimpan di database.</div></td></tr>@endforelse
</tbody></table></div>
<div class="pagination"><span>Menampilkan {{ $records->firstItem() ?? 0 }}–{{ $records->lastItem() ?? 0 }} dari {{ number_format($records->total()) }} KRS</span>{{ $records->links() }}</div>
</section>
@endsection
