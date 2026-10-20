@extends('layouts.student')
@section('title', 'Pembayaran UKT')
@section('page-label', 'Pembayaran UKT')

@section('content')
<div class="page-head">
    <div><div class="eyebrow">Informasi keuangan akademik</div><h1>Status &amp; Riwayat Pembayaran UKT</h1><p>Halaman ini hanya menampilkan catatan UKT atas NPM yang sedang login. Status berasal dari database, bukan pemeriksaan langsung ke bank.</p></div>
</div>

<div class="student-stat-grid" style="grid-template-columns:repeat(4,minmax(0,1fr))">
    <div class="stat-card"><div class="stat-label">Total Tagihan</div><div class="stat-value">{{ number_format($stats['total']) }}</div><div class="stat-note">Seluruh periode</div></div>
    <div class="stat-card"><div class="stat-label">Lunas</div><div class="stat-value">{{ number_format($stats['paid']) }}</div><div class="stat-note">Catatan berstatus Lunas</div></div>
    <div class="stat-card"><div class="stat-label">Belum Bayar</div><div class="stat-value">{{ number_format($stats['unpaid']) }}</div><div class="stat-note">Catatan berstatus Belum Bayar</div></div>
    <div class="stat-card"><div class="stat-label">Dispensasi</div><div class="stat-value">{{ number_format($stats['dispensation']) }}</div><div class="stat-note">Catatan dispensasi</div></div>
</div>

<section class="card panel">
    <div class="panel-head"><div><h2>Riwayat Tagihan dan Pembayaran</h2><div class="secondary-text">{{ number_format($payments->total()) }} catatan untuk NPM {{ $student->npm }}</div></div></div>
    <div class="table-wrap"><table class="data-table"><thead><tr><th>Periode</th><th>Nominal</th><th>Nomor VA</th><th>Bank</th><th>Status</th><th>Tanggal Bayar</th></tr></thead><tbody>
        @forelse($payments as $payment)
            <tr>
                <td>{{ $payment->tahunAkademik?->nama_semester ?? $payment->kode_semester }}<div class="secondary-text">{{ $payment->tahunAkademik?->tahun_ajaran }}</div></td>
                <td><strong>Rp {{ number_format((float)$payment->nominal_tagihan, 0, ',', '.') }}</strong></td>
                <td class="number">{{ strlen((string)$payment->nomor_va) > 6 ? str_repeat('•', strlen((string)$payment->nomor_va)-4).substr((string)$payment->nomor_va,-4) : '••••' }}</td>
                <td>{{ $payment->bank_mitra ?? '—' }}</td>
                <td><span class="badge {{ $payment->status_bayar === 'Lunas' ? 'badge-success' : ($payment->status_bayar === 'Dispensasi' ? 'badge-info' : 'badge-warning') }}">{{ $payment->status_bayar }}</span></td>
                <td>{{ $payment->tgl_bayar?->format('d M Y H:i') ?? '—' }}</td>
            </tr>
        @empty
            <tr><td colspan="6"><div class="empty"><strong>Belum ada riwayat pembayaran</strong>Belum ada catatan tagihan UKT yang tersimpan untuk akun ini.</div></td></tr>
        @endforelse
    </tbody></table></div>
    <div class="pagination"><span>Menampilkan {{ $payments->firstItem() ?? 0 }}–{{ $payments->lastItem() ?? 0 }} dari {{ number_format($payments->total()) }} data</span>{{ $payments->links() }}</div>
</section>
@endsection
