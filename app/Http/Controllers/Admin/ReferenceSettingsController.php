<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fakultas;
use App\Models\JalurMasuk;
use App\Models\Ruangan;
use App\Models\StatusMahasiswa;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReferenceSettingsController extends Controller
{
    private function config(string $resource): array
    {
        $map = [
            'status-mahasiswa' => [
                'model' => StatusMahasiswa::class,
                'pk' => 'id_status',
                'label' => 'Status Mahasiswa',
            ],
            'jalur-masuk' => [
                'model' => JalurMasuk::class,
                'pk' => 'id_jalur',
                'label' => 'Jalur Masuk',
            ],
            'ruangan' => [
                'model' => Ruangan::class,
                'pk' => 'id_ruangan',
                'label' => 'Ruangan',
            ],
        ];

        abort_unless(isset($map[$resource]), 404);
        return $map[$resource];
    }

    public function index(Request $request)
    {
        $resource = $request->input('type', 'status-mahasiswa');
        $config = $this->config($resource);
        $model = $config['model'];

        $query = $model::query();

        if ($resource === 'ruangan') {
            $query->with('fakultas');
        }

        if ($request->filled('search')) {
            $term = trim($request->string('search')->toString());

            if ($resource === 'status-mahasiswa') {
                $query->where(function ($q) use ($term) {
                    $q->where('nama_status', 'ilike', "%{$term}%")
                      ->orWhere('kode_status', 'ilike', "%{$term}%");
                });
            } elseif ($resource === 'jalur-masuk') {
                $query->where(function ($q) use ($term) {
                    $q->where('nama_jalur', 'ilike', "%{$term}%")
                      ->orWhere('kode_jalur', 'ilike', "%{$term}%");
                });
            } else {
                $query->where(function ($q) use ($term) {
                    $q->where('kode_ruangan', 'ilike', "%{$term}%")
                      ->orWhere('nama_gedung', 'ilike', "%{$term}%");
                });
            }
        }

        $orderBy = match ($resource) {
            'status-mahasiswa' => 'nama_status',
            'jalur-masuk' => 'nama_jalur',
            default => 'kode_ruangan',
        };

        $records = $query->orderBy($orderBy)->paginate(15)->withQueryString();
        $faculties = Fakultas::orderBy('nama_fakultas')->get();

        $counts = [
            'statuses' => StatusMahasiswa::count(),
            'pathways' => JalurMasuk::count(),
            'rooms' => Ruangan::count(),
        ];

        return view('admin.reference-settings', compact(
            'resource',
            'config',
            'records',
            'faculties',
            'counts'
        ));
    }

    public function store(Request $request, string $resource)
    {
        $config = $this->config($resource);
        $data = $request->validate($this->rules($resource));

        try {
            ($config['model'])::create($data);
            return redirect()->route('admin.reference-settings.index', ['type' => $resource])
                ->with('success', $config['label'] . ' berhasil ditambahkan.');
        } catch (QueryException $e) {
            report($e);
            return back()->withInput()->withErrors([
                'database' => 'Data gagal ditambahkan. Periksa kode unik dan relasi yang dipilih.',
            ]);
        }
    }

    public function update(Request $request, string $resource, string $id)
    {
        $config = $this->config($resource);
        $model = $config['model'];
        $record = $model::where($config['pk'], $id)->firstOrFail();

        $data = $request->validate($this->rules($resource, $record));

        try {
            $record->fill($data)->save();
            return redirect()->route('admin.reference-settings.index', ['type' => $resource])
                ->with('success', $config['label'] . ' berhasil diperbarui.');
        } catch (QueryException $e) {
            report($e);
            return back()->withInput()->withErrors([
                'database' => 'Data gagal diperbarui. Periksa kode dan relasi.',
            ]);
        }
    }

    public function destroy(string $resource, string $id)
    {
        $config = $this->config($resource);
        $model = $config['model'];
        $record = $model::where($config['pk'], $id)->firstOrFail();

        if ($resource === 'status-mahasiswa' &&
            ($record->mahasiswa()->exists() || $record->riwayatSemester()->exists())) {
            return back()->withErrors([
                'delete' => 'Status tidak bisa dihapus karena masih dipakai pada data mahasiswa atau riwayat semester.',
            ]);
        }

        if ($resource === 'jalur-masuk' && $record->mahasiswa()->exists()) {
            return back()->withErrors([
                'delete' => 'Jalur masuk tidak bisa dihapus karena masih dipakai oleh mahasiswa.',
            ]);
        }

        if ($resource === 'ruangan' && $record->kelasKuliah()->exists()) {
            return back()->withErrors([
                'delete' => 'Ruangan tidak bisa dihapus karena masih dipakai dalam jadwal kuliah.',
            ]);
        }

        try {
            $record->delete();
            return redirect()->route('admin.reference-settings.index', ['type' => $resource])
                ->with('success', $config['label'] . ' berhasil dihapus.');
        } catch (QueryException $e) {
            report($e);
            return back()->withErrors([
                'delete' => 'Data masih digunakan oleh entitas lain.',
            ]);
        }
    }

    private function rules(string $resource, ?object $record = null): array
    {
        if ($resource === 'status-mahasiswa') {
            $kode = Rule::unique('status_mahasiswa', 'kode_status');
            $nama = Rule::unique('status_mahasiswa', 'nama_status');

            if ($record) {
                $kode->ignore($record->id_status, 'id_status');
                $nama->ignore($record->id_status, 'id_status');
            }

            return [
                'kode_status' => ['nullable', 'string', 'max:10', $kode],
                'nama_status' => ['required', 'string', 'max:50', $nama],
                'is_aktif_kuliah' => ['required', 'boolean'],
            ];
        }

        if ($resource === 'jalur-masuk') {
            $kode = Rule::unique('jalur_masuk', 'kode_jalur');
            if ($record) {
                $kode->ignore($record->id_jalur, 'id_jalur');
            }

            return [
                'kode_jalur' => ['required', 'string', 'max:10', $kode],
                'nama_jalur' => ['required', 'string', 'max:50'],
            ];
        }

        $kode = Rule::unique('ruangan', 'kode_ruangan');
        if ($record) {
            $kode->ignore($record->id_ruangan, 'id_ruangan');
        }

        return [
            'kode_ruangan' => ['required', 'string', 'max:20', $kode],
            'nama_gedung' => ['required', 'string', 'max:100'],
            'kapasitas' => ['required', 'integer', 'min:1', 'max:1000'],
            'id_fakultas' => ['nullable', 'integer', 'exists:fakultas,id_fakultas'],
        ];
    }
}
