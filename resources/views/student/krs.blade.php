@extends('layouts.student')
@section('title', 'Rencana Studi (KRS)')
@section('page-label', 'Rencana Studi (KRS)')

@section('content')
<div class="page-head">
    <div><div class="eyebrow">Registrasi akademik</div><h1>Rencana Studi (KRS)</h1><p>Pilih kelas untuk semester aktif, periksa bentrok jadwal, kemudian ajukan KRS untuk diperiksa oleh Dosen Pembimbing Akademik.</p></div>
</div>

<div class="status-banner {{ $termOpen ? 'success' : 'warning' }}">
    @if($term)
        <strong>{{ $term->nama_semester }} · {{ $term->tahun_ajaran }}</strong><br>
        Periode KRS: {{ $term->tgl_mulai_krs?->format('d M Y') }} sampai {{ $term->tgl_selesai_krs?->format('d M Y') }}.
        @if($termOpen) Pengisian KRS sedang dibuka. @else Pengisian KRS di luar rentang tanggal tersebut sedang ditutup. @endif
    @else
        <strong>Semester aktif belum diatur.</strong> Hubungi administrator untuk menentukan periode akademik aktif.
    @endif
</div>

<div class="student-stat-grid" style="grid-template-columns:repeat(3,minmax(0,1fr))">
    <div class="stat-card"><div class="stat-label">Status KRS</div><div class="stat-value" style="font-size:19px">{{ $krs?->status_persetujuan_pa ?? 'Belum dibuat' }}</div><div class="stat-note">Untuk semester aktif</div></div>
    <div class="stat-card"><div class="stat-label">Mata Kuliah Dipilih</div><div class="stat-value">{{ $selectedDetails->count() }}</div><div class="stat-note">Kelas di KRS saat ini</div></div>
    <div class="stat-card"><div class="stat-label">Total SKS</div><div class="stat-value">{{ $totalSks }}</div><div class="stat-note">Batas prototipe 24 SKS</div></div>
</div>

@if($krs?->catatan_pa)
    <div class="status-banner warning"><strong>Catatan Dosen PA:</strong><br>{{ $krs->catatan_pa }}</div>
@endif

<div class="course-picker-grid">
    <section class="card panel">
        <div class="panel-head"><div><h2>Kelas tersedia</h2><div class="secondary-text">Daftar kelas pada semester aktif saja</div></div></div>
        <div class="panel-body">
            <form method="GET" action="{{ route('student.krs') }}" class="portal-filter" style="padding:0;border:0;box-shadow:none;margin-bottom:15px">
                <div class="field field-grow"><label for="search">Cari mata kuliah</label><input id="search" name="search" value="{{ request('search') }}" placeholder="Kode atau nama mata kuliah"></div>
                <div class="field field-small"><label for="semester_paket">Semester kurikulum</label><select name="semester_paket" id="semester_paket"><option value="">Semua</option>@for($i=1;$i<=8;$i++)<option value="{{ $i }}" @selected((string)request('semester_paket') === (string)$i)>Semester {{ $i }}</option>@endfor</select></div>
                <button class="btn btn-primary" type="submit">Cari</button>
            </form>
            @if($availableClasses->isEmpty())
                <div class="empty"><strong>Tidak ada kelas tersedia</strong>Pastikan semester aktif memiliki kelas yang telah dijadwalkan dan belum dipilih.</div>
            @else
                <div class="course-list">
                    @foreach($availableClasses as $class)
                        @php($count = $class->krsDetails()->whereHas('krs', fn($q) => $q->whereIn('status_persetujuan_pa', ['Diajukan','Disetujui']))->count())
                        <div class="course-option">
                            <div class="course-option-main">
                                <div class="course-option-title">{{ $class->mataKuliah?->nama_mk ?? 'Mata kuliah' }} <span class="badge badge-info">{{ $class->mataKuliah?->kode_mk }}</span></div>
                                <div class="course-meta"><span>{{ $class->mataKuliah?->total_sks }} SKS</span><span>Kelas {{ $class->nama_kelas }}</span><span>{{ $class->hari }}, {{ substr((string)$class->jam_mulai,0,5) }}–{{ substr((string)$class->jam_selesai,0,5) }}</span><span>{{ $class->dosenPengampu?->nama_dosen ?? 'Dosen belum ditetapkan' }}</span><span>{{ $class->ruangan?->kode_ruangan ?? 'Ruangan belum ditetapkan' }}</span><span>Kuota {{ $count }}/{{ $class->kuota }}</span></div>
                            </div>
                            <div class="course-option-actions">
                                @if($editable && $termOpen)
                                    <form method="POST" action="{{ route('student.krs.add') }}">
                                        @csrf<input type="hidden" name="id_kelas" value="{{ $class->id_kelas }}">
                                        <button class="btn btn-sm btn-primary" type="submit">＋ Tambah</button>
                                    </form>
                                @else
                                    <span class="badge">Tidak dapat diubah</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <section class="card panel">
        <div class="panel-head"><div><h2>Kelas dipilih</h2><div class="secondary-text">Periksa kembali sebelum mengajukan KRS</div></div></div>
        <div class="panel-body">
            @if($selectedDetails->isEmpty())
                <div class="empty"><strong>Belum ada kelas dipilih</strong>Tambahkan kelas dari daftar tersedia selama periode KRS dibuka.</div>
            @else
                <div class="course-list">
                    @foreach($selectedDetails as $detail)
                        @php($class = $detail->kelasKuliah)
                        <div class="course-option">
                            <div class="course-option-main">
                                <div class="course-option-title">{{ $class?->mataKuliah?->nama_mk ?? 'Mata kuliah' }}</div>
                                <div class="course-meta"><span>{{ $class?->mataKuliah?->kode_mk }}</span><span>{{ $class?->mataKuliah?->total_sks }} SKS</span><span>{{ $class?->hari }} {{ substr((string)($class?->jam_mulai ?? ''),0,5) }}–{{ substr((string)($class?->jam_selesai ?? ''),0,5) }}</span></div>
                            </div>
                            @if($editable && $termOpen)
                                <form method="POST" action="{{ route('student.krs.remove', $detail->id_krs_detail) }}" onsubmit="return confirm('Hapus kelas ini dari KRS?')">
                                    @csrf @method('DELETE')<button class="btn btn-sm btn-danger" type="submit">Hapus</button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
                @if($editable && $termOpen)
                    <form method="POST" action="{{ route('student.krs.submit') }}" style="margin-top:18px" onsubmit="return confirm('Ajukan KRS kepada Dosen PA? Setelah diajukan, kelas tidak dapat diubah sampai statusnya diperbarui.')">
                        @csrf<button class="btn btn-primary" type="submit" style="width:100%">Ajukan KRS</button>
                    </form>
                @elseif($krs)
                    <div class="status-banner" style="margin-top:16px">KRS berstatus <strong>{{ $krs->status_persetujuan_pa }}</strong> dan sedang dalam mode baca saja.</div>
                @endif
            @endif
        </div>
    </section>
</div>
@endsection
