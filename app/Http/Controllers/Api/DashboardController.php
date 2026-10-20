<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\KelasKuliah;
use App\Models\Krs;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\PembayaranUkt;
use App\Models\Prodi;
use App\Models\TahunAkademik;

class DashboardController extends Controller
{
    public function index()
    {
        $semesterAktif = TahunAkademik::where('is_aktif', true)->first();

        $kelasAktif = $semesterAktif
            ? KelasKuliah::where(
                'kode_semester',
                $semesterAktif->kode_semester
            )->count()
            : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'total_mahasiswa' => Mahasiswa::count(),
                'total_dosen' => Dosen::count(),
                'total_prodi' => Prodi::count(),
                'total_mata_kuliah' => MataKuliah::count(),

                'total_kelas_semester_aktif' => $kelasAktif,

                'krs_menunggu_persetujuan' => Krs::where(
                    'status_persetujuan_pa',
                    'Diajukan'
                )->count(),

                'ukt_belum_lunas' => PembayaranUkt::where(
                    'status_bayar',
                    'Belum Bayar'
                )->count(),

                'semester_aktif' => $semesterAktif,
            ],
        ]);
    }
}