<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MahasiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = Mahasiswa::with([
            'prodi.fakultas',
            'statusMahasiswa',
            'dosenPa',
        ]);

        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->where('npm', 'ilike', "%{$search}%")
                    ->orWhere('nama', 'ilike', "%{$search}%")
                    ->orWhere('email_kampus', 'ilike', "%{$search}%");
            });
        }

        if ($request->filled('id_prodi')) {
            $query->where('id_prodi', $request->integer('id_prodi'));
        }

        if ($request->filled('angkatan')) {
            $query->where('angkatan', $request->integer('angkatan'));
        }

        if ($request->filled('id_status')) {
            $query->where('id_status', $request->integer('id_status'));
        }

        $perPage = min(
            max($request->integer('per_page', 20), 1),
            100
        );

        return response()->json([
            'success' => true,
            'data' => $query
                ->orderBy('npm')
                ->paginate($perPage),
        ]);
    }

    public function show(string $npm)
    {
        $mahasiswa = Mahasiswa::with([
            'prodi.fakultas',
            'statusMahasiswa',
            'jalurMasuk',
            'dosenPa',
            'orangTua',
            'riwayatStatusSemester.statusMahasiswa',
        ])->findOrFail($npm);

        return response()->json([
            'success' => true,
            'data' => $mahasiswa,
        ]);
    }

    private function validationRules(
        ?Mahasiswa $mahasiswa = null
    ): array {
        $creating = $mahasiswa === null;

        $required = $creating
            ? 'required|'
            : 'sometimes|required|';

        $optional = $creating
            ? 'nullable|'
            : 'sometimes|nullable|';

        $unique = fn ($column) => $mahasiswa
            ? Rule::unique('mahasiswa', $column)
                ->ignore($mahasiswa->npm, 'npm')
            : Rule::unique('mahasiswa', $column);

        $rules = [
            'npm' => [
                $required . 'string|max:15',
                $unique('npm'),
            ],

            'raw_npm_siakadu' => [
                $optional . 'string|max:40',
                $unique('raw_npm_siakadu'),
            ],

            'nik' => [
                $optional . 'string|size:16',
                $unique('nik'),
            ],

            'nisn' => $optional . 'string|size:10',
            'nama' => $required . 'string|max:150',

            'jenis_kelamin' => $required . 'in:Laki-laki,Perempuan',

            'tempat_lahir' => $optional . 'string|max:100',
            'tgl_lahir' => $optional . 'date',
            'agama' => $optional . 'string|max:20',

            'email_kampus' => [
                $optional . 'email|max:120',
                $unique('email_kampus'),
            ],

            'email_pribadi' => $optional . 'email|max:120',
            'no_hp' => $optional . 'string|max:20',
            'alamat_domisili' => $optional . 'string',
            'kode_pos' => $optional . 'string|max:10',

            'id_prodi' => $required . 'integer|exists:prodi,id_prodi',

            'id_status' => $required .
                'integer|exists:status_mahasiswa,id_status',

            'id_jalur' => $optional .
                'integer|exists:jalur_masuk,id_jalur',

            'id_dosen_pa' => $optional .
                'integer|exists:dosen,id_dosen',

            'angkatan' => $required . 'integer|between:1960,2100',
            'tgl_masuk' => $required . 'date',
            'tgl_lulus' => $optional . 'date',

            'semester_berjalan' => $optional . 'integer|between:1,20',
            'ipk' => $optional . 'numeric|between:0,4',
            'total_sks_lulus' => $optional . 'integer|min:0',
            'golongan_ukt' => $optional . 'integer|between:1,12',
            'nominal_ukt' => $optional . 'numeric|min:0',
        ];

        if (!$creating) {
            unset($rules['npm']);
        }

        return $rules;
    }

    public function store(Request $request)
    {
        $data = $request->validate(
            $this->validationRules()
        );

        $mahasiswa = Mahasiswa::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Mahasiswa berhasil ditambahkan.',
            'data' => $mahasiswa,
        ], 201);
    }

    public function update(Request $request, string $npm)
    {
        $mahasiswa = Mahasiswa::findOrFail($npm);

        $data = $request->validate(
            $this->validationRules($mahasiswa)
        );

        $mahasiswa->fill($data);
        $mahasiswa->save();

        return response()->json([
            'success' => true,
            'message' => 'Data mahasiswa berhasil diperbarui.',
            'data' => $mahasiswa->fresh([
                'prodi',
                'statusMahasiswa',
                'dosenPa',
            ]),
        ]);
    }
}