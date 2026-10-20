@extends('layouts.admin')
@section('title', $title)
@section('page-label', 'Periode Akademik')
@section('content')
@php($get = fn($field, $default = '') => old($field, data_get($record, $field, $default)))
<div class="page-head"><div><div class="eyebrow">Kalender akademik</div><h1>{{ $title }}</h1><p>Pastikan kode semester unik dan tanggal penutupan KRS tidak lebih awal dari tanggal pembukaan.</p></div><div class="actions"><a class="btn" href="{{ route('admin.academic-terms.index') }}">← Kembali</a></div></div>
<section class="card"><div class="panel-body">
<form method="POST" action="{{ $action }}">
    @csrf
    @if($method !== 'POST') @method($method) @endif
    <div class="form-grid">
        <div class="field"><label for="kode_semester">Kode Semester <span class="required">*</span></label><input id="kode_semester" name="kode_semester" maxlength="5" minlength="5" value="{{ $get('kode_semester') }}" @if($record) readonly @endif required></div>
        <div class="field"><label for="nama_semester">Nama Semester <span class="required">*</span></label><input id="nama_semester" name="nama_semester" maxlength="50" value="{{ $get('nama_semester') }}" placeholder="Contoh: Ganjil 2026/2027" required></div>
        <div class="field"><label for="tahun_ajaran">Tahun Ajaran <span class="required">*</span></label><input id="tahun_ajaran" name="tahun_ajaran" maxlength="9" value="{{ $get('tahun_ajaran') }}" placeholder="2026/2027" required></div>
        <div class="field"><label for="jenis_semester">Jenis Semester <span class="required">*</span></label><select id="jenis_semester" name="jenis_semester" required><option value="">Pilih jenis</option>@foreach(['Ganjil','Genap','Antara'] as $item)<option value="{{ $item }}" @selected($get('jenis_semester')===$item)>{{ $item }}</option>@endforeach</select></div>
        <div class="field"><label for="tgl_mulai_krs">Tanggal Mulai KRS <span class="required">*</span></label><input type="date" id="tgl_mulai_krs" name="tgl_mulai_krs" value="{{ $get('tgl_mulai_krs') ? \Illuminate\Support\Carbon::parse($get('tgl_mulai_krs'))->format('Y-m-d') : '' }}" required></div>
        <div class="field"><label for="tgl_selesai_krs">Tanggal Selesai KRS <span class="required">*</span></label><input type="date" id="tgl_selesai_krs" name="tgl_selesai_krs" value="{{ $get('tgl_selesai_krs') ? \Illuminate\Support\Carbon::parse($get('tgl_selesai_krs'))->format('Y-m-d') : '' }}" required></div>
        <div class="field field-full"><label><input type="hidden" name="is_aktif" value="0"><input type="checkbox" name="is_aktif" value="1" @checked((string)$get('is_aktif','0')==='1' || $get('is_aktif') === true)> Jadikan periode ini sebagai semester aktif</label><div class="secondary-text">Mengaktifkan periode ini akan menonaktifkan periode lain yang sebelumnya aktif.</div></div>
    </div>
    <div class="form-actions"><a class="btn" href="{{ route('admin.academic-terms.index') }}">Batal</a><button class="btn btn-primary" type="submit">Simpan Periode</button></div>
</form>
</div></section>
@endsection
