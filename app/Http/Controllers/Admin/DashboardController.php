<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Krs;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Prodi;
use App\Models\PembayaranUkt;
use App\Models\TahunAkademik;

class DashboardController extends Controller
{
    public function index()
    {
        $activeTerm = TahunAkademik::where('is_aktif', true)->first();
        $stats = [
            'students' => Mahasiswa::count(),
            'lecturers' => Dosen::count(),
            'programs' => Prodi::count(),
            'courses' => MataKuliah::count(),
            'pending_krs' => Krs::whereIn('status_persetujuan_pa', ['Diajukan', 'Draft'])->count(),
            'unpaid_ukt' => PembayaranUkt::where('status_bayar', 'Belum Bayar')->count(),
        ];
        $recentKrs = Krs::with(['mahasiswa.prodi', 'tahunAkademik'])
            ->orderByDesc('id_krs')->limit(8)->get();

        return view('admin.dashboard', compact('stats', 'activeTerm', 'recentKrs'));
    }
}
