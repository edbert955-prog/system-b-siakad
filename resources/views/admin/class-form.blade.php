@extends('layouts.admin')
@section('title', $title)
@section('page-label', 'Jadwal Kuliah')
@section('content')
@php($get = fn($field, $default = '') => old($field, data_get($record, $field, $default)))
<div class="page-head"><div><div class="eyebrow">Operasional perkuliahan</div><h1>{{ $title }}</h1><p>Lengkapi seluruh informasi kelas. Dosen dan ruangan tidak boleh memiliki jadwal yang bentrok pada waktu yang sama.</p></div><div class="actions"><a class="btn" href="{{ route('admin.class-schedules.index') }}">← Kembali</a></div></div>
<section class="card"><div class="panel-body"><form method="POST" action="{{ $action }}">@csrf @if($method !== 'POST') @method($method) @endif
<div class="form-grid">
    <div class="field field-full"><label for="id_mk">Mata Kuliah *</label><select id="id_mk" name="id_mk" required><option value="">Pilih mata kuliah</option>@foreach($courses as $course)<option value="{{ $course->id_mk }}" @selected((string)$get('id_mk')===(string)$course->id_mk)>{{ $course->kode_mk }} — {{ $course->nama_mk }} ({{ $course->prodi?->nama_prodi }})</option>@endforeach</select></div>
    <div class="field"><label for="kode_semester">Periode Akademik *</label><select id="kode_semester" name="kode_semester" required><option value="">Pilih periode</option>@foreach($terms as $term)<option value="{{ $term->kode_semester }}" @selected($get('kode_semester')===$term->kode_semester)>{{ $term->kode_semester }} — {{ $term->nama_semester }} ({{ $term->tahun_ajaran }})</option>@endforeach</select></div>
    <div class="field"><label for="nama_kelas">Nama Kelas *</label><input id="nama_kelas" name="nama_kelas" maxlength="10" value="{{ $get('nama_kelas') }}" placeholder="Contoh: A" required></div>
    <div class="field"><label for="id_dosen_pengampu">Dosen Pengampu *</label><select id="id_dosen_pengampu" name="id_dosen_pengampu" required><option value="">Pilih dosen</option>@foreach($lecturers as $lecturer)<option value="{{ $lecturer->id_dosen }}" @selected((string)$get('id_dosen_pengampu')===(string)$lecturer->id_dosen)>{{ $lecturer->nama_dosen }}</option>@endforeach</select></div>
    <div class="field"><label for="id_ruangan">Ruangan</label><select id="id_ruangan" name="id_ruangan"><option value="">Belum ditetapkan</option>@foreach($rooms as $room)<option value="{{ $room->id_ruangan }}" @selected((string)$get('id_ruangan')===(string)$room->id_ruangan)>{{ $room->kode_ruangan }} — {{ $room->nama_gedung }} (kapasitas {{ $room->kapasitas }})</option>@endforeach</select></div>
    <div class="field"><label for="hari">Hari *</label><select id="hari" name="hari" required><option value="">Pilih hari</option>@foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'] as $day)<option value="{{ $day }}" @selected($get('hari')===$day)>{{ $day }}</option>@endforeach</select></div>
    <div class="field"><label for="kuota">Kuota Mahasiswa *</label><input type="number" id="kuota" name="kuota" min="1" max="500" value="{{ $get('kuota',40) }}" required></div>
    <div class="field"><label for="jam_mulai">Jam Mulai *</label><input type="time" id="jam_mulai" name="jam_mulai" value="{{ substr((string)$get('jam_mulai'),0,5) }}" required></div>
    <div class="field"><label for="jam_selesai">Jam Selesai *</label><input type="time" id="jam_selesai" name="jam_selesai" value="{{ substr((string)$get('jam_selesai'),0,5) }}" required></div>
</div>
<div class="form-actions"><a class="btn" href="{{ route('admin.class-schedules.index') }}">Batal</a><button class="btn btn-primary" type="submit">Simpan Jadwal</button></div>
</form></div></section>
@endsection
