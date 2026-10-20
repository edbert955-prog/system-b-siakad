@extends('layouts.admin')
@section('title', 'Dashboard')
@section('page-label', 'Dashboard')
@section('content')
<div class="page-head">
    <div><div class="eyebrow">Ringkasan akademik</div><h1>Dashboard SIAKADU</h1><p>Ringkasan langsung dari tabel akademik PostgreSQL yang terhubung.</p></div>
    <div class="actions"><a class="btn btn-primary" href="{{ route('admin.students.index') }}">Kelola Mahasiswa</a><a class="btn" href="{{ route('admin.lecturers.index') }}">Kelola Dosen</a><a class="btn" href="{{ route('admin.courses.index') }}">Katalog Mata Kuliah</a></div>
</div>
<div class="stat-grid">
    <div class="stat-card"><div class="stat-top"><span class="stat-label">Total Mahasiswa</span><span class="stat-icon">♙</span></div><div class="stat-value">{{ number_format($stats['students']) }}</div><div class="stat-note">Jumlah pada tabel mahasiswa</div></div>
    <div class="stat-card"><div class="stat-top"><span class="stat-label">Total Dosen</span><span class="stat-icon">♧</span></div><div class="stat-value">{{ number_format($stats['lecturers']) }}</div><div class="stat-note">Jumlah pada tabel dosen</div></div>
    <div class="stat-card"><div class="stat-top"><span class="stat-label">Program Studi</span><span class="stat-icon">▤</span></div><div class="stat-value">{{ number_format($stats['programs']) }}</div><div class="stat-note">Jumlah pada tabel prodi</div></div>
    <div class="stat-card"><div class="stat-top"><span class="stat-label">Mata Kuliah</span><span class="stat-icon">▣</span></div><div class="stat-value">{{ number_format($stats['courses']) }}</div><div class="stat-note">Jumlah mata kuliah terdaftar</div></div>
</div>
<div class="grid-two">
    <section class="card panel">
        <div class="panel-head"><h2>Semester Akademik Aktif</h2>@if($activeTerm)<span class="badge badge-success">Aktif</span>@else<span class="badge badge-warning">Belum diatur</span>@endif</div>
        <div class="panel-body">
            @if($activeTerm)
                <div class="eyebrow">{{ $activeTerm->kode_semester }}</div><div style="font-size:22px;font-weight:800;margin-bottom:4px">{{ $activeTerm->nama_semester }}</div><div class="muted">Tahun ajaran {{ $activeTerm->tahun_ajaran }} · {{ $activeTerm->jenis_semester }}</div>
                <div class="detail-list" style="margin-top:12px"><div class="detail-item"><div class="detail-label">Pembukaan KRS</div><div class="detail-value">{{ $activeTerm->tgl_mulai_krs?->format('d M Y') ?? 'Belum diatur' }}</div></div><div class="detail-item"><div class="detail-label">Penutupan KRS</div><div class="detail-value">{{ $activeTerm->tgl_selesai_krs?->format('d M Y') ?? 'Belum diatur' }}</div></div></div>
            @else
                <div class="empty"><strong>Belum ada semester aktif</strong>Tambahkan atau aktifkan tahun akademik sebelum membuka fitur semester.</div>
            @endif
        </div>
    </section>
    <section class="card panel">
        <div class="panel-head"><h2>Status Operasional</h2><span class="badge badge-info">Dari PostgreSQL</span></div>
        <div class="panel-body">
            <div class="detail-list"><div class="detail-item"><div class="detail-label">KRS Draft / Menunggu</div><div class="detail-value">{{ number_format($stats['pending_krs']) }}</div></div><div class="detail-item"><div class="detail-label">Tagihan UKT Belum Lunas</div><div class="detail-value">{{ number_format($stats['unpaid_ukt']) }}</div></div></div>
            <p class="muted" style="margin:14px 0 0;font-size:12px">Angka mengikuti isi tabel saat halaman dibuka; tidak menggunakan statistik contoh dari desain Stitch.</p>
        </div>
    </section>
</div>
<section class="card panel" style="margin-top:20px">
    <div class="panel-head"><div><h2>Aktivitas KRS Terbaru</h2><div class="secondary-text">Menampilkan catatan KRS terakhir yang tersimpan.</div></div><a class="btn btn-sm" href="{{ url('/api/v1/krs') }}" target="_blank" rel="noopener">Lihat endpoint KRS</a></div>
    <div class="table-wrap"><table class="data-table"><thead><tr><th>Mahasiswa</th><th>Semester</th><th>Tanggal Pengisian</th><th>Status</th></tr></thead><tbody>
    @forelse($recentKrs as $item)
        <tr><td><div class="primary-text">{{ $item->mahasiswa?->nama ?? 'Mahasiswa tidak ditemukan' }}</div><div class="secondary-text">{{ $item->npm }}</div></td><td>{{ $item->tahunAkademik?->nama_semester ?? $item->kode_semester }} {{ $item->tahunAkademik?->tahun_ajaran }}</td><td>{{ $item->tgl_pengisian?->format('d M Y H:i') ?? '—' }}</td><td><span class="badge {{ $item->status_persetujuan_pa === 'Disetujui' ? 'badge-success' : ($item->status_persetujuan_pa === 'Ditolak' ? 'badge-danger' : 'badge-warning') }}">{{ $item->status_persetujuan_pa }}</span></td></tr>
    @empty
        <tr><td colspan="4"><div class="empty"><strong>Belum ada data KRS</strong>Catatan KRS akan muncul di sini setelah tersedia di database.</div></td></tr>
    @endforelse
    </tbody></table></div>
</section>
@endsection
