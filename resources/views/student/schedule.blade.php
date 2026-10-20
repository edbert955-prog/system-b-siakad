@extends('layouts.student')
@section('title', 'Jadwal Kuliah')
@section('page-label', 'Jadwal Kuliah')

@section('content')
<div class="page-head">
    <div><div class="eyebrow">Perkuliahan saya</div><h1>Jadwal Kuliah</h1><p>Jadwal berikut bersumber dari KRS yang telah disetujui untuk semester terpilih.</p></div>
</div>

<form class="portal-filter" method="GET" action="{{ route('student.schedule') }}">
    <div class="field field-grow"><label for="kode_semester">Periode Akademik</label><select name="kode_semester" id="kode_semester"><option value="">Pilih periode</option>@foreach($studentTerms as $term)<option value="{{ $term->kode_semester }}" @selected($selectedTerm === $term->kode_semester)>{{ $term->nama_semester }} · {{ $term->tahun_ajaran }}</option>@endforeach</select></div>
    <button class="btn btn-primary" type="submit">Tampilkan</button>
</form>

<div class="student-hero">
    <div><div class="eyebrow">Jadwal pribadi</div><h1 style="font-size:22px">{{ $student->nama }}</h1><p>{{ $student->npm }} · {{ $krs?->tahunAkademik?->nama_semester ?? 'Periode belum memiliki KRS disetujui' }}</p></div>
    <div class="actions"><a class="btn btn-primary" href="{{ route('student.krs') }}">Lihat KRS</a></div>
</div>

<section class="card panel">
    <div class="panel-head"><div><h2>Daftar Jadwal</h2><div class="secondary-text">{{ $classes->count() }} kelas pada semester terpilih</div></div></div>
    <div class="panel-body">
        @if($classes->isNotEmpty())
            <div class="schedule-list">
                @foreach($classes as $detail)
                    @php($class = $detail->kelasKuliah)
                    <div class="schedule-row">
                        <div class="schedule-time">{{ $class->hari }}<br>{{ substr((string)$class->jam_mulai,0,5) }}–{{ substr((string)$class->jam_selesai,0,5) }}</div>
                        <div>
                            <div class="schedule-course">{{ $class->mataKuliah?->nama_mk ?? 'Mata kuliah' }}</div>
                            <div class="schedule-details"><span>{{ $class->mataKuliah?->kode_mk }}</span><span>{{ $class->mataKuliah?->total_sks }} SKS</span><span>Kelas {{ $class->nama_kelas }}</span><span>{{ $class->dosenPengampu?->nama_dosen ?? 'Dosen belum ditetapkan' }}</span><span>{{ $class->ruangan?->kode_ruangan ?? 'Ruangan belum ditetapkan' }}{{ $class->ruangan?->nama_gedung ? ' · '.$class->ruangan->nama_gedung : '' }}</span></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty"><strong>Jadwal belum tersedia</strong>Pastikan kamu memiliki KRS berstatus Disetujui dan kelasnya sudah memiliki jadwal.</div>
        @endif
    </div>
</section>
@endsection
