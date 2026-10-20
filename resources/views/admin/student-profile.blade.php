@extends('layouts.admin')
@section('title', 'Profil Mahasiswa')
@section('page-label', 'Profil Mahasiswa')
@section('content')
<div class="page-head"><div><div class="eyebrow">Data akademik / Mahasiswa</div><h1>Profil Mahasiswa</h1><p>Informasi yang ditampilkan berasal dari data mahasiswa dan relasi akademik yang tersedia.</p></div><div class="actions"><a class="btn" href="{{ route('admin.students.index') }}">← Kembali</a><a class="btn btn-primary" href="{{ route('admin.students.edit', $mahasiswa->npm) }}">Edit Data</a></div></div>
<section class="card panel"><div class="panel-body"><div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap"><div class="user-avatar" style="width:58px;height:58px;font-size:24px;background:#dbeafe;color:#1e40af">{{ strtoupper(substr($mahasiswa->nama,0,1)) }}</div><div style="flex:1;min-width:220px"><h2 style="font-size:22px;margin:0 0 4px">{{ $mahasiswa->nama }}</h2><div class="muted">NPM {{ $mahasiswa->npm }} · Angkatan {{ $mahasiswa->angkatan }}</div><div style="margin-top:8px"><span class="badge {{ $mahasiswa->statusMahasiswa?->is_aktif_kuliah ? 'badge-success' : 'badge-warning' }}">{{ $mahasiswa->statusMahasiswa?->nama_status ?? 'Status tidak diketahui' }}</span></div></div></div></div></section>
<div class="grid-two">
<section class="card panel"><div class="panel-head"><h2>Informasi Akademik</h2></div><div class="panel-body"><div class="detail-list">
<div class="detail-item"><div class="detail-label">Program Studi</div><div class="detail-value">{{ $mahasiswa->prodi?->nama_prodi ?? '—' }}</div></div>
<div class="detail-item"><div class="detail-label">Fakultas</div><div class="detail-value">{{ $mahasiswa->prodi?->fakultas?->nama_fakultas ?? '—' }}</div></div>
<div class="detail-item"><div class="detail-label">Jenjang</div><div class="detail-value">{{ $mahasiswa->prodi?->jenjang ?? '—' }}</div></div>
<div class="detail-item"><div class="detail-label">Dosen PA</div><div class="detail-value">{{ $mahasiswa->dosenPa?->nama_dosen ?? 'Belum ditetapkan' }}</div></div>
<div class="detail-item"><div class="detail-label">Semester Berjalan</div><div class="detail-value">{{ $mahasiswa->semester_berjalan ?? '—' }}</div></div>
<div class="detail-item"><div class="detail-label">Tanggal Masuk</div><div class="detail-value">{{ $mahasiswa->tgl_masuk?->format('d M Y') ?? '—' }}</div></div>
<div class="detail-item"><div class="detail-label">IPK</div><div class="detail-value">{{ number_format((float)$mahasiswa->ipk,2) }}</div></div>
<div class="detail-item"><div class="detail-label">Total SKS Lulus</div><div class="detail-value">{{ $mahasiswa->total_sks_lulus ?? 0 }} SKS</div></div>
<div class="detail-item"><div class="detail-label">Jalur Masuk</div><div class="detail-value">{{ $mahasiswa->jalurMasuk?->nama_jalur ?? '—' }}</div></div>
<div class="detail-item"><div class="detail-label">Nominal UKT pada profil</div><div class="detail-value">{{ $mahasiswa->nominal_ukt !== null ? 'Rp '.number_format((float)$mahasiswa->nominal_ukt,0,',','.') : '—' }}</div></div>
</div></div></section>
<section class="card panel"><div class="panel-head"><h2>Informasi Kontak</h2></div><div class="panel-body"><div class="detail-list">
<div class="detail-item"><div class="detail-label">Email Kampus</div><div class="detail-value">{{ $mahasiswa->email_kampus ?: '—' }}</div></div>
<div class="detail-item"><div class="detail-label">Email Pribadi</div><div class="detail-value">{{ $mahasiswa->email_pribadi ?: '—' }}</div></div>
<div class="detail-item"><div class="detail-label">Nomor HP</div><div class="detail-value">{{ $mahasiswa->no_hp ?: '—' }}</div></div>
<div class="detail-item"><div class="detail-label">Tempat, Tanggal Lahir</div><div class="detail-value">{{ $mahasiswa->tempat_lahir ?: '—' }}{{ $mahasiswa->tgl_lahir ? ', '.$mahasiswa->tgl_lahir->format('d M Y') : '' }}</div></div>
<div class="detail-item"><div class="detail-label">Agama</div><div class="detail-value">{{ $mahasiswa->agama ?: '—' }}</div></div>
<div class="detail-item"><div class="detail-label">Kode Pos</div><div class="detail-value">{{ $mahasiswa->kode_pos ?: '—' }}</div></div>
</div><p class="muted" style="font-size:11px;margin:12px 0 0">NIK dan alamat lengkap tidak ditampilkan pada ringkasan profil ini untuk mengurangi paparan data sensitif.</p></div></section>
</div>
<section class="card panel"><div class="panel-head"><div><h2>Riwayat Semester</h2><div class="secondary-text">Status, IPS, dan SKS diambil jika tersedia.</div></div></div><div class="table-wrap"><table class="data-table"><thead><tr><th>Semester</th><th>Status</th><th>IPS</th><th>SKS Diambil</th></tr></thead><tbody>
@forelse($mahasiswa->riwayatStatusSemester->sortByDesc('kode_semester') as $history)<tr><td>{{ $history->kode_semester }}</td><td>{{ $history->statusMahasiswa?->nama_status ?? '—' }}</td><td>{{ $history->ips !== null ? number_format((float)$history->ips,2) : '—' }}</td><td>{{ $history->sks_diambil ?? '—' }}</td></tr>@empty<tr><td colspan="4"><div class="empty"><strong>Belum ada riwayat semester</strong>Data akan muncul setelah riwayat tersedia di database.</div></td></tr>@endforelse
</tbody></table></div></section>
<div class="grid-two"><section class="card panel"><div class="panel-head"><h2>Riwayat KRS</h2></div><div class="table-wrap"><table class="data-table"><thead><tr><th>Semester</th><th>Tanggal</th><th>Status</th></tr></thead><tbody>@forelse($mahasiswa->krs->sortByDesc('kode_semester') as $item)<tr><td>{{ $item->tahunAkademik?->nama_semester ?? $item->kode_semester }}</td><td>{{ $item->tgl_pengisian?->format('d M Y') ?? '—' }}</td><td><span class="badge {{ $item->status_persetujuan_pa === 'Disetujui' ? 'badge-success' : 'badge-warning' }}">{{ $item->status_persetujuan_pa }}</span></td></tr>@empty<tr><td colspan="3" class="muted" style="padding:18px">Belum ada data KRS.</td></tr>@endforelse</tbody></table></div></section>
<section class="card panel"><div class="panel-head"><h2>Riwayat Pembayaran UKT</h2></div><div class="table-wrap"><table class="data-table"><thead><tr><th>Semester</th><th>Tagihan</th><th>Status</th><th>Tanggal Bayar</th></tr></thead><tbody>@forelse($mahasiswa->pembayaranUkt->sortByDesc('kode_semester') as $payment)<tr><td>{{ $payment->tahunAkademik?->nama_semester ?? $payment->kode_semester }}</td><td>Rp {{ number_format((float)$payment->nominal_tagihan,0,',','.') }}</td><td><span class="badge {{ $payment->status_bayar === 'Lunas' ? 'badge-success' : 'badge-warning' }}">{{ $payment->status_bayar }}</span></td><td>{{ $payment->tgl_bayar?->format('d M Y') ?? '—' }}</td></tr>@empty<tr><td colspan="4" class="muted" style="padding:18px">Belum ada data pembayaran UKT.</td></tr>@endforelse</tbody></table></div></section></div>
@endsection
