@extends('layouts.student')
@section('title', 'Profil Akademik')
@section('page-label', 'Profil Akademik')

@section('content')
<div class="page-head">
    <div><div class="eyebrow">Informasi pribadi dan akademik</div><h1>Profil Mahasiswa</h1><p>Data akademik bersumber dari tabel mahasiswa. Jika ada informasi resmi yang keliru, hubungi staf akademik; perubahan profil akun tersedia pada Pengaturan Akun.</p></div>
    <div class="actions"><a class="btn btn-primary" href="{{ route('student.account-settings') }}">Pengaturan Akun</a></div>
</div>

<section class="card panel">
    <div class="profile-header">
        <div class="profile-avatar">{{ strtoupper(substr($student->nama,0,1)) }}</div>
        <div style="min-width:0;flex:1"><h2 style="margin:0 0 5px;font-size:20px">{{ $student->nama }}</h2><div class="secondary-text">{{ $student->npm }} · Angkatan {{ $student->angkatan }}</div><div style="margin-top:9px"><span class="badge {{ $student->statusMahasiswa?->is_aktif_kuliah ? 'badge-success' : 'badge-warning' }}">{{ $student->statusMahasiswa?->nama_status ?? 'Status belum tersedia' }}</span></div></div>
    </div>
    <div class="panel-body">
        <h3 class="account-section-title">Identitas Akademik</h3>
        <div class="student-info-grid">
            <div class="student-info-item"><div class="student-info-label">NPM</div><div class="student-info-value">{{ $student->npm }}</div></div>
            <div class="student-info-item"><div class="student-info-label">Program Studi</div><div class="student-info-value">{{ $student->prodi?->nama_prodi ?? '—' }}</div></div>
            <div class="student-info-item"><div class="student-info-label">Fakultas</div><div class="student-info-value">{{ $student->prodi?->fakultas?->nama_fakultas ?? '—' }}</div></div>
            <div class="student-info-item"><div class="student-info-label">Jenjang</div><div class="student-info-value">{{ $student->prodi?->jenjang ?? '—' }}</div></div>
            <div class="student-info-item"><div class="student-info-label">Angkatan</div><div class="student-info-value">{{ $student->angkatan }}</div></div>
            <div class="student-info-item"><div class="student-info-label">Tanggal Masuk</div><div class="student-info-value">{{ $student->tgl_masuk?->format('d M Y') ?? '—' }}</div></div>
            <div class="student-info-item"><div class="student-info-label">Semester Berjalan</div><div class="student-info-value">{{ $student->semester_berjalan ?? '—' }}</div></div>
            <div class="student-info-item"><div class="student-info-label">Dosen Pembimbing Akademik</div><div class="student-info-value">{{ $student->dosenPa?->nama_dosen ?? 'Belum ditetapkan' }}</div></div>
        </div>

        <hr class="account-section-divider">
        <h3 class="account-section-title">Kontak</h3>
        <div class="student-info-grid">
            <div class="student-info-item"><div class="student-info-label">Email Kampus</div><div class="student-info-value">{{ $student->email_kampus ?? '—' }}</div></div>
            <div class="student-info-item"><div class="student-info-label">Email Pribadi</div><div class="student-info-value">{{ $student->email_pribadi ?? '—' }}</div></div>
            <div class="student-info-item"><div class="student-info-label">Nomor HP Akademik</div><div class="student-info-value">{{ $student->no_hp ?? '—' }}</div></div>
            <div class="student-info-item"><div class="student-info-label">Alamat Domisili</div><div class="student-info-value">{{ $student->alamat_domisili ?? '—' }}</div></div>
        </div>

        <hr class="account-section-divider">
        <h3 class="account-section-title">Ringkasan Akademik</h3>
        <div class="student-stat-grid" style="margin:15px 0 0;grid-template-columns:repeat(3,minmax(0,1fr))">
            <div class="stat-card"><div class="stat-label">IPK</div><div class="stat-value">{{ number_format((float)$student->ipk,2) }}</div></div>
            <div class="stat-card"><div class="stat-label">SKS Lulus</div><div class="stat-value">{{ (int)$student->total_sks_lulus }}</div></div>
            <div class="stat-card"><div class="stat-label">Jalur Masuk</div><div class="stat-value" style="font-size:17px">{{ $student->jalurMasuk?->nama_jalur ?? '—' }}</div></div>
        </div>
    </div>
</section>
@endsection
