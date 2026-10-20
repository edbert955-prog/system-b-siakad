@extends('layouts.admin')
@section('title', 'Pembayaran UKT')
@section('page-label', 'Pembayaran UKT')
@section('content')
<div class="page-head"><div><div class="eyebrow">Keuangan akademik</div><h1>Manajemen Pembayaran UKT</h1><p>Kelola catatan tagihan per mahasiswa dan semester. Status pada halaman ini adalah catatan di database SIAKADU, bukan verifikasi transaksi langsung dari bank.</p></div></div>
<div class="stat-grid">
    <div class="stat-card"><div class="stat-label">Total Tagihan Tercatat</div><div class="stat-value">{{ number_format($stats['total']) }}</div><div class="stat-note">Record pembayaran UKT</div></div>
    <div class="stat-card"><div class="stat-label">Lunas</div><div class="stat-value">{{ number_format($stats['paid']) }}</div><div class="stat-note">Status tercatat Lunas</div></div>
    <div class="stat-card"><div class="stat-label">Belum Bayar</div><div class="stat-value">{{ number_format($stats['unpaid']) }}</div><div class="stat-note">Masih tercatat sebagai tagihan</div></div>
    <div class="stat-card"><div class="stat-label">Nilai Tagihan</div><div class="stat-value" style="font-size:23px">Rp {{ number_format($stats['billed'],0,',','.') }}</div><div class="stat-note">{{ number_format($stats['dispensation']) }} dispensasi tercatat</div></div>
</div>
<section class="card panel"><div class="panel-head"><div><h2>Tambah Tagihan UKT</h2><div class="secondary-text">Satu mahasiswa hanya boleh memiliki satu record pembayaran per semester.</div></div></div>
<div class="panel-body"><form method="POST" action="{{ route('admin.ukt-payments.store') }}"><!-- @csrf --><input type="hidden" name="_token" value="{{ csrf_token() }}">
<div class="form-grid">
    <div class="field"><label for="npm">NPM Mahasiswa *</label><input id="npm" name="npm" value="{{ old('npm') }}" required placeholder="Masukkan NPM yang valid"></div>
    <div class="field"><label for="kode_semester">Periode Akademik *</label><select id="kode_semester" name="kode_semester" required><option value="">Pilih periode</option>@foreach($terms as $term)<option value="{{ $term->kode_semester }}" @selected(old('kode_semester')===$term->kode_semester)>{{ $term->kode_semester }} — {{ $term->nama_semester }}</option>@endforeach</select></div>
    <div class="field"><label for="nomor_va">Nomor VA *</label><input id="nomor_va" name="nomor_va" maxlength="30" value="{{ old('nomor_va') }}" required></div>
    <div class="field"><label for="nominal_tagihan">Nominal Tagihan (Rp) *</label><input id="nominal_tagihan" name="nominal_tagihan" type="number" min="0" step="1000" value="{{ old('nominal_tagihan') }}" required></div>
    <div class="field"><label for="bank_mitra">Bank Mitra</label><input id="bank_mitra" name="bank_mitra" maxlength="30" value="{{ old('bank_mitra','BNI') }}"></div>
</div>
<div class="form-actions"><button class="btn btn-primary" type="submit">Buat Tagihan</button></div>
</form></div></section>
<form class="filter-bar" method="GET" action="{{ route('admin.ukt-payments.index') }}">
    <div class="field field-grow"><label for="search">Cari mahasiswa</label><input id="search" name="search" value="{{ request('search') }}" placeholder="NPM atau nama mahasiswa"></div>
    <div class="field field-small"><label for="kode_semester_filter">Periode</label><select id="kode_semester_filter" name="kode_semester"><option value="">Semua periode</option>@foreach($terms as $term)<option value="{{ $term->kode_semester }}" @selected(request('kode_semester')===$term->kode_semester)>{{ $term->kode_semester }} — {{ $term->nama_semester }}</option>@endforeach</select></div>
    <div class="field field-small"><label for="status_bayar">Status</label><select id="status_bayar" name="status_bayar"><option value="">Semua status</option>@foreach(['Belum Bayar','Lunas','Dispensasi'] as $status)<option value="{{ $status }}" @selected(request('status_bayar')===$status)>{{ $status }}</option>@endforeach</select></div>
    <div class="field field-small"><label for="bank_mitra_filter">Bank</label><input id="bank_mitra_filter" name="bank_mitra" value="{{ request('bank_mitra') }}" placeholder="Nama bank"></div>
    <button class="btn btn-primary" type="submit">Cari</button><a class="btn" href="{{ route('admin.ukt-payments.index') }}">Reset</a>
</form>
<section class="card"><div class="panel-head"><div><h2>Daftar Tagihan Mahasiswa</h2><div class="secondary-text">{{ number_format($payments->total()) }} data pembayaran ditemukan</div></div></div>
<div class="table-wrap"><table class="data-table"><thead><tr><th>Mahasiswa</th><th>Semester</th><th>Nomor VA</th><th>Nominal</th><th>Bank</th><th>Status</th><th>Tanggal Bayar</th><th>Ubah Status</th></tr></thead><tbody>
@forelse($payments as $payment)
<tr>
<td><div class="primary-text">{{ $payment->mahasiswa?->nama ?? 'Mahasiswa tidak ditemukan' }}</div><div class="secondary-text">{{ $payment->npm }} · {{ $payment->mahasiswa?->prodi?->nama_prodi }}</div></td>
<td>{{ $payment->tahunAkademik?->nama_semester ?? $payment->kode_semester }}<div class="secondary-text">{{ $payment->tahunAkademik?->tahun_ajaran }}</div></td>
<td class="number">{{ substr((string)$payment->nomor_va,0,3) }}••••{{ substr((string)$payment->nomor_va,-3) }}</td>
<td>Rp {{ number_format((float)$payment->nominal_tagihan,0,',','.') }}</td><td>{{ $payment->bank_mitra ?? '—' }}</td>
<td><span class="badge {{ $payment->status_bayar==='Lunas'?'badge-success':($payment->status_bayar==='Dispensasi'?'badge-info':'badge-warning') }}">{{ $payment->status_bayar }}</span></td>
<td>{{ $payment->tgl_bayar?->format('d M Y H:i') ?? '—' }}</td>
<td><form method="POST" action="{{ route('admin.ukt-payments.status',$payment->id_pembayaran) }}" style="display:flex;gap:6px;align-items:center;min-width:185px">@csrf @method('PATCH')<select name="status_bayar" aria-label="Status pembayaran">@foreach(['Belum Bayar','Lunas','Dispensasi'] as $status)<option value="{{ $status }}" @selected($payment->status_bayar===$status)>{{ $status }}</option>@endforeach</select><button class="btn btn-sm btn-primary" type="submit">Simpan</button></form></td>
</tr>
@empty<tr><td colspan="8"><div class="empty"><strong>Tidak ada tagihan ditemukan</strong>Sesuaikan filter atau buat tagihan baru di atas.</div></td></tr>@endforelse
</tbody></table></div><div class="pagination"><span>Menampilkan {{ $payments->firstItem() ?? 0 }}–{{ $payments->lastItem() ?? 0 }} dari {{ number_format($payments->total()) }} tagihan</span>{{ $payments->links() }}</div>
</section>
@endsection
