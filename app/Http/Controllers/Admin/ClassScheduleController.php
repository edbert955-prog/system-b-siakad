<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Fakultas;
use App\Models\KelasKuliah;
use App\Models\MataKuliah;
use App\Models\Ruangan;
use App\Models\TahunAkademik;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ClassScheduleController extends Controller
{
    public function index(Request $request)
    {
        $query = KelasKuliah::with([
            'mataKuliah.prodi',
            'tahunAkademik',
            'dosenPengampu',
            'ruangan',
        ]);

        if ($request->filled('search')) {
            $term = trim($request->string('search')->toString());
            $query->where(function ($outer) use ($term) {
                $outer->whereHas('mataKuliah', function ($q) use ($term) {
                    $q->where('kode_mk', 'ilike', "%{$term}%")
                      ->orWhere('nama_mk', 'ilike', "%{$term}%");
                })->orWhere('nama_kelas', 'ilike', "%{$term}%");
            });
        }

        if ($request->filled('kode_semester')) {
            $query->where('kode_semester', $request->input('kode_semester'));
        }

        if ($request->filled('hari')) {
            $query->where('hari', $request->input('hari'));
        }

        if ($request->filled('id_dosen_pengampu')) {
            $query->where('id_dosen_pengampu', $request->input('id_dosen_pengampu'));
        }

        if ($request->filled('id_mk')) {
            $query->where('id_mk', $request->input('id_mk'));
        }

        $classes = $query->orderByRaw(
            "CASE hari WHEN 'Senin' THEN 1 WHEN 'Selasa' THEN 2 WHEN 'Rabu' THEN 3 WHEN 'Kamis' THEN 4 WHEN 'Jumat' THEN 5 WHEN 'Sabtu' THEN 6 WHEN 'Minggu' THEN 7 ELSE 8 END"
        )->orderBy('jam_mulai')->paginate(15)->withQueryString();

        $terms = TahunAkademik::orderByDesc('kode_semester')->get();
        $lecturers = Dosen::orderBy('nama_dosen')->get();
        $courses = MataKuliah::orderBy('kode_mk')->get();

        $stats = [
            'classes' => KelasKuliah::count(),
            'terms' => TahunAkademik::where('is_aktif', true)->count(),
            'unassigned_rooms' => KelasKuliah::whereNull('id_ruangan')->count(),
        ];

        return view('admin.class-schedules', compact('classes', 'terms', 'lecturers', 'courses', 'stats'));
    }

    public function create()
    {
        return view('admin.class-form', [
            'title' => 'Tambah Kelas Kuliah',
            'record' => null,
            'action' => route('admin.class-schedules.store'),
            'method' => 'POST',
            ...$this->formData(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $conflict = $this->findConflict($data);

        if ($conflict) {
            return back()->withInput()->withErrors(['schedule' => $conflict]);
        }

        try {
            KelasKuliah::create($data);
            return redirect()->route('admin.class-schedules.index')
                ->with('success', 'Jadwal kelas berhasil ditambahkan.');
        } catch (QueryException $e) {
            report($e);
            return back()->withInput()->withErrors([
                'database' => 'Kelas gagal disimpan. Periksa kombinasi mata kuliah, semester, dan nama kelas agar tidak duplikat.',
            ]);
        }
    }

    public function edit(int $id)
    {
        return view('admin.class-form', [
            'title' => 'Edit Jadwal Kelas',
            'record' => KelasKuliah::findOrFail($id),
            'action' => route('admin.class-schedules.update', $id),
            'method' => 'PATCH',
            ...$this->formData(),
        ]);
    }

    public function update(Request $request, int $id)
    {
        $record = KelasKuliah::findOrFail($id);
        $data = $request->validate($this->rules($record));
        $conflict = $this->findConflict($data, $record->id_kelas);

        if ($conflict) {
            return back()->withInput()->withErrors(['schedule' => $conflict]);
        }

        try {
            $record->update($data);
            return redirect()->route('admin.class-schedules.index')
                ->with('success', 'Jadwal kelas berhasil diperbarui.');
        } catch (QueryException $e) {
            report($e);
            return back()->withInput()->withErrors([
                'database' => 'Jadwal kelas gagal diperbarui. Periksa data unik dan relasi.',
            ]);
        }
    }

    public function destroy(int $id)
    {
        $record = KelasKuliah::findOrFail($id);

        if ($record->krsDetails()->exists()) {
            return back()->withErrors([
                'delete' => 'Kelas tidak dapat dihapus karena telah digunakan dalam KRS mahasiswa.',
            ]);
        }

        try {
            $record->delete();
            return back()->with('success', 'Kelas berhasil dihapus.');
        } catch (QueryException $e) {
            report($e);
            return back()->withErrors(['delete' => 'Kelas masih direferensikan oleh data lain.']);
        }
    }

    private function formData(): array
    {
        return [
            'courses' => MataKuliah::with('prodi')->orderBy('kode_mk')->get(),
            'terms' => TahunAkademik::orderByDesc('kode_semester')->get(),
            'lecturers' => Dosen::orderBy('nama_dosen')->get(),
            'rooms' => Ruangan::orderBy('kode_ruangan')->get(),
        ];
    }

    private function rules(?KelasKuliah $record = null): array
    {
        return [
            'id_mk' => ['required', 'integer', 'exists:mata_kuliah,id_mk'],
            'kode_semester' => ['required', 'string', 'size:5', 'exists:tahun_akademik,kode_semester'],
            'nama_kelas' => ['required', 'string', 'max:10'],
            'id_dosen_pengampu' => ['required', 'integer', 'exists:dosen,id_dosen'],
            'id_ruangan' => ['nullable', 'integer', 'exists:ruangan,id_ruangan'],
            'hari' => ['required', Rule::in(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'])],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'kuota' => ['required', 'integer', 'min:1', 'max:500'],
        ];
    }

    private function findConflict(array $data, ?int $ignoreId = null): ?string
    {
        $base = KelasKuliah::query()
            ->where('kode_semester', $data['kode_semester'])
            ->where('hari', $data['hari'])
            ->where('jam_mulai', '<', $data['jam_selesai'])
            ->where('jam_selesai', '>', $data['jam_mulai']);

        if ($ignoreId !== null) {
            $base->where('id_kelas', '!=', $ignoreId);
        }

        $lecturerConflict = (clone $base)
            ->where('id_dosen_pengampu', $data['id_dosen_pengampu'])
            ->exists();

        if ($lecturerConflict) {
            return 'Dosen pengampu memiliki kelas lain pada hari dan waktu yang bertabrakan.';
        }

        if (!empty($data['id_ruangan'])) {
            $roomConflict = (clone $base)
                ->where('id_ruangan', $data['id_ruangan'])
                ->exists();

            if ($roomConflict) {
                return 'Ruangan sudah dipakai oleh kelas lain pada hari dan waktu tersebut.';
            }
        }

        return null;
    }
}
