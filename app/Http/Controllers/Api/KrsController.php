<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KelasKuliah;
use App\Models\Krs;
use App\Models\KrsDetail;
use Illuminate\Http\Request;

class KrsController extends Controller
{
    private function relations(): array
    {
        return [
            'mahasiswa.prodi',
            'tahunAkademik',
            'details.kelasKuliah.mataKuliah',
            'details.kelasKuliah.dosenPengampu',
            'details.kelasKuliah.ruangan',
        ];
    }

    public function index(Request $request)
    {
        $query = Krs::with($this->relations());

        if ($request->filled('npm')) {
            $query->where('npm', $request->input('npm'));
        }

        if ($request->filled('kode_semester')) {
            $query->where(
                'kode_semester',
                $request->input('kode_semester')
            );
        }

        if ($request->filled('status')) {
            $query->where(
                'status_persetujuan_pa',
                $request->input('status')
            );
        }

        return response()->json([
            'success' => true,
            'data' => $query
                ->orderByDesc('id_krs')
                ->paginate(min(
                    max($request->integer('per_page', 20), 1),
                    100
                )),
        ]);
    }

    public function show(Krs $krs)
    {
        return response()->json([
            'success' => true,
            'data' => $krs->load($this->relations()),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'npm' => 'required|string|exists:mahasiswa,npm',
            'kode_semester' => 'required|string|size:5|exists:tahun_akademik,kode_semester',
        ]);

        $semester = \App\Models\TahunAkademik::where(
            'kode_semester',
            $data['kode_semester']
        )->firstOrFail();

        if (!$semester->is_aktif) {
            return response()->json([
                'success' => false,
                'message' => 'KRS hanya dapat dibuat pada semester aktif.',
            ], 422);
        }

        $sudahAda = Krs::where('npm', $data['npm'])
            ->where('kode_semester', $data['kode_semester'])
            ->exists();

        if ($sudahAda) {
            return response()->json([
                'success' => false,
                'message' => 'Mahasiswa sudah memiliki KRS untuk semester ini.',
            ], 409);
        }

        $data['status_persetujuan_pa'] = 'Draft';

        $krs = Krs::create($data);

        return response()->json([
            'success' => true,
            'message' => 'KRS berhasil dibuat.',
            'data' => $krs,
        ], 201);
    }

    public function addDetail(Request $request, Krs $krs)
    {
        if (!in_array(
            $krs->status_persetujuan_pa,
            ['Draft', 'Ditolak'],
            true
        )) {
            return response()->json([
                'success' => false,
                'message' => 'KRS yang sedang diajukan atau sudah disetujui tidak dapat diubah.',
            ], 422);
        }

        $data = $request->validate([
            'id_kelas' => 'required|integer|exists:kelas_kuliah,id_kelas',
        ]);

        $kelas = KelasKuliah::with('mataKuliah')
            ->findOrFail($data['id_kelas']);

        if ($kelas->kode_semester !== $krs->kode_semester) {
            return response()->json([
                'success' => false,
                'message' => 'Kelas kuliah tidak sesuai dengan semester KRS.',
            ], 422);
        }

        $alreadyTaken = KrsDetail::where('id_krs', $krs->id_krs)
            ->where('id_kelas', $kelas->id_kelas)
            ->exists();

        if ($alreadyTaken) {
            return response()->json([
                'success' => false,
                'message' => 'Kelas tersebut sudah terdaftar dalam KRS.',
            ], 409);
        }

        $jumlahPeserta = KrsDetail::where('id_kelas', $kelas->id_kelas)
            ->whereHas('krs', function ($query) {
                $query->whereIn('status_persetujuan_pa', [
                    'Diajukan',
                    'Disetujui',
                ]);
            })
            ->count();

        if ($jumlahPeserta >= $kelas->kuota) {
            return response()->json([
                'success' => false,
                'message' => 'Kuota kelas sudah penuh.',
            ], 422);
        }

        $jumlahSks = KrsDetail::where('id_krs', $krs->id_krs)
            ->with('kelasKuliah.mataKuliah')
            ->get()
            ->sum(fn ($item) =>
                $item->kelasKuliah->mataKuliah->total_sks
            );

        if ($jumlahSks + $kelas->mataKuliah->total_sks > 24) {
            return response()->json([
                'success' => false,
                'message' => 'Penambahan mata kuliah melebihi batas 24 SKS pada prototipe ini.',
            ], 422);
        }

        $detail = KrsDetail::create([
            'id_krs' => $krs->id_krs,
            'id_kelas' => $kelas->id_kelas,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Mata kuliah berhasil ditambahkan ke KRS.',
            'data' => $detail->load('kelasKuliah.mataKuliah'),
        ], 201);
    }

    public function removeDetail(Krs $krs, int $detail)
    {
        if (!in_array(
            $krs->status_persetujuan_pa,
            ['Draft', 'Ditolak'],
            true
        )) {
            return response()->json([
                'success' => false,
                'message' => 'KRS yang sedang diajukan atau sudah disetujui tidak dapat diubah.',
            ], 422);
        }

        $item = KrsDetail::where('id_krs', $krs->id_krs)
            ->where('id_krs_detail', $detail)
            ->firstOrFail();

        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mata kuliah berhasil dihapus dari KRS.',
        ]);
    }

    public function updateStatus(Request $request, Krs $krs)
    {
        $data = $request->validate([
            'status_persetujuan_pa' => 'required|in:Diajukan,Disetujui,Ditolak',
            'catatan_pa' => 'nullable|string',
        ]);

        if (
            $data['status_persetujuan_pa'] === 'Disetujui'
            && $krs->details()->doesntExist()
        ) {
            return response()->json([
                'success' => false,
                'message' => 'KRS harus memiliki minimal satu mata kuliah.',
            ], 422);
        }

        $krs->status_persetujuan_pa = $data['status_persetujuan_pa'];

        if (array_key_exists('catatan_pa', $data)) {
            $krs->catatan_pa = $data['catatan_pa'];
        }

        $krs->save();

        return response()->json([
            'success' => true,
            'message' => 'Status KRS berhasil diperbarui.',
            'data' => $krs->fresh($this->relations()),
        ]);
    }
}