<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PembayaranUkt;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PembayaranUktController extends Controller
{
    public function index(Request $request)
    {
        $query = PembayaranUkt::with([
            'mahasiswa.prodi',
            'tahunAkademik',
        ]);

        if ($request->filled('npm')) {
            $query->where('npm', $request->input('npm'));
        }

        if ($request->filled('kode_semester')) {
            $query->where(
                'kode_semester',
                $request->input('kode_semester')
            );
        }

        if ($request->filled('status_bayar')) {
            $query->where(
                'status_bayar',
                $request->input('status_bayar')
            );
        }

        return response()->json([
            'success' => true,
            'data' => $query
                ->orderByDesc('id_pembayaran')
                ->paginate(min(
                    max($request->integer('per_page', 20), 1),
                    100
                )),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'npm' => 'required|string|exists:mahasiswa,npm',
            'kode_semester' => 'required|string|size:5|exists:tahun_akademik,kode_semester',
            'nomor_va' => 'required|string|max:30|unique:pembayaran_ukt,nomor_va',
            'nominal_tagihan' => 'required|numeric|min:0',
            'status_bayar' => [
                'required',
                Rule::in(['Belum Bayar', 'Lunas', 'Dispensasi']),
            ],
            'tgl_bayar' => 'nullable|date',
            'bank_mitra' => 'nullable|string|max:30',
        ]);

        $exists = PembayaranUkt::where('npm', $data['npm'])
            ->where('kode_semester', $data['kode_semester'])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Tagihan mahasiswa untuk semester tersebut sudah ada.',
            ], 409);
        }

        if ($data['status_bayar'] === 'Lunas') {
            $data['tgl_bayar'] = $data['tgl_bayar'] ?? now();
        } else {
            $data['tgl_bayar'] = null;
        }

        $pembayaran = PembayaranUkt::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Data pembayaran UKT berhasil ditambahkan.',
            'data' => $pembayaran,
        ], 201);
    }

    public function update(Request $request, int $id)
    {
        $pembayaran = PembayaranUkt::findOrFail($id);

        $data = $request->validate([
            'status_bayar' => 'sometimes|required|in:Belum Bayar,Lunas,Dispensasi',
            'tgl_bayar' => 'sometimes|nullable|date',
            'bank_mitra' => 'sometimes|nullable|string|max:30',
        ]);

        if (($data['status_bayar'] ?? null) === 'Lunas') {
            $data['tgl_bayar'] = $data['tgl_bayar']
                ?? $pembayaran->tgl_bayar
                ?? now();
        } elseif (
            isset($data['status_bayar'])
            && $data['status_bayar'] !== 'Lunas'
        ) {
            $data['tgl_bayar'] = null;
        }

        $pembayaran->fill($data);
        $pembayaran->save();

        return response()->json([
            'success' => true,
            'message' => 'Data pembayaran UKT berhasil diperbarui.',
            'data' => $pembayaran,
        ]);
    }
}