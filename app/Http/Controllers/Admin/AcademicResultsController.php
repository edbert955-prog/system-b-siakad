<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Krs;
use App\Models\Mahasiswa;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;

class AcademicResultsController extends Controller
{
    public function index(Request $request)
    {
        $student = null;
        $selectedKrs = null;
        $details = collect();
        $transcript = collect();
        $ips = null;
        $totalSks = 0;
        $totalMutu = 0.0;

        $search = trim($request->string('npm')->toString());
        $selectedTerm = $request->string('kode_semester')->toString();

        if ($search !== '') {
            $student = Mahasiswa::with('prodi.fakultas')->find($search);

            if ($student) {
                $allKrs = Krs::with([
                    'tahunAkademik',
                    'details.kelasKuliah.mataKuliah',
                    'details.kelasKuliah.dosenPengampu',
                ])->where('npm', $student->npm)
                  ->orderByDesc('kode_semester')
                  ->get();

                $transcript = $allKrs->flatMap(function ($krs) {
                    return $krs->details->map(function ($detail) use ($krs) {
                        $detail->academic_term = $krs->tahunAkademik;
                        $detail->krs_status = $krs->status_persetujuan_pa;
                        return $detail;
                    });
                });

                if ($selectedTerm !== '') {
                    $selectedKrs = $allKrs->firstWhere('kode_semester', $selectedTerm);
                } else {
                    $selectedKrs = $allKrs->first();
                    $selectedTerm = $selectedKrs?->kode_semester ?? '';
                }

                $details = $selectedKrs?->details ?? collect();

                $graded = $details->filter(fn ($detail) =>
                    $detail->nilai_huruf !== null &&
                    $detail->bobot_mutu !== null &&
                    $detail->kelasKuliah?->mataKuliah !== null
                );

                $totalSks = $graded->sum(
                    fn ($detail) => (int) $detail->kelasKuliah->mataKuliah->total_sks
                );

                $totalMutu = $graded->sum(
                    fn ($detail) =>
                        (float) $detail->bobot_mutu *
                        (int) $detail->kelasKuliah->mataKuliah->total_sks
                );

                $ips = $totalSks > 0 ? round($totalMutu / $totalSks, 2) : null;
            }
        }

        $terms = TahunAkademik::orderByDesc('kode_semester')->get();

        return view('admin.academic-results', compact(
            'student',
            'selectedKrs',
            'details',
            'transcript',
            'terms',
            'selectedTerm',
            'ips',
            'totalSks',
            'totalMutu'
        ));
    }
}
