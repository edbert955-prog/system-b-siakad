@extends('layouts.admin')
@section('title', 'Jadwal Kuliah')
@section('page-label', 'Jadwal Kuliah')
@section('content')
<div class="page-head"><div><div class="eyebrow">Operasional perkuliahan</div><h1>Manajemen Jadwal Kuliah</h1><p>Atur kelas, dosen pengampu, ruangan, hari, waktu, dan kuota. Sistem akan memeriksa benturan jadwal dosen serta ruangan.</p></div><div class="actions"><a class="btn btn-primary" href="{{ route('admin.class-schedules.create') }}">＋ Tambah Jadwal</a></div></div>
<div class="stat-grid">
    <div class="stat-card"><div class="stat-label">Total Kelas</div><div class="stat-value">{{ number_format($stats['classes']) }}</div><div class="stat-note">Seluruh kelas pada database</div></div>
    <div class="stat-card"><div class="stat-label">Semester Aktif</div><div class="stat-value">{{ number_format($stats['terms']) }}</div><div class="stat-note">Periode yang ditandai aktif</div></div>
    <div class="stat-card"><div class="stat-label">Belum Memiliki Ruangan</div><div class="stat-value">{{ number_format($stats['unassigned_rooms']) }}</div><div class="stat-note">Perlu ditinjau oleh admin</div></div>
    <div class="stat-card"><div class="stat-label">Tampilan</div><div class="stat-value" style="font-size:20px">Daftar</div><div class="stat-note">Diurutkan menurut hari dan jam</div></div>
</div>
<form class="filter-bar" method="GET" action="{{ route('admin.class-schedules.index') }}">
    <div class="field field-grow"><label for="search">Cari kelas atau mata kuliah</label><input id="search" name="search" value="{{ request('search') }}" placeholder="Kode MK, nama MK, atau nama kelas"></div>
    <div class="field field-small"><label for="kode_semester">Periode</label><select id="kode_semester" name="kode_semester"><option value="">Semua periode</option>@foreach($terms as $term)<option value="{{ $term->kode_semester }}" @selected(request('kode_semester')===$term->kode_semester)>{{ $term->kode_semester }} · {{ $term->nama_semester }}</option>@endforeach</select></div>
    <div class="field field-small"><label for="hari">Hari</label><select id="hari" name="hari"><option value="">Semua hari</option>@foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'] as $day)<option value="{{ $day }}" @selected(request('hari')===$day)>{{ $day }}</option>@endforeach</select></div>
    <div class="field field-small"><label for="id_dosen_pengampu">Dosen</label><select id="id_dosen_pengampu" name="id_dosen_pengampu"><option value="">Semua dosen</option>@foreach($lecturers as $lecturer)<option value="{{ $lecturer->id_dosen }}" @selected((string)request('id_dosen_pengampu')===(string)$lecturer->id_dosen)>{{ $lecturer->nama_dosen }}</option>@endforeach</select></div>
    <button class="btn btn-primary" type="submit">Cari</button><a class="btn" href="{{ route('admin.class-schedules.index') }}">Reset</a>
</form>
<section class="card"><div class="panel-head"><div><h2>Jadwal Terdaftar</h2><div class="secondary-text">{{ number_format($classes->total()) }} kelas ditemukan</div></div></div>
<div class="table-wrap"><table class="data-table"><thead><tr><th>Kode / Mata Kuliah</th><th>Periode</th><th>Kelas</th><th>Dosen Pengampu</th><th>Hari & Waktu</th><th>Ruangan</th><th>Kuota</th><th>Aksi</th></tr></thead><tbody>
@forelse($classes as $item)
<tr>
<td><div class="primary-text">{{ $item->mataKuliah?->kode_mk ?? '—' }}</div><div class="secondary-text">{{ $item->mataKuliah?->nama_mk ?? 'Mata kuliah tidak ditemukan' }}</div></td>
<td>{{ $item->tahunAkademik?->nama_semester ?? $item->kode_semester }}<div class="secondary-text">{{ $item->tahunAkademik?->tahun_ajaran }}</div></td>
<td><span class="badge badge-info">{{ $item->nama_kelas }}</span></td>
<td>{{ $item->dosenPengampu?->nama_dosen ?? 'Belum ditetapkan' }}</td>
<td><strong>{{ $item->hari }}</strong><div class="secondary-text">{{ substr((string)$item->jam_mulai,0,5) }}–{{ substr((string)$item->jam_selesai,0,5) }}</div></td>
<td>{{ $item->ruangan?->kode_ruangan ?? 'Belum diatur' }}<div class="secondary-text">{{ $item->ruangan?->nama_gedung }}</div></td>
<td>{{ $item->krsDetails()->whereHas('krs', fn($q) => $q->whereIn('status_persetujuan_pa',['Diajukan','Disetujui']))->count() }} / {{ $item->kuota }}</td>
<td><div class="table-actions"><a class="btn btn-sm" href="{{ route('admin.class-schedules.edit', $item->id_kelas) }}">Edit</a><form class="inline-form" method="POST" action="{{ route('admin.class-schedules.destroy', $item->id_kelas) }}" onsubmit="return confirm('Hapus kelas ini?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger" type="submit">Hapus</button></form></div></td>
</tr>
@empty<tr><td colspan="8"><div class="empty"><strong>Belum ada jadwal yang sesuai</strong>Ubah filter atau tambahkan jadwal kelas baru.</div></td></tr>@endforelse
</tbody></table></div><div class="pagination"><span>Menampilkan {{ $classes->firstItem() ?? 0 }}–{{ $classes->lastItem() ?? 0 }} dari {{ number_format($classes->total()) }} kelas</span>{{ $classes->links() }}</div></section>
@endsection
