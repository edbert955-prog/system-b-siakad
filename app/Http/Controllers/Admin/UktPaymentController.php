<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use App\Models\PembayaranUkt;
use App\Models\TahunAkademik;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UktPaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = PembayaranUkt::with([
            'mahasiswa.prodi',
            'tahunAkademik',
        ]);

        if ($request->filled('search')) {
            $term = trim($request->string('search')->toString());
            $query->whereHas('mahasiswa', function ($q) use ($term) {
                $q->where('npm', 'ilike', "%{$term}%")
                  ->orWhere('nama', 'ilike', "%{$term}%");
            });
        }

        if ($request->filled('kode_semester')) {
            $query->where('kode_semester', $request->input('kode_semester'));
        }

        if ($request->filled('status_bayar')) {
            $query->where('status_bayar', $request->input('status_bayar'));
        }

        if ($request->filled('bank_mitra')) {
            $query->where('bank_mitra', $request->input('bank_mitra'));
        }

        $payments = $query->orderByDesc('id_pembayaran')
            ->paginate(15)
            ->withQueryString();

        $terms = TahunAkademik::orderByDesc('kode_semester')->get();

        $stats = [
            'total' => PembayaranUkt::count(),
            'paid' => PembayaranUkt::where('status_bayar', 'Lunas')->count(),
            'unpaid' => PembayaranUkt::where('status_bayar', 'Belum Bayar')->count(),
            'dispensation' => PembayaranUkt::where('status_bayar', 'Dispensasi')->count(),
            'billed' => (float) PembayaranUkt::sum('nominal_tagihan'),
        ];

        return view('admin.ukt-payments', compact('payments', 'terms', 'stats'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'npm' => ['required', 'string', 'exists:mahasiswa,npm'],
            'kode_semester' => ['required', 'string', 'size:5', 'exists:tahun_akademik,kode_semester'],
            'nomor_va' => ['required', 'string', 'max:30', 'unique:pembayaran_ukt,nomor_va'],
            'nominal_tagihan' => ['required', 'numeric', 'min:0'],
            'bank_mitra' => ['nullable', 'string', 'max:30'],
        ]);

        if (PembayaranUkt::where('npm', $data['npm'])->where('kode_semester', $data['kode_semester'])->exists()) {
            return back()->withInput()->withErrors([
                'payment' => 'Tagihan UKT untuk mahasiswa dan semester tersebut sudah ada.',
            ]);
        }

        $data['status_bayar'] = 'Belum Bayar';
        $data['tgl_bayar'] = null;
        $data['bank_mitra'] = $data['bank_mitra'] ?: 'BNI';

        try {
            PembayaranUkt::create($data);
            return redirect()->route('admin.ukt-payments.index')
                ->with('success', 'Tagihan UKT berhasil dibuat.');
        } catch (QueryException $e) {
            report($e);
            return back()->withInput()->withErrors([
                'database' => 'Tagihan gagal disimpan. Periksa nomor VA, NPM, dan semester.',
            ]);
        }
    }

    public function updateStatus(Request $request, int $id)
    {
        $payment = PembayaranUkt::findOrFail($id);

        $data = $request->validate([
            'status_bayar' => ['required', Rule::in(['Belum Bayar', 'Lunas', 'Dispensasi'])],
            'bank_mitra' => ['nullable', 'string', 'max:30'],
        ]);

        $payment->status_bayar = $data['status_bayar'];
        if (array_key_exists('bank_mitra', $data)) {
            $payment->bank_mitra = $data['bank_mitra'];
        }

        if ($payment->status_bayar === 'Lunas') {
            $payment->tgl_bayar = $payment->tgl_bayar ?? now();
        } else {
            $payment->tgl_bayar = null;
        }

        $payment->save();

        return redirect()->route('admin.ukt-payments.index')
            ->with('success', 'Status pembayaran berhasil diperbarui.');
    }
}
