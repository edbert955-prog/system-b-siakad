@extends('layouts.student')
@section('title', 'Hasil Studi (KHS)')
@section('page-label', 'Hasil Studi (KHS)')

@section('content')
<div class="page-head">
    <div><div class="eyebrow">Hasil pembelajaran</div><h1>Kartu Hasil Studi (KHS)</h1><p>Lihat nilai semester dan riwayat transkrip yang tersimpan atas NPM milikmu.</p></div>
    <div class="actions no-print"><button type="button" class="btn" onclick="window.print()">Cetak / Simpan PDF</button></div>
</div>

<form class="portal-filter no-print" method="GET" action="{{ route('student.khs') }}">
    <div class="field field-grow"><label for="kode_semester">Pilih semester</label><select id="kode_semester" name="kode_semester"><option value="">Semester terbaru</option>@foreach($allKrs as $item)<option value="{{ $item->kode_semester }}" @selected($selectedTerm === $item->kode_semester)>{{ $item->tahunAkademik?->nama_semester ?? $item->kode_semester }} · {{ $item->tahunAkademik?->tahun_ajaran }}</option>@endforeach</select></div>
    <button type="submit" class="btn btn-primary">Tampilkan</button>
    <a class="btn" href="{{ route('student.khs') }}">Reset</a>
</form>

<div class="student-hero">
    <div><div class="eyebrow">Ringkasan akademik</div><h1 style="font-size:22px">{{ $student->nama }}</h1><p>{{ $student->npm }} · {{ $student->prodi?->nama_prodi ?? 'Program studi belum tersedia' }}</p><div class="student-id-pill">IPK kumulatif {{ number_format((float)$student->ipk,2) }} · {{ (int)$student->total_sks_lulus }} SKS lulus</div></div>
    <div><div class="stat-label">Semester dipilih</div><div style="font-weight:800;font-size:17px;margin-top:5px">{{ $selectedKrs?->tahunAkademik?->nama_semester ?? 'Belum tersedia' }}</div><div class="secondary-text">{{ $selectedKrs?->tahunAkademik?->tahun_ajaran }}</div></div>
</div>

<div class="student-stat-grid" style="grid-template-columns:repeat(3,minmax(0,1fr))">
    <div class="stat-card"><div class="stat-label">IPS Semester</div><div class="stat-value">{{ $ips === null ? '—' : number_format($ips,2) }}</div><div class="stat-note">Dihitung dari nilai lengkap</div></div>
    <div class="stat-card"><div class="stat-label">SKS Dinilai</div><div class="stat-value">{{ $totalSks }}</div><div class="stat-note">Semester terpilih</div></div>
    <div class="stat-card"><div class="stat-label">Status KRS</div><div class="stat-value" style="font-size:18px">{{ $selectedKrs?->status_persetujuan_pa ?? 'Belum ada' }}</div><div class="stat-note">Status akademik periode ini</div></div>
</div>

<section class="card panel">
    <div class="panel-head"><div><h2>Nilai Semester</h2><div class="secondary-text">{{ $selectedKrs?->tahunAkademik?->nama_semester ?? $selectedKrs?->kode_semester ?? 'Belum ada data' }}</div></div><span class="badge badge-info">{{ $grades->count() }} mata kuliah</span></div>
    <div class="table-wrap"><table class="data-table"><thead><tr><th>Kode MK</th><th>Mata Kuliah</th><th>SKS</th><th>Nilai Angka</th><th>Huruf</th><th>Bobot Mutu</th><th>Dosen</th></tr></thead><tbody>
        @forelse($grades as $detail)
            <tr><td>{{ $detail->kelasKuliah?->mataKuliah?->kode_mk ?? '—' }}</td><td><span class="primary-text">{{ $detail->kelasKuliah?->mataKuliah?->nama_mk ?? 'Mata kuliah tidak tersedia' }}</span></td><td>{{ $detail->kelasKuliah?->mataKuliah?->total_sks ?? 0 }}</td><td>{{ $detail->nilai_angka ?? 'Belum dinilai' }}</td><td>{{ $detail->nilai_huruf ?? '—' }}</td><td>{{ $detail->bobot_mutu ?? '—' }}</td><td>{{ $detail->kelasKuliah?->dosenPengampu?->nama_dosen ?? '—' }}</td></tr>
        @empty
            <tr><td colspan="7"><div class="empty"><strong>Nilai belum tersedia</strong>Belum ada rincian KRS atau nilai yang bisa ditampilkan untuk semester ini.</div></td></tr>
        @endforelse
    </tbody></table></div>
</section>

<section class="card panel" id="transcript">
    <div class="panel-head">
        <div>
            <h2>Transkrip Nilai</h2>
            <div class="secondary-text">
                Gabungan rincian nilai dari seluruh periode KRS yang tersedia.
            </div>
        </div>

        <button
            class="btn btn-sm no-print"
            type="button"
            onclick="window.print()"
        >
            Cetak / Simpan PDF
        </button>
    </div>

    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Semester</th>
                    <th>Kode</th>
                    <th>Mata Kuliah</th>
                    <th>SKS</th>
                    <th>Nilai</th>
                    <th>Bobot</th>
                </tr>
            </thead>

            <tbody>
                @forelse($transcript as $detail)
                    <tr>
                        <td>
                            {{ $detail->academicTerm?->nama_semester ?? '—' }}

                            <div class="secondary-text">
                                {{ $detail->academicTerm?->tahun_ajaran }}
                            </div>
                        </td>

                        <td>
                            {{ $detail->kelasKuliah?->mataKuliah?->kode_mk ?? '—' }}
                        </td>

                        <td>
                            {{ $detail->kelasKuliah?->mataKuliah?->nama_mk ?? '—' }}
                        </td>

                        <td>
                            {{ $detail->kelasKuliah?->mataKuliah?->total_sks ?? 0 }}
                        </td>

                        <td>
                            {{ $detail->nilai_huruf ?? 'Belum dinilai' }}
                        </td>

                        <td>
                            {{ $detail->bobot_mutu ?? '—' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty">
                                <strong>Transkrip belum tersedia</strong>
                                Nilai akan muncul setelah data nilai tersimpan.
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
