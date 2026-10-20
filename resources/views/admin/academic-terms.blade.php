@extends('layouts.admin')
@section('title', 'Periode Akademik')
@section('page-label', 'Periode Akademik')
@section('content')
<div class="page-head">
    <div><div class="eyebrow">Kalender akademik</div><h1>Periode Akademik</h1><p>Kelola semester, tahun ajaran, periode KRS, dan semester aktif. Data diambil dari tabel <code>tahun_akademik</code>.</p></div>
    <div class="actions"><a class="btn btn-primary" href="{{ route('admin.academic-terms.create') }}">＋ Tambah Periode</a></div>
</div>
<div class="stat-grid">
    <div class="stat-card"><div class="stat-label">Total Periode</div><div class="stat-value">{{ number_format($stats['total']) }}</div><div class="stat-note">Semua periode terdaftar</div></div>
    <div class="stat-card"><div class="stat-label">Periode Aktif</div><div class="stat-value">{{ number_format($stats['active']) }}</div><div class="stat-note">Idealnya hanya satu semester aktif</div></div>
    <div class="stat-card"><div class="stat-label">Semester Ganjil</div><div class="stat-value">{{ number_format($stats['odd']) }}</div><div class="stat-note">Sesuai jenis semester pada database</div></div>
    <div class="stat-card"><div class="stat-label">Semester Genap</div><div class="stat-value">{{ number_format($stats['even']) }}</div><div class="stat-note">Sesuai jenis semester pada database</div></div>
</div>
<form class="filter-bar" method="GET" action="{{ route('admin.academic-terms.index') }}">
    <div class="field field-grow"><label for="search">Cari periode</label><input id="search" name="search" value="{{ request('search') }}" placeholder="Kode, nama semester, atau tahun ajaran"></div>
    <div class="field field-small"><label for="jenis_semester">Jenis semester</label><select id="jenis_semester" name="jenis_semester"><option value="">Semua jenis</option>@foreach(['Ganjil','Genap','Antara'] as $kind)<option value="{{ $kind }}" @selected(request('jenis_semester')===$kind)>{{ $kind }}</option>@endforeach</select></div>
    <div class="field field-small"><label for="status">Status</label><select id="status" name="status"><option value="">Semua status</option><option value="aktif" @selected(request('status')==='aktif')>Aktif</option><option value="nonaktif" @selected(request('status')==='nonaktif')>Tidak aktif</option></select></div>
    <button class="btn btn-primary" type="submit">Terapkan</button><a class="btn" href="{{ route('admin.academic-terms.index') }}">Reset</a>
</form>
<section class="card">
    <div class="panel-head"><div><h2>Daftar Periode</h2><div class="secondary-text">{{ number_format($terms->total()) }} periode ditemukan</div></div></div>
    <div class="table-wrap"><table class="data-table">
        <thead><tr><th>Kode</th><th>Nama Semester</th><th>Tahun Ajaran</th><th>Jenis</th><th>Periode KRS</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
        @forelse($terms as $term)
            <tr>
                <td class="number"><strong>{{ $term->kode_semester }}</strong></td>
                <td>{{ $term->nama_semester }}</td>
                <td>{{ $term->tahun_ajaran }}</td>
                <td>{{ $term->jenis_semester }}</td>
                <td>{{ $term->tgl_mulai_krs?->format('d M Y') }} – {{ $term->tgl_selesai_krs?->format('d M Y') }}</td>
                <td>@if($term->is_aktif)<span class="badge badge-success">Aktif</span>@else<span class="badge">Nonaktif</span>@endif</td>
                <td><div class="table-actions"><a class="btn btn-sm" href="{{ route('admin.academic-terms.edit', $term->kode_semester) }}">Edit</a>
                <form class="inline-form" method="POST" action="{{ route('admin.academic-terms.destroy', $term->kode_semester) }}" onsubmit="return confirm('Hapus periode ini? Hanya periode yang belum direferensikan yang bisa dihapus.')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger" type="submit">Hapus</button></form></div></td>
            </tr>
        @empty
            <tr><td colspan="7"><div class="empty"><strong>Belum ada periode akademik</strong>Tambahkan periode untuk mengatur semester dan pembukaan KRS.</div></td></tr>
        @endforelse
        </tbody>
    </table></div>
    <div class="pagination"><span>Menampilkan {{ $terms->firstItem() ?? 0 }}–{{ $terms->lastItem() ?? 0 }} dari {{ number_format($terms->total()) }} periode</span>{{ $terms->links() }}</div>
</section>
@endsection
