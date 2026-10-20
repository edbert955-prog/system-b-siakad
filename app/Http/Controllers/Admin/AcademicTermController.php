<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TahunAkademik;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AcademicTermController extends Controller
{
    public function index(Request $request)
    {
        $query = TahunAkademik::query();

        if ($request->filled('search')) {
            $term = trim($request->string('search')->toString());
            $query->where(function ($q) use ($term) {
                $q->where('kode_semester', 'ilike', "%{$term}%")
                  ->orWhere('nama_semester', 'ilike', "%{$term}%")
                  ->orWhere('tahun_ajaran', 'ilike', "%{$term}%");
            });
        }

        if ($request->filled('jenis_semester')) {
            $query->where('jenis_semester', $request->input('jenis_semester'));
        }

        if ($request->filled('status')) {
            $query->where('is_aktif', $request->input('status') === 'aktif');
        }

        $terms = $query->orderByDesc('kode_semester')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => TahunAkademik::count(),
            'active' => TahunAkademik::where('is_aktif', true)->count(),
            'odd' => TahunAkademik::where('jenis_semester', 'Ganjil')->count(),
            'even' => TahunAkademik::where('jenis_semester', 'Genap')->count(),
        ];

        return view('admin.academic-terms', compact('terms', 'stats'));
    }

    public function create()
    {
        return view('admin.term-form', [
            'title' => 'Tambah Periode Akademik',
            'record' => null,
            'action' => route('admin.academic-terms.store'),
            'method' => 'POST',
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $makeActive = (bool) ($data['is_aktif'] ?? false);

        try {
            DB::transaction(function () use ($data, $makeActive) {
                if ($makeActive) {
                    TahunAkademik::where('is_aktif', true)->update(['is_aktif' => false]);
                }
                TahunAkademik::create($data);
            });

            return redirect()->route('admin.academic-terms.index')
                ->with('success', 'Periode akademik berhasil ditambahkan.');
        } catch (QueryException $e) {
            report($e);
            return back()->withInput()->withErrors([
                'database' => 'Periode gagal disimpan. Periksa kode semester dan pastikan tidak ada kode duplikat.',
            ]);
        }
    }

    public function edit(string $kode)
    {
        return view('admin.term-form', [
            'title' => 'Edit Periode Akademik',
            'record' => TahunAkademik::findOrFail($kode),
            'action' => route('admin.academic-terms.update', $kode),
            'method' => 'PATCH',
        ]);
    }

    public function update(Request $request, string $kode)
    {
        $record = TahunAkademik::findOrFail($kode);
        $data = $request->validate($this->rules($kode));
        $makeActive = (bool) ($data['is_aktif'] ?? false);

        try {
            DB::transaction(function () use ($record, $data, $makeActive) {
                if ($makeActive) {
                    TahunAkademik::where('is_aktif', true)
                        ->where('kode_semester', '!=', $record->kode_semester)
                        ->update(['is_aktif' => false]);
                }
                unset($data['kode_semester']);
                $record->fill($data)->save();
            });

            return redirect()->route('admin.academic-terms.index')
                ->with('success', 'Periode akademik berhasil diperbarui.');
        } catch (QueryException $e) {
            report($e);
            return back()->withInput()->withErrors([
                'database' => 'Periode gagal diperbarui. Periksa kembali data yang dimasukkan.',
            ]);
        }
    }

    public function destroy(string $kode)
    {
        $record = TahunAkademik::findOrFail($kode);

        if ($record->krs()->exists() || $record->kelasKuliah()->exists() || $record->pembayaranUkt()->exists()) {
            return back()->withErrors([
                'delete' => 'Periode tidak dapat dihapus karena sudah direferensikan oleh KRS, kelas, atau pembayaran.',
            ]);
        }

        try {
            $record->delete();
            return back()->with('success', 'Periode akademik berhasil dihapus.');
        } catch (QueryException $e) {
            report($e);
            return back()->withErrors(['delete' => 'Periode masih digunakan oleh data lain.']);
        }
    }

    private function rules(?string $ignoreKode = null): array
    {
        $kodeRule = Rule::unique('tahun_akademik', 'kode_semester');
        if ($ignoreKode !== null) {
            $kodeRule->ignore($ignoreKode, 'kode_semester');
        }

        return [
            'kode_semester' => [$ignoreKode ? 'sometimes' : 'required', 'string', 'size:5', $kodeRule],
            'nama_semester' => ['required', 'string', 'max:50'],
            'tahun_ajaran' => ['required', 'string', 'max:9'],
            'jenis_semester' => ['required', Rule::in(['Ganjil', 'Genap', 'Antara'])],
            'tgl_mulai_krs' => ['required', 'date'],
            'tgl_selesai_krs' => ['required', 'date', 'after_or_equal:tgl_mulai_krs'],
            'is_aktif' => ['nullable', 'boolean'],
        ];
    }
}
