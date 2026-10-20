@extends('layouts.admin')
@section('title', 'Direktori Mahasiswa')
@section('page-label', 'Mahasiswa')
@section('content')
<div class="page-head"><div><div class="eyebrow">Data akademik</div><h1>Direktori Mahasiswa</h1><p>Cari dan kelola data mahasiswa yang tersimpan di database SIAKADU.</p></div><div class="actions"><a class="btn btn-primary" href="{{ route('admin.students.create') }}">＋ Tambah Mahasiswa</a></div></div>
<form class="filter-bar" method="GET" action="{{ route('admin.students.index') }}">
    <div class="field field-grow"><label for="search">Cari mahasiswa</label><input id="search" name="search" value="{{ request('search') }}" placeholder="Cari NPM, nama, atau email kampus..."></div>
    <div class="field field-small"><label for="id_prodi">Program Studi</label><select id="id_prodi" name="id_prodi"><option value="">Semua program studi</option>@foreach($programs as $program)<option value="{{ $program->id_prodi }}" @selected((string)request('id_prodi') === (string)$program->id_prodi)>{{ $program->nama_prodi }}</option>@endforeach</select></div>
    <div class="field field-small"><label for="id_status">Status</label><select id="id_status" name="id_status"><option value="">Semua status</option>@foreach($statuses as $status)<option value="{{ $status->id_status }}" @selected((string)request('id_status') === (string)$status->id_status)>{{ $status->nama_status }}</option>@endforeach</select></div>
    <div class="field field-small"><label for="angkatan">Angkatan</label><input id="angkatan" name="angkatan" type="number" min="1960" max="2100" value="{{ request('angkatan') }}" placeholder="Contoh: 2024"></div>
    <button class="btn btn-primary" type="submit">Cari / Filter</button><a class="btn" href="{{ route('admin.students.index') }}">Reset</a>
</form>
<section class="card"><div class="panel-head"><div><h2>Data Mahasiswa</h2><div class="secondary-text">{{ number_format($students->total()) }} data sesuai filter.</div></div><span class="badge badge-info">{{ $students->perPage() }} baris / halaman</span></div>
<div class="table-wrap"><table class="data-table"><thead><tr><th>NPM</th><th>Nama Mahasiswa</th><th>Program Studi</th><th>Angkatan</th><th>Status</th><th>IPK</th><th>SKS Lulus</th><th>Dosen PA</th><th>Aksi</th></tr></thead><tbody>
@forelse($students as $student)
<tr>
<td class="number"><span class="primary-text">{{ $student->npm }}</span></td>
<td><div class="primary-text">{{ $student->nama }}</div><div class="secondary-text">{{ $student->email_kampus ?: 'Email kampus belum diisi' }}</div></td>
<td>{{ $student->prodi?->nama_prodi ?? '—' }}<div class="secondary-text">{{ $student->prodi?->fakultas?->nama_fakultas ?? 'Fakultas belum diatur' }}</div></td>
<td>{{ $student->angkatan }}</td>
<td><span class="badge {{ $student->statusMahasiswa?->is_aktif_kuliah ? 'badge-success' : 'badge-warning' }}">{{ $student->statusMahasiswa?->nama_status ?? 'Tidak diketahui' }}</span></td>
<td class="number">{{ number_format((float)$student->ipk, 2) }}</td><td class="number">{{ $student->total_sks_lulus ?? 0 }}</td>
<td>{{ $student->dosenPa?->nama_dosen ?? 'Belum ditetapkan' }}</td>
<td><div class="table-actions"><a class="btn btn-sm" href="{{ route('admin.students.show', $student->npm) }}">Detail</a><a class="btn btn-sm" href="{{ route('admin.students.edit', $student->npm) }}">Edit</a></div></td>
</tr>
@empty<tr><td colspan="9"><div class="empty"><strong>Tidak ada mahasiswa ditemukan</strong>Coba ubah kata kunci atau reset filter pencarian.</div></td></tr>@endforelse
</tbody></table></div><div class="pagination"><span>Menampilkan {{ $students->firstItem() ?? 0 }}–{{ $students->lastItem() ?? 0 }} dari {{ number_format($students->total()) }} mahasiswa</span>{{ $students->links() }}</div></section>
@endsection
