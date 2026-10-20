<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Fakultas;
use App\Models\JalurMasuk;
use App\Models\KelasKuliah;
use App\Models\MataKuliah;
use App\Models\Prodi;
use App\Models\Ruangan;
use App\Models\StatusMahasiswa;
use App\Models\TahunAkademik;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MasterDataController extends Controller
{
    private function definitions(): array
    {
        return [
            'fakultas' => [
                'model' => Fakultas::class,
                'pk' => 'id_fakultas',
                'order' => 'nama_fakultas',
                'with' => [],
                'rules' => [
                    'kode_fakultas' => 'required|string|max:10',
                    'nama_fakultas' => 'required|string|max:100',
                ],
            ],

            'prodi' => [
                'model' => Prodi::class,
                'pk' => 'id_prodi',
                'order' => 'nama_prodi',
                'with' => ['fakultas'],
                'rules' => [
                    'kode_prodi' => 'nullable|string|max:15',
                    'id_fakultas' => 'nullable|integer|exists:fakultas,id_fakultas',
                    'nama_prodi' => 'required|string|max:150',
                    'jenjang' => 'required|in:D3,D4,S1,S2,S3,Profesi,Sp-1',
                    'akreditasi' => 'nullable|string|max:20',
                    'gelar_lulusan' => 'nullable|string|max:30',
                    'sks_minimal' => 'nullable|integer|min:1',
                ],
            ],

            'status-mahasiswa' => [
                'model' => StatusMahasiswa::class,
                'pk' => 'id_status',
                'order' => 'nama_status',
                'with' => [],
                'rules' => [
                    'kode_status' => 'nullable|string|max:10',
                    'nama_status' => 'required|string|max:50',
                    'is_aktif_kuliah' => 'required|boolean',
                ],
            ],

            'jalur-masuk' => [
                'model' => JalurMasuk::class,
                'pk' => 'id_jalur',
                'order' => 'nama_jalur',
                'with' => [],
                'rules' => [
                    'kode_jalur' => 'required|string|max:10',
                    'nama_jalur' => 'required|string|max:50',
                ],
            ],

            'dosen' => [
                'model' => Dosen::class,
                'pk' => 'id_dosen',
                'order' => 'nama_dosen',
                'with' => ['prodiHomebase'],
                'rules' => [
                    'nidn' => 'required|string|size:10',
                    'nip' => 'nullable|string|max:20',
                    'nama_dosen' => 'required|string|max:150',
                    'gelar_depan' => 'nullable|string|max:30',
                    'gelar_belakang' => 'nullable|string|max:50',
                    'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
                    'email_dosen' => 'nullable|email|max:120',
                    'no_hp' => 'nullable|string|max:20',
                    'jabatan_fungsional' => 'nullable|string|max:50',
                    'id_prodi_homebase' => 'required|integer|exists:prodi,id_prodi',
                ],
            ],

            'tahun-akademik' => [
                'model' => TahunAkademik::class,
                'pk' => 'kode_semester',
                'order' => 'kode_semester',
                'with' => [],
                'rules' => [
                    'kode_semester' => 'required|string|size:5',
                    'nama_semester' => 'required|string|max:50',
                    'tahun_ajaran' => 'required|string|max:9',
                    'jenis_semester' => 'required|in:Ganjil,Genap,Antara',
                    'tgl_mulai_krs' => 'required|date',
                    'tgl_selesai_krs' => 'required|date|after_or_equal:tgl_mulai_krs',
                    'is_aktif' => 'required|boolean',
                ],
            ],

            'ruangan' => [
                'model' => Ruangan::class,
                'pk' => 'id_ruangan',
                'order' => 'kode_ruangan',
                'with' => ['fakultas'],
                'rules' => [
                    'kode_ruangan' => 'required|string|max:20',
                    'nama_gedung' => 'required|string|max:100',
                    'kapasitas' => 'required|integer|min:1',
                    'id_fakultas' => 'nullable|integer|exists:fakultas,id_fakultas',
                ],
            ],

            'mata-kuliah' => [
                'model' => MataKuliah::class,
                'pk' => 'id_mk',
                'order' => 'kode_mk',
                'with' => ['prodi'],
                'rules' => [
                    'kode_mk' => 'required|string|max:15',
                    'nama_mk' => 'required|string|max:150',
                    'id_prodi' => 'required|integer|exists:prodi,id_prodi',
                    'sks_teori' => 'required|integer|min:0',
                    'sks_praktikum' => 'required|integer|min:0',
                    'total_sks' => 'required|integer|min:1',
                    'semester_paket' => 'required|integer|between:1,8',
                    'jenis_mk' => 'required|in:Wajib,Pilihan,MKWU',
                ],
            ],

            'kelas-kuliah' => [
                'model' => KelasKuliah::class,
                'pk' => 'id_kelas',
                'order' => 'id_kelas',
                'with' => [
                    'mataKuliah',
                    'tahunAkademik',
                    'dosenPengampu',
                    'ruangan',
                ],
                'rules' => [
                    'id_mk' => 'required|integer|exists:mata_kuliah,id_mk',
                    'kode_semester' => 'required|string|size:5|exists:tahun_akademik,kode_semester',
                    'nama_kelas' => 'required|string|max:10',
                    'id_dosen_pengampu' => 'required|integer|exists:dosen,id_dosen',
                    'id_ruangan' => 'nullable|integer|exists:ruangan,id_ruangan',
                    'hari' => 'required|string|max:10',
                    'jam_mulai' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
                    'jam_selesai' => ['required', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
                    'kuota' => 'required|integer|min:1',
                ],
            ],
        ];
    }

    private function definition(string $resource): array
    {
        $definitions = $this->definitions();

        abort_unless(
            isset($definitions[$resource]),
            404,
            'Jenis master data tidak tersedia.'
        );

        return $definitions[$resource];
    }

    public function index(Request $request, string $resource)
    {
        $definition = $this->definition($resource);
        $model = $definition['model'];

        $query = $model::query();

        if (!empty($definition['with'])) {
            $query->with($definition['with']);
        }

        $perPage = min(
            max($request->integer('per_page', 20), 1),
            100
        );

        return response()->json([
            'success' => true,
            'data' => $query
                ->orderBy($definition['order'])
                ->paginate($perPage),
        ]);
    }

    public function show(string $resource, string $id)
    {
        $definition = $this->definition($resource);
        $model = $definition['model'];

        $record = $model::query()
            ->with($definition['with'])
            ->where($definition['pk'], $id)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $record,
        ]);
    }

    public function store(Request $request, string $resource)
    {
        $definition = $this->definition($resource);
        $model = $definition['model'];

        $data = $request->validate($definition['rules']);

        try {
            $record = DB::transaction(function () use (
                $resource,
                $model,
                $data
            ) {
                if (
                    $resource === 'tahun-akademik'
                    && $data['is_aktif']
                ) {
                    TahunAkademik::where('is_aktif', true)
                        ->update(['is_aktif' => false]);
                }

                return $model::create($data);
            });

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil ditambahkan.',
                'data' => $record,
            ], 201);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data gagal ditambahkan. Periksa data duplikat dan relasi foreign key.',
            ], 409);
        }
    }

    public function update(
        Request $request,
        string $resource,
        string $id
    ) {
        $definition = $this->definition($resource);
        $model = $definition['model'];

        $rules = [];

        foreach ($definition['rules'] as $field => $rule) {
            if ($resource === 'tahun-akademik' && $field === 'kode_semester') {
                continue;
            }

            $rules[$field] = is_array($rule)
                ? array_merge(['sometimes'], $rule)
                : array_merge(['sometimes'], explode('|', $rule));
        }

        $data = $request->validate($rules);

        try {
            $record = DB::transaction(function () use (
                $resource,
                $definition,
                $model,
                $id,
                $data
            ) {
                $record = $model::where(
                    $definition['pk'],
                    $id
                )->firstOrFail();

                if (
                    $resource === 'tahun-akademik'
                    && ($data['is_aktif'] ?? false)
                ) {
                    TahunAkademik::where('is_aktif', true)
                        ->where('kode_semester', '!=', $id)
                        ->update(['is_aktif' => false]);
                }

                $record->fill($data);
                $record->save();

                return $record->refresh();
            });

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diperbarui.',
                'data' => $record,
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data gagal diperbarui. Periksa duplikasi dan relasi foreign key.',
            ], 409);
        }
    }

    public function destroy(string $resource, string $id)
    {
        $definition = $this->definition($resource);
        $model = $definition['model'];

        $record = $model::where(
            $definition['pk'],
            $id
        )->firstOrFail();

        try {
            $record->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus.',
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak dapat dihapus karena masih digunakan oleh data akademik lain.',
            ], 409);
        }
    }
}