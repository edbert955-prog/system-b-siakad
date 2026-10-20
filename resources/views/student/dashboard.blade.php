@extends('layouts.student')
@section('title', 'Dashboard Mahasiswa')
@section('page-label', 'Dashboard')

@section('content')
<div class="student-hero">
    <div>
        <div class="eyebrow">Portal mahasiswa</div>
        <h1>Selamat datang, {{ $student->nama }}.</h1>
        <p>Berikut ringkasan status akademik, rencana studi, jadwal kuliah, dan pembayaran UKT milikmu.</p>
        <div class="student-id-pill">{{ $student->npm }} <span>·</span> {{ $student->prodi?->nama_prodi ?? 'Program studi belum tersedia' }}</div>
    </div>
    <div class="actions">
        <a class="btn btn-primary" href="{{ route('student.krs') }}">Buka KRS</a>
        <a class="btn" href="{{ route('student.profile') }}">Profil Akademik</a>
    </div>
</div>

<div class="student-stat-grid">
    <div class="stat-card">
        <div class="stat-label">IPK Kumulatif</div>
        <div class="stat-value">{{ number_format((float)$student->ipk, 2) }}</div>
        <div class="stat-note">Data tersimpan pada sistem akademik</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total SKS Lulus</div>
        <div class="stat-value">{{ number_format((int)$student->total_sks_lulus) }}</div>
        <div class="stat-note">SKS yang tercatat sudah lulus</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Semester Berjalan</div>
        <div class="stat-value">{{ $student->semester_berjalan ?? '—' }}</div>
        <div class="stat-note">Berdasarkan data mahasiswa</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Status Akademik</div>
        <div class="stat-value" style="font-size:20px">{{ $student->statusMahasiswa?->nama_status ?? '—' }}</div>
        <div class="stat-note">Status saat ini</div>
    </div>
</div>

<div class="student-content-grid">
    <section class="card panel">
        <div class="panel-head">
            <div><h2>Rencana Studi Semester Aktif</h2><div class="secondary-text">{{ $term?->nama_semester ?? 'Semester aktif belum diatur' }} {{ $term?->tahun_ajaran }}</div></div>
            @if($currentKrs)
                <span class="badge {{ $currentKrs->status_persetujuan_pa === 'Disetujui' ? 'badge-success' : ($currentKrs->status_persetujuan_pa === 'Ditolak' ? 'badge-danger' : 'badge-warning') }}">{{ $currentKrs->status_persetujuan_pa }}</span>
            @else
                <span class="badge">Belum ada KRS</span>
            @endif
        </div>
        <div class="panel-body">
            @if($currentKrs)
                @php($krsCredits = $currentKrs->details->sum(fn($detail) => (int)($detail->kelasKuliah?->mataKuliah?->total_sks ?? 0)))
                <div class="student-info-grid" style="margin-bottom:16px">
                    <div class="student-info-item"><div class="student-info-label">Jumlah Mata Kuliah</div><div class="student-info-value">{{ $currentKrs->details->count() }}</div></div>
                    <div class="student-info-item"><div class="student-info-label">Total SKS</div><div class="student-info-value">{{ $krsCredits }} SKS</div></div>
                </div>
                @if($currentKrs->catatan_pa)
                    <div class="status-banner warning"><strong>Catatan Dosen PA:</strong><br>{{ $currentKrs->catatan_pa }}</div>
                @endif
                <a href="{{ route('student.krs') }}" class="btn btn-primary">Lihat Rincian KRS</a>
            @else
                <div class="empty"><strong>Belum ada KRS untuk periode aktif</strong>Jika periode pengisian dibuka, kamu dapat memilih mata kuliah melalui halaman KRS.</div>
                <a href="{{ route('student.krs') }}" class="btn btn-primary">Buka Registrasi KRS</a>
            @endif
        </div>
    </section>

    <section class="card panel">
        <div class="panel-head"><div><h2>Status UKT Terbaru</h2><div class="secondary-text">Tagihan berdasarkan database SIAKADU</div></div></div>
        <div class="panel-body">
            @if($latestPayment)
                <div class="student-info-item"><div class="student-info-label">Periode</div><div class="student-info-value">{{ $latestPayment->tahunAkademik?->nama_semester ?? $latestPayment->kode_semester }} {{ $latestPayment->tahunAkademik?->tahun_ajaran }}</div></div>
                <div class="student-info-item"><div class="student-info-label">Nominal Tagihan</div><div class="student-info-value">Rp {{ number_format((float)$latestPayment->nominal_tagihan, 0, ',', '.') }}</div></div>
                <div class="student-info-item"><div class="student-info-label">Status</div><div class="student-info-value"><span class="badge {{ $latestPayment->status_bayar === 'Lunas' ? 'badge-success' : ($latestPayment->status_bayar === 'Dispensasi' ? 'badge-info' : 'badge-warning') }}">{{ $latestPayment->status_bayar }}</span></div></div>
                <div class="form-actions"><a class="btn btn-primary" href="{{ route('student.ukt-payments') }}">Lihat Riwayat UKT</a></div>
            @else
                <div class="empty"><strong>Belum ada data pembayaran</strong>Data UKT akan muncul setelah tagihan untuk NPM ini tersedia.</div>
            @endif
        </div>
    </section>
