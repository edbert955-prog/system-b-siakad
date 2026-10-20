<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\KelasKuliah;
use App\Models\Krs;
use App\Models\KrsDetail;
use App\Models\Mahasiswa;
use App\Models\PembayaranUkt;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StudentPortalController extends Controller
{
    private function student(Request $request): Mahasiswa
    {
        $npm = trim((string) $request->user()->nomor_induk);

        return Mahasiswa::with([
            'prodi.fakultas', 'statusMahasiswa', 'dosenPa',
        ])->findOrFail($npm);
    }

    private function activeTerm(): ?TahunAkademik
    {
        return TahunAkademik::where('is_aktif', true)->first();
    }

    private function krsWindowOpen(?TahunAkademik $term): bool
    {
        if (!$term || !$term->tgl_mulai_krs || !$term->tgl_selesai_krs) {
            return false;
        }

        $today = now()->startOfDay();

        return $today->greaterThanOrEqualTo($term->tgl_mulai_krs->copy()->startOfDay())
            && $today->lessThanOrEqualTo($term->tgl_selesai_krs->copy()->endOfDay());
    }

    public function dashboard(Request $request)
    {
        $student = $this->student($request);
        $term = $this->activeTerm();

        $currentKrs = null;
        $schedule = collect();
        if ($term) {
            $currentKrs = Krs::with([
                'details.kelasKuliah.mataKuliah',
                'details.kelasKuliah.dosenPengampu',
                'details.kelasKuliah.ruangan',
            ])->where('npm', $student->npm)
              ->where('kode_semester', $term->kode_semester)
              ->first();

            if ($currentKrs && $currentKrs->status_persetujuan_pa === 'Disetujui') {
                $schedule = $currentKrs->details
                    ->filter(fn ($detail) => $detail->kelasKuliah)
                    ->sortBy(fn ($detail) => sprintf(
                        '%02d-%s',
                        $this->dayOrder($detail->kelasKuliah->hari),
                        $detail->kelasKuliah->jam_mulai
                    ))
                    ->take(5);
            }
        }

        $latestPayment = PembayaranUkt::with('tahunAkademik')
            ->where('npm', $student->npm)
            ->orderByDesc('kode_semester')
            ->first();

        $recentKrs = Krs::with('tahunAkademik')
            ->where('npm', $student->npm)
            ->orderByDesc('kode_semester')
            ->limit(4)
            ->get();

        return view('student.dashboard', compact(
            'student', 'term', 'currentKrs', 'schedule', 'latestPayment', 'recentKrs'
        ));
    }

    public function krs(Request $request)
    {
        $student = $this->student($request);
        $term = $this->activeTerm();
        $termOpen = $this->krsWindowOpen($term);
        $krs = null;
        $selectedDetails = collect();
        $availableClasses = collect();
        $totalSks = 0;

        if ($term) {
            $krs = Krs::with([
                'details.kelasKuliah.mataKuliah.prodi',
                'details.kelasKuliah.dosenPengampu',
                'details.kelasKuliah.ruangan',
            ])->where('npm', $student->npm)
              ->where('kode_semester', $term->kode_semester)
              ->first();

            $selectedDetails = $krs?->details ?? collect();
            $totalSks = $selectedDetails->sum(
                fn ($detail) => (int) ($detail->kelasKuliah?->mataKuliah?->total_sks ?? 0)
            );

            $selectedIds = $selectedDetails->pluck('id_kelas')->all();
            $query = KelasKuliah::with([
                'mataKuliah.prodi', 'dosenPengampu', 'ruangan',
            ])->where('kode_semester', $term->kode_semester);

            if (!empty($selectedIds)) {
                $query->whereNotIn('id_kelas', $selectedIds);
            }

            if ($request->filled('search')) {
                $search = trim($request->string('search')->toString());
                $query->whereHas('mataKuliah', function ($q) use ($search) {
                    $q->where('kode_mk', 'ilike', "%{$search}%")
                        ->orWhere('nama_mk', 'ilike', "%{$search}%");
                });
            }

            if ($request->filled('semester_paket')) {
                $query->whereHas('mataKuliah', fn ($q) =>
                    $q->where('semester_paket', $request->integer('semester_paket'))
                );
            }

            $availableClasses = $query->orderBy('hari')
                ->orderBy('jam_mulai')
                ->limit(100)
                ->get();
        }

        $editable = $termOpen && (!$krs || in_array(
            $krs->status_persetujuan_pa,
            ['Draft', 'Ditolak'],
            true
        ));

        return view('student.krs', compact(
            'student', 'term', 'termOpen', 'krs', 'selectedDetails',
            'availableClasses', 'totalSks', 'editable'
        ));
    }

    public function addKrsDetail(Request $request)
    {
        $student = $this->student($request);
        $data = $request->validate([
            'id_kelas' => ['required', 'integer', 'exists:kelas_kuliah,id_kelas'],
        ]);

        $term = $this->activeTerm();
        if (!$this->krsWindowOpen($term)) {
            throw ValidationException::withMessages([
                'krs' => 'Periode pengisian KRS sedang tidak dibuka.',
            ]);
        }

        $class = KelasKuliah::with('mataKuliah')->findOrFail($data['id_kelas']);
        if ($class->kode_semester !== $term->kode_semester) {
            throw ValidationException::withMessages([
                'id_kelas' => 'Kelas tidak termasuk semester aktif.',
            ]);
        }

        $krs = Krs::firstOrCreate(
            ['npm' => $student->npm, 'kode_semester' => $term->kode_semester],
            ['status_persetujuan_pa' => 'Draft', 'tgl_pengisian' => now()]
        );

        if (!in_array($krs->status_persetujuan_pa, ['Draft', 'Ditolak'], true)) {
            throw ValidationException::withMessages([
                'krs' => 'KRS sudah diajukan atau disetujui sehingga tidak dapat diubah.',
            ]);
        }

        if ($krs->status_persetujuan_pa === 'Ditolak') {
            $krs->status_persetujuan_pa = 'Draft';
            $krs->catatan_pa = null;
            $krs->save();
        }

        if ($krs->details()->where('id_kelas', $class->id_kelas)->exists()) {
            throw ValidationException::withMessages([
                'id_kelas' => 'Kelas sudah ada di dalam KRS.',
            ]);
        }

        $otherDetails = KrsDetail::with('kelasKuliah.mataKuliah')
            ->where('id_krs', $krs->id_krs)->get();

        $currentCredits = $otherDetails->sum(
            fn ($detail) => (int) ($detail->kelasKuliah?->mataKuliah?->total_sks ?? 0)
        );
        $newCredits = (int) ($class->mataKuliah?->total_sks ?? 0);

        if ($currentCredits + $newCredits > 24) {
            throw ValidationException::withMessages([
                'id_kelas' => 'Penambahan kelas melebihi batas 24 SKS untuk prototipe ini.',
            ]);
        }

        $conflict = $otherDetails->first(function ($detail) use ($class) {
            $other = $detail->kelasKuliah;
            if (!$other || $other->hari !== $class->hari) {
                return false;
            }

            return (string) $other->jam_mulai < (string) $class->jam_selesai
                && (string) $other->jam_selesai > (string) $class->jam_mulai;
        });

        if ($conflict) {
            throw ValidationException::withMessages([
                'id_kelas' => 'Jadwal kelas bertabrakan dengan mata kuliah yang sudah dipilih.',
            ]);
        }

        $registered = KrsDetail::where('id_kelas', $class->id_kelas)
            ->whereHas('krs', fn ($q) => $q->whereIn(
                'status_persetujuan_pa', ['Diajukan', 'Disetujui']
            ))->count();

        if ($registered >= $class->kuota) {
            throw ValidationException::withMessages([
                'id_kelas' => 'Kuota kelas sudah penuh.',
            ]);
        }

        DB::transaction(function () use ($krs, $class) {
            KrsDetail::create([
                'id_krs' => $krs->id_krs,
                'id_kelas' => $class->id_kelas,
            ]);
        });

        return redirect()->route('student.krs')->with('success', 'Mata kuliah ditambahkan ke KRS.');
    }

    public function removeKrsDetail(Request $request, int $detail)
    {
        $student = $this->student($request);
        $item = KrsDetail::with('krs.tahunAkademik')->findOrFail($detail);
        $term = $this->activeTerm();

        abort_unless($item->krs && $item->krs->npm === $student->npm, 404);

        if (!$this->krsWindowOpen($term) || $item->krs->kode_semester !== $term?->kode_semester) {
            throw ValidationException::withMessages([
                'krs' => 'Perubahan KRS hanya diperbolehkan selama periode KRS aktif.',
            ]);
        }

        if (!in_array($item->krs->status_persetujuan_pa, ['Draft', 'Ditolak'], true)) {
            throw ValidationException::withMessages([
                'krs' => 'KRS sudah diajukan atau disetujui dan tidak dapat diedit.',
            ]);
        }

        $item->delete();

        return redirect()->route('student.krs')->with('success', 'Mata kuliah dihapus dari KRS.');
    }

    public function submitKrs(Request $request)
    {
        $student = $this->student($request);
        $term = $this->activeTerm();

        if (!$this->krsWindowOpen($term)) {
            throw ValidationException::withMessages([
                'krs' => 'Periode pengisian KRS sedang tidak dibuka.',
            ]);
        }

        $krs = Krs::with('details')->where('npm', $student->npm)
            ->where('kode_semester', $term->kode_semester)->firstOrFail();

        if (!in_array($krs->status_persetujuan_pa, ['Draft', 'Ditolak'], true)) {
            throw ValidationException::withMessages([
                'krs' => 'KRS ini tidak berada pada status yang dapat diajukan.',
            ]);
        }

        if ($krs->details->isEmpty()) {
            throw ValidationException::withMessages([
                'krs' => 'Pilih setidaknya satu mata kuliah sebelum mengajukan KRS.',
            ]);
        }

        $krs->status_persetujuan_pa = 'Diajukan';
        $krs->tgl_pengisian = now();
        $krs->catatan_pa = null;
        $krs->save();

        return redirect()->route('student.krs')->with('success', 'KRS berhasil diajukan untuk pemeriksaan Dosen PA.');
    }

    public function khs(Request $request)
    {
        $student = $this->student($request);
        $allKrs = Krs::with([
            'tahunAkademik',
            'details.kelasKuliah.mataKuliah',
            'details.kelasKuliah.dosenPengampu',
        ])->where('npm', $student->npm)
          ->orderByDesc('kode_semester')
          ->get();

        $selectedTerm = $request->string('kode_semester')->toString();
        $selectedKrs = $selectedTerm !== ''
            ? $allKrs->firstWhere('kode_semester', $selectedTerm)
            : $allKrs->first();
        $grades = $selectedKrs?->details ?? collect();

        $graded = $grades->filter(fn ($detail) =>
            $detail->nilai_huruf !== null &&
            $detail->bobot_mutu !== null &&
            $detail->kelasKuliah?->mataKuliah !== null
        );
        $totalSks = $graded->sum(fn ($detail) => (int) $detail->kelasKuliah->mataKuliah->total_sks);
        $totalMutu = $graded->sum(fn ($detail) =>
            (float) $detail->bobot_mutu * (int) $detail->kelasKuliah->mataKuliah->total_sks
        );
        $ips = $totalSks > 0 ? round($totalMutu / $totalSks, 2) : null;

        $transcript = $allKrs->flatMap(function ($krs) {
            return $krs->details->map(function ($detail) use ($krs) {
                $detail->academicTerm = $krs->tahunAkademik;
                return $detail;
            });
        });

        return view('student.khs', compact(
            'student', 'allKrs', 'selectedKrs', 'selectedTerm',
            'grades', 'ips', 'totalSks', 'totalMutu', 'transcript'
        ));
    }

    public function schedule(Request $request)
    {
        $student = $this->student($request);
        $studentTerms = TahunAkademik::whereHas('krs', fn ($q) =>
            $q->where('npm', $student->npm)
        )->orderByDesc('kode_semester')->get();

        $activeTerm = $this->activeTerm();
        $selectedTerm = $request->string('kode_semester')->toString()
            ?: ($activeTerm?->kode_semester ?? $studentTerms->first()?->kode_semester);

        $krs = $selectedTerm
            ? Krs::with([
                'tahunAkademik',
                'details.kelasKuliah.mataKuliah',
                'details.kelasKuliah.dosenPengampu',
                'details.kelasKuliah.ruangan',
            ])->where('npm', $student->npm)
              ->where('kode_semester', $selectedTerm)
              ->where('status_persetujuan_pa', 'Disetujui')
              ->first()
            : null;

        $classes = $krs?->details
            ->filter(fn ($detail) => $detail->kelasKuliah)
            ->sortBy(fn ($detail) => sprintf(
                '%02d-%s',
                $this->dayOrder($detail->kelasKuliah->hari),
                $detail->kelasKuliah->jam_mulai
            )) ?? collect();

        return view('student.schedule', compact(
            'student', 'studentTerms', 'selectedTerm', 'krs', 'classes'
        ));
    }

    public function uktPayments(Request $request)
    {
        $student = $this->student($request);
        $payments = PembayaranUkt::with('tahunAkademik')
            ->where('npm', $student->npm)
            ->orderByDesc('kode_semester')
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'total' => PembayaranUkt::where('npm', $student->npm)->count(),
            'paid' => PembayaranUkt::where('npm', $student->npm)->where('status_bayar', 'Lunas')->count(),
            'unpaid' => PembayaranUkt::where('npm', $student->npm)->where('status_bayar', 'Belum Bayar')->count(),
            'dispensation' => PembayaranUkt::where('npm', $student->npm)->where('status_bayar', 'Dispensasi')->count(),
        ];

        return view('student.ukt-payments', compact('student', 'payments', 'stats'));
    }

    public function profile(Request $request)
    {
        $student = $this->student($request)->load([
            'jalurMasuk', 'orangTua', 'riwayatStatusSemester.statusMahasiswa',
        ]);

        return view('student.profile', compact('student'));
    }

    private function dayOrder(?string $day): int
    {
        $index = array_search($day, [
            'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu',
        ], true);

        return $index === false ? 8 : $index;
    }
}
