<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Krs;

class KhsController extends Controller
{
    public function show(string $npm, string $kodeSemester)
    {
        $krs = Krs::with([
            'mahasiswa.prodi',
            'tahunAkademik',
            'details.kelasKuliah.mataKuliah',
            'details.kelasKuliah.dosenPengampu',
        ])
            ->where('npm', $npm)
            ->where('kode_semester', $kodeSemester)
            ->firstOrFail();

        $graded = $krs->details->filter(function ($detail) {
            return $detail->nilai_huruf !== null
                && $detail->bobot_mutu !== null
                && $detail->kelasKuliah?->mataKuliah !== null;
        });

        $totalSksDinilai = $graded->sum(function ($detail) {
            return $detail->kelasKuliah->mataKuliah->total_sks;
        });

        $totalMutu = $graded->sum(function ($detail) {
            $sks = $detail->kelasKuliah->mataKuliah->total_sks;

            return (float) $detail->bobot_mutu * $sks;
        });

        $ips = $totalSksDinilai > 0
            ? round($totalMutu / $totalSksDinilai, 2)
            : null;

        return response()->json([
            'success' => true,
            'data' => [
                'npm' => $krs->npm,
                'nama' => $krs->mahasiswa->nama,
                'program_studi' => $krs->mahasiswa->prodi->nama_prodi,
                'semester' => $krs->tahunAkademik,
                'status_krs' => $krs->status_persetujuan_pa,
                'total_sks_dinilai' => $totalSksDinilai,
                'total_mutu' => round($totalMutu, 2),
                'ips' => $ips,
                'mata_kuliah' => $krs->details,
            ],
        ]);
    }
}