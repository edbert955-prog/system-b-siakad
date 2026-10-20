@extends('layouts.admin')
@section('title', 'Manajemen Dosen')
@section('page-label', 'Dosen')
@section('content')
<div class="page-head"><div><div class="eyebrow">Data sumber daya akademik</div><h1>Manajemen Dosen</h1><p>Kelola dosen yang benar-benar tersimpan pada tabel dosen. Halaman ini tidak menampilkan profil contoh dari desain.</p></div><div class="actions"><a class="btn btn-primary" href="{{ route('admin.lecturers.create') }}">＋ Tambah Dosen</a></div></div>
<div class="stat-grid">
<div class="stat-card"><div class="stat-label">Total Dosen</div><div class="stat-value">{{ number_format($stats['total_dosen']) }}</div><div class="stat-note">Seluruh dosen pada database</div></div>
<div class="stat-card"><div class="stat-label">Dosen dengan Mahasiswa PA</div><div class="stat-value">{{ number_format($stats['dosen_dengan_mahasiswa_pa']) }}</div><div class="stat-note">Dosen yang tercatat membimbing mahasiswa</div></div>
<div class="stat-card"><div class="stat-label">Kelas Kuliah</div><div class="stat-value">{{ number_format($stats['total_kelas']) }}</div><div class="stat-note">Jumlah record pada tabel kelas kuliah</div></div>
<div class="stat-card"><div class="stat-label">Program Homebase</div><div class="stat-value">{{ number_format($stats['homebase_terpakai']) }}</div><div class="stat-note">Program studi dengan dosen terdaftar</div></div>
</div>
<form class="filter-bar" method="GET" action="{{ route('admin.lecturers.index') }}">
<div class="field field-grow"><label for="search">Cari dosen</label><input id="search" name="search" value="{{ request('search') }}" placeholder="Nama, NIDN, atau NIP..."></div>
<div class="field field-small"><label for="id_prodi_homebase">Program homebase</label><select id="id_prodi_homebase" name="id_prodi_homebase"><option value="">Semua program</option>@foreach($programs as $program)<option value="{{ $program->id_prodi }}" @selected((string)request('id_prodi_homebase') === (string)$program->id_prodi)>{{ $program->nama_prodi }}</option>@endforeach</select></div>
<button class="btn btn-primary" type="submit">Cari</button><a class="btn" href="{{ route('admin.lecturers.index') }}">Reset</a>
</form>
<section class="card"><div class="panel-head"><div><h2>Daftar Dosen</h2><div class="secondary-text">{{ number_format($lecturers->total()) }} dosen ditemukan sesuai filter.</div></div></div><div class="table-wrap"><table class="data-table"><thead><tr><th>Identitas Dosen</th><th>NIDN / NIP</th><th>Homebase</th><th>Jabatan Fungsional</th><th>Mahasiswa PA</th><th>Kelas Diampu</th><th>Aksi</th></tr></thead><tbody>
@forelse($lecturers as $lecturer)
<tr><td><div class="primary-text">{{ trim(($lecturer->gelar_depan ? $lecturer->gelar_depan.' ' : '').$lecturer->nama_dosen.($lecturer->gelar_belakang ? ', '.$lecturer->gelar_belakang : '')) }}</div><div class="secondary-text">{{ $lecturer->email_dosen ?: 'Email belum diisi' }}</div></td><td><div>{{ $lecturer->nidn }}</div><div class="secondary-text">NIP: {{ $lecturer->nip ?: '—' }}</div></td><td>{{ $lecturer->prodiHomebase?->nama_prodi ?? 'Belum ditetapkan' }}<div class="secondary-text">{{ $lecturer->prodiHomebase?->fakultas?->nama_fakultas ?? '—' }}</div></td><td>{{ $lecturer->jabatan_fungsional ?: '—' }}</td><td>{{ number_format($lecturer->mahasiswa_bimbingan_count ?? 0) }}</td><td>{{ number_format($lecturer->kelas_diampu_count ?? 0) }}</td><td><div class="table-actions"><a class="btn btn-sm" href="{{ route('admin.lecturers.edit', $lecturer->id_dosen) }}">Edit</a><form class="inline-form" method="POST" action="{{ route('admin.lecturers.destroy', $lecturer->id_dosen) }}" onsubmit="return confirm('Hapus data dosen ini? Penghapusan akan ditolak jika masih digunakan data akademik.')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger" type="submit">Hapus</button></form></div></td></tr>
@empty<tr><td colspan="7"><div class="empty"><strong>Tidak ada dosen ditemukan</strong>Coba ubah kata kunci atau filter homebase.</div></td></tr>@endforelse
</tbody></table></div><div class="pagination"><span>Menampilkan {{ $lecturers->firstItem() ?? 0 }}–{{ $lecturers->lastItem() ?? 0 }} dari {{ number_format($lecturers->total()) }} dosen</span>{{ $lecturers->links() }}</div></section>
@endsection