</div>

<section class="card panel" style="margin-top:20px">
    <div class="panel-head"><div><h2>Jadwal Kuliah Semester Aktif</h2><div class="secondary-text">Hanya jadwal dari KRS yang telah disetujui</div></div><a class="btn btn-sm" href="{{ route('student.schedule') }}">Lihat Semua Jadwal</a></div>
    <div class="panel-body">
        @if($schedule->isNotEmpty())
            <div class="schedule-list">
                @foreach($schedule as $detail)
                    @php($class = $detail->kelasKuliah)
                    <div class="schedule-row">
                        <div class="schedule-time">{{ $class->hari }}<br>{{ substr((string)$class->jam_mulai,0,5) }}–{{ substr((string)$class->jam_selesai,0,5) }}</div>
                        <div><div class="schedule-course">{{ $class->mataKuliah?->nama_mk ?? 'Mata Kuliah' }}</div><div class="schedule-details"><span>{{ $class->mataKuliah?->kode_mk }}</span><span>{{ $class->mataKuliah?->total_sks }} SKS</span><span>Kelas {{ $class->nama_kelas }}</span><span>{{ $class->dosenPengampu?->nama_dosen ?? 'Dosen belum ditetapkan' }}</span><span>{{ $class->ruangan?->kode_ruangan ?? 'Ruangan belum ditetapkan' }}</span></div></div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty"><strong>Jadwal belum tersedia</strong>Jadwal akan muncul ketika KRS semester aktif berstatus Disetujui dan kelas sudah dipilih.</div>
        @endif
    </div>
</section>

<section class="card panel" style="margin-top:20px">
    <div class="panel-head"><div><h2>Riwayat Rencana Studi</h2><div class="secondary-text">Periode KRS terakhir yang tersimpan</div></div></div>
    <div class="table-wrap">
        <table class="data-table"><thead><tr><th>Periode</th><th>Tanggal Pengisian</th><th>Status</th><th>Catatan PA</th></tr></thead><tbody>
            @forelse($recentKrs as $item)
                <tr><td>{{ $item->tahunAkademik?->nama_semester ?? $item->kode_semester }}<div class="secondary-text">{{ $item->tahunAkademik?->tahun_ajaran }}</div></td><td>{{ $item->tgl_pengisian?->format('d M Y') ?? '—' }}</td><td><span class="badge {{ $item->status_persetujuan_pa === 'Disetujui' ? 'badge-success' : ($item->status_persetujuan_pa === 'Ditolak' ? 'badge-danger' : 'badge-warning') }}">{{ $item->status_persetujuan_pa }}</span></td><td>{{ $item->catatan_pa ?: '—' }}</td></tr>
            @empty
                <tr><td colspan="4"><div class="empty"><strong>Belum ada riwayat KRS</strong></div></td></tr>
            @endforelse
        </tbody></table>
    </div>
</section>
@endsection
