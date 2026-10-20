<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Krs;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KrsManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Krs::with([
            'mahasiswa.prodi',
            'tahunAkademik',
            'details.kelasKuliah.mataKuliah',
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

        if ($request->filled('status')) {
            $query->where('status_persetujuan_pa', $request->input('status'));
        }

        if ($request->filled('id_prodi')) {
            $query->whereHas('mahasiswa', function ($q) use ($request) {
                $q->where('id_prodi', $request->input('id_prodi'));
            });
        }

        $records = $query->orderByDesc('id_krs')->paginate(15)->withQueryString();

        $terms = \App\Models\TahunAkademik::orderByDesc('kode_semester')->get();
        $programs = \App\Models\Prodi::orderBy('nama_prodi')->get();

        $stats = [
            'total' => Krs::count(),
            'draft' => Krs::where('status_persetujuan_pa', 'Draft')->count(),
            'submitted' => Krs::where('status_persetujuan_pa', 'Diajukan')->count(),
            'approved' => Krs::where('status_persetujuan_pa', 'Disetujui')->count(),
            'rejected' => Krs::where('status_persetujuan_pa', 'Ditolak')->count(),
        ];

        return view('admin.krs-management', compact('records', 'terms', 'programs', 'stats'));
    }

    public function review(int $id)
    {
        $krs = Krs::with([
            'mahasiswa.prodi.fakultas',
            'mahasiswa.dosenPa',
            'tahunAkademik',
            'details.kelasKuliah.mataKuliah',
            'details.kelasKuliah.dosenPengampu',
            'details.kelasKuliah.ruangan',
        ])->findOrFail($id);

        $totalSks = $krs->details->sum(
            fn ($detail) => (int) ($detail->kelasKuliah?->mataKuliah?->total_sks ?? 0)
        );

        return view('admin.krs-review', compact('krs', 'totalSks'));
    }

    public function updateStatus(Request $request, int $id)
    {
        $krs = Krs::with('details')->findOrFail($id);

        $data = $request->validate([
            'status_persetujuan_pa' => [
                'required',
                Rule::in(['Diajukan', 'Disetujui', 'Ditolak']),
            ],
            'catatan_pa' => ['nullable', 'string', 'max:3000'],
        ]);

        if ($data['status_persetujuan_pa'] === 'Disetujui' && $krs->details->isEmpty()) {
            return back()->withInput()->withErrors([
                'krs' => 'KRS tidak dapat disetujui karena belum memiliki mata kuliah.',
            ]);
        }

        if ($data['status_persetujuan_pa'] === 'Ditolak' && trim($data['catatan_pa'] ?? '') === '') {
            return back()->withInput()->withErrors([
                'catatan_pa' => 'Catatan wajib diisi ketika KRS ditolak.',
            ]);
        }

        $krs->status_persetujuan_pa = $data['status_persetujuan_pa'];
        $krs->catatan_pa = $data['catatan_pa'] ?? null;
        $krs->save();

        return redirect()->route('admin.krs-management.review', $krs->id_krs)
            ->with('success', 'Status KRS berhasil diperbarui.');
    }
}
