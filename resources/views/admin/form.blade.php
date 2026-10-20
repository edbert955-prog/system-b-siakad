@extends('layouts.admin')
@section('title', $title)
@section('page-label', $title)
@section('content')
@php
    $get = function ($field, $default = '') use ($record) {
        return old($field, data_get($record, $field, $default));
    };
@endphp
<div class="page-head"><div><div class="eyebrow">Master data / Formulir</div><h1>{{ $title }}</h1><p>Isi data sesuai sumber akademik. Kolom bertanda <span class="required">*</span> wajib diisi.</p></div><div class="actions"><a class="btn" href="{{ url()->previous() }}">← Kembali</a></div></div>
<section class="card"><div class="panel-body">
<form method="POST" action="{{ $action }}">
    @csrf
    @if($method !== 'POST') @method($method) @endif
    <div class="form-grid">
    @if($type === 'student')
        <div class="field"><label for="npm">NPM <span class="required">*</span></label><input id="npm" name="npm" value="{{ $get('npm') }}" maxlength="15" @if($record) readonly style="background:#f1f5f9" @endif required></div>
        <div class="field"><label for="nama">Nama lengkap <span class="required">*</span></label><input id="nama" name="nama" value="{{ $get('nama') }}" maxlength="150" required></div>
        <div class="field"><label for="jenis_kelamin">Jenis kelamin <span class="required">*</span></label><select id="jenis_kelamin" name="jenis_kelamin" required><option value="">Pilih...</option>@foreach(['Laki-laki','Perempuan'] as $g)<option value="{{ $g }}" @selected($get('jenis_kelamin') === $g)>{{ $g }}</option>@endforeach</select></div>
        <div class="field"><label for="angkatan">Angkatan <span class="required">*</span></label><input type="number" id="angkatan" name="angkatan" min="1960" max="2100" value="{{ $get('angkatan', date('Y')) }}" required></div>
        <div class="field"><label for="id_prodi">Program Studi <span class="required">*</span></label><select name="id_prodi" id="id_prodi" required><option value="">Pilih program studi</option>@foreach($programs as $item)<option value="{{ $item->id_prodi }}" @selected((string)$get('id_prodi') === (string)$item->id_prodi)>{{ $item->nama_prodi }}</option>@endforeach</select></div>
        <div class="field"><label for="id_status">Status Akademik <span class="required">*</span></label><select name="id_status" id="id_status" required><option value="">Pilih status</option>@foreach($statuses as $item)<option value="{{ $item->id_status }}" @selected((string)$get('id_status') === (string)$item->id_status)>{{ $item->nama_status }}</option>@endforeach</select></div>
        <div class="field"><label for="id_jalur">Jalur Masuk</label><select name="id_jalur" id="id_jalur"><option value="">Belum diatur</option>@foreach($pathways as $item)<option value="{{ $item->id_jalur }}" @selected((string)$get('id_jalur') === (string)$item->id_jalur)>{{ $item->nama_jalur }}</option>@endforeach</select></div>
        <div class="field"><label for="id_dosen_pa">Dosen PA</label><select name="id_dosen_pa" id="id_dosen_pa"><option value="">Belum ditetapkan</option>@foreach($lecturers as $item)<option value="{{ $item->id_dosen }}" @selected((string)$get('id_dosen_pa') === (string)$item->id_dosen)>{{ $item->nama_dosen }}</option>@endforeach</select></div>
        <div class="field"><label for="tgl_masuk">Tanggal masuk <span class="required">*</span></label><input type="date" id="tgl_masuk" name="tgl_masuk" value="{{ $get('tgl_masuk', date('Y').'-08-01') }}" required></div>
        <div class="field"><label for="tgl_lulus">Tanggal lulus</label><input type="date" id="tgl_lulus" name="tgl_lulus" value="{{ $get('tgl_lulus') }}"></div>
        <div class="field"><label for="email_kampus">Email kampus</label><input type="email" id="email_kampus" name="email_kampus" value="{{ $get('email_kampus') }}" maxlength="120"></div>
        <div class="field"><label for="email_pribadi">Email pribadi</label><input type="email" id="email_pribadi" name="email_pribadi" value="{{ $get('email_pribadi') }}" maxlength="120"></div>
        <div class="field"><label for="no_hp">Nomor HP</label><input id="no_hp" name="no_hp" value="{{ $get('no_hp') }}" maxlength="20"></div>
        <div class="field"><label for="tempat_lahir">Tempat lahir</label><input id="tempat_lahir" name="tempat_lahir" value="{{ $get('tempat_lahir') }}" maxlength="100"></div>
        <div class="field"><label for="tgl_lahir">Tanggal lahir</label><input type="date" id="tgl_lahir" name="tgl_lahir" value="{{ $get('tgl_lahir') ? \Illuminate\Support\Carbon::parse($get('tgl_lahir'))->format('Y-m-d') : '' }}"></div>
        <div class="field"><label for="agama">Agama</label><input id="agama" name="agama" value="{{ $get('agama') }}" maxlength="20"></div>
        <div class="field"><label for="kode_pos">Kode pos</label><input id="kode_pos" name="kode_pos" value="{{ $get('kode_pos') }}" maxlength="10"></div>
        <div class="field field-full"><label for="alamat_domisili">Alamat domisili</label><textarea id="alamat_domisili" name="alamat_domisili" rows="3">{{ $get('alamat_domisili') }}</textarea></div>
        <div class="field"><label for="nik">NIK</label><input id="nik" name="nik" value="{{ $get('nik') }}" maxlength="16"></div>
        <div class="field"><label for="nisn">NISN</label><input id="nisn" name="nisn" value="{{ $get('nisn') }}" maxlength="10"></div>
        <div class="field"><label for="semester_berjalan">Semester berjalan</label><input type="number" id="semester_berjalan" name="semester_berjalan" min="1" max="20" value="{{ $get('semester_berjalan', 1) }}"></div>
        <div class="field"><label for="ipk">IPK</label><input type="number" id="ipk" name="ipk" min="0" max="4" step="0.01" value="{{ $get('ipk', 0) }}"></div>
        <div class="field"><label for="total_sks_lulus">Total SKS lulus</label><input type="number" id="total_sks_lulus" name="total_sks_lulus" min="0" value="{{ $get('total_sks_lulus', 0) }}"></div>
        <div class="field"><label for="golongan_ukt">Golongan UKT</label><input type="number" id="golongan_ukt" name="golongan_ukt" min="1" max="12" value="{{ $get('golongan_ukt') }}"></div>
        <div class="field"><label for="nominal_ukt">Nominal UKT (Rp)</label><input type="number" id="nominal_ukt" name="nominal_ukt" min="0" step="1000" value="{{ $get('nominal_ukt') }}"></div>
        <div class="field field-full"><label for="raw_npm_siakadu">NPM asli sumber SIAKADU (opsional)</label><input id="raw_npm_siakadu" name="raw_npm_siakadu" value="{{ $get('raw_npm_siakadu') }}" maxlength="40"></div>
    @elseif($type === 'lecturer')
        <div class="field"><label for="nidn">NIDN <span class="required">*</span></label><input id="nidn" name="nidn" value="{{ $get('nidn') }}" maxlength="10" minlength="10" required></div>
        <div class="field"><label for="nip">NIP</label><input id="nip" name="nip" value="{{ $get('nip') }}" maxlength="20"></div>
        <div class="field"><label for="nama_dosen">Nama dosen <span class="required">*</span></label><input id="nama_dosen" name="nama_dosen" value="{{ $get('nama_dosen') }}" maxlength="150" required></div>
        <div class="field"><label for="jenis_kelamin">Jenis kelamin <span class="required">*</span></label><select id="jenis_kelamin" name="jenis_kelamin" required><option value="">Pilih...</option>@foreach(['Laki-laki','Perempuan'] as $g)<option value="{{ $g }}" @selected($get('jenis_kelamin') === $g)>{{ $g }}</option>@endforeach</select></div>
        <div class="field"><label for="gelar_depan">Gelar depan</label><input id="gelar_depan" name="gelar_depan" value="{{ $get('gelar_depan') }}" maxlength="30"></div>
        <div class="field"><label for="gelar_belakang">Gelar belakang</label><input id="gelar_belakang" name="gelar_belakang" value="{{ $get('gelar_belakang') }}" maxlength="50"></div>
        <div class="field"><label for="email_dosen">Email dosen</label><input type="email" id="email_dosen" name="email_dosen" value="{{ $get('email_dosen') }}" maxlength="120"></div>
        <div class="field"><label for="no_hp">Nomor HP</label><input id="no_hp" name="no_hp" value="{{ $get('no_hp') }}" maxlength="20"></div>
        <div class="field"><label for="jabatan_fungsional">Jabatan fungsional</label><input id="jabatan_fungsional" name="jabatan_fungsional" value="{{ $get('jabatan_fungsional') }}" maxlength="50"></div>
        <div class="field"><label for="id_prodi_homebase">Program homebase <span class="required">*</span></label><select id="id_prodi_homebase" name="id_prodi_homebase" required><option value="">Pilih program studi</option>@foreach($programs as $item)<option value="{{ $item->id_prodi }}" @selected((string)$get('id_prodi_homebase') === (string)$item->id_prodi)>{{ $item->nama_prodi }}</option>@endforeach</select></div>
    @elseif($type === 'faculty')
        <div class="field"><label for="kode_fakultas">Kode fakultas <span class="required">*</span></label><input id="kode_fakultas" name="kode_fakultas" value="{{ $get('kode_fakultas') }}" maxlength="10" required></div>
        <div class="field"><label for="nama_fakultas">Nama fakultas <span class="required">*</span></label><input id="nama_fakultas" name="nama_fakultas" value="{{ $get('nama_fakultas') }}" maxlength="100" required></div>
    @elseif($type === 'program')
        <div class="field"><label for="kode_prodi">Kode program studi</label><input id="kode_prodi" name="kode_prodi" value="{{ $get('kode_prodi') }}" maxlength="15"></div>
        <div class="field"><label for="nama_prodi">Nama program studi <span class="required">*</span></label><input id="nama_prodi" name="nama_prodi" value="{{ $get('nama_prodi') }}" maxlength="150" required></div>
        <div class="field"><label for="id_fakultas">Fakultas <span class="required">*</span></label><select id="id_fakultas" name="id_fakultas" required><option value="">Pilih fakultas</option>@foreach($faculties as $item)<option value="{{ $item->id_fakultas }}" @selected((string)$get('id_fakultas') === (string)$item->id_fakultas)>{{ $item->nama_fakultas }}</option>@endforeach</select></div>
        <div class="field"><label for="jenjang">Jenjang <span class="required">*</span></label><select id="jenjang" name="jenjang" required><option value="">Pilih jenjang</option>@foreach(['D3','D4','S1','S2','S3','Profesi','Sp-1'] as $level)<option value="{{ $level }}" @selected($get('jenjang') === $level)>{{ $level }}</option>@endforeach</select></div>
        <div class="field"><label for="akreditasi">Akreditasi</label><input id="akreditasi" name="akreditasi" value="{{ $get('akreditasi') }}" maxlength="20" placeholder="Contoh: Baik Sekali"></div>
        <div class="field"><label for="gelar_lulusan">Gelar lulusan</label><input id="gelar_lulusan" name="gelar_lulusan" value="{{ $get('gelar_lulusan') }}" maxlength="30"></div>
        <div class="field"><label for="sks_minimal">SKS minimal lulus <span class="required">*</span></label><input type="number" id="sks_minimal" name="sks_minimal" min="1" value="{{ $get('sks_minimal', 144) }}" required></div>
    @elseif($type === 'course')
        <div class="field"><label for="kode_mk">Kode mata kuliah <span class="required">*</span></label><input id="kode_mk" name="kode_mk" value="{{ $get('kode_mk') }}" maxlength="15" required></div>
        <div class="field"><label for="nama_mk">Nama mata kuliah <span class="required">*</span></label><input id="nama_mk" name="nama_mk" value="{{ $get('nama_mk') }}" maxlength="150" required></div>
        <div class="field"><label for="id_prodi">Program studi <span class="required">*</span></label><select name="id_prodi" id="id_prodi" required><option value="">Pilih program studi</option>@foreach($programs as $item)<option value="{{ $item->id_prodi }}" @selected((string)$get('id_prodi') === (string)$item->id_prodi)>{{ $item->nama_prodi }}</option>@endforeach</select></div>
        <div class="field"><label for="semester_paket">Semester paket <span class="required">*</span></label><select name="semester_paket" id="semester_paket" required><option value="">Pilih semester</option>@for($i=1;$i<=8;$i++)<option value="{{ $i }}" @selected((string)$get('semester_paket') === (string)$i)>Semester {{ $i }}</option>@endfor</select></div>
        <div class="field"><label for="sks_teori">SKS teori <span class="required">*</span></label><input type="number" min="0" id="sks_teori" name="sks_teori" value="{{ $get('sks_teori', 3) }}" required></div>
        <div class="field"><label for="sks_praktikum">SKS praktikum <span class="required">*</span></label><input type="number" min="0" id="sks_praktikum" name="sks_praktikum" value="{{ $get('sks_praktikum', 0) }}" required></div>
        <div class="field"><label for="total_sks">Total SKS <span class="required">*</span></label><input type="number" min="1" id="total_sks" name="total_sks" value="{{ $get('total_sks', 3) }}" required><span class="secondary-text">Harus sama dengan SKS teori + praktikum.</span></div>
        <div class="field"><label for="jenis_mk">Jenis mata kuliah <span class="required">*</span></label><select id="jenis_mk" name="jenis_mk" required><option value="">Pilih jenis</option>@foreach(['Wajib','Pilihan','MKWU'] as $kind)<option value="{{ $kind }}" @selected($get('jenis_mk', 'Wajib') === $kind)>{{ $kind }}</option>@endforeach</select></div>
    @endif
    </div>
    <div class="form-actions"><a class="btn" href="{{ url()->previous() }}">Batal</a><button type="submit" class="btn btn-primary">Simpan Data</button></div>
</form>
</div></section>
@endsection
