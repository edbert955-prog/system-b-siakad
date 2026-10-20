<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JalurMasuk;
use App\Models\Mahasiswa;
use App\Models\Prodi;
use App\Models\StatusMahasiswa;
use App\Models\Dosen;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MahasiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = Mahasiswa::with(['prodi.fakultas', 'statusMahasiswa', 'dosenPa']);
        if ($request->filled('search')) {
            $term = trim($request->string('search')->toString());
            $query->where(function ($q) use ($term) {
                $q->where('npm', 'ilike', "%{$term}%")
                  ->orWhere('nama', 'ilike', "%{$term}%")
                  ->orWhere('email_kampus', 'ilike', "%{$term}%");
            });
        }
        if ($request->filled('id_prodi')) $query->where('id_prodi', $request->input('id_prodi'));
        if ($request->filled('id_status')) $query->where('id_status', $request->input('id_status'));
        if ($request->filled('angkatan')) $query->where('angkatan', $request->input('angkatan'));

        $students = $query->orderBy('nama')->paginate(15)->withQueryString();
        $programs = Prodi::orderBy('nama_prodi')->get();
        $statuses = StatusMahasiswa::orderBy('nama_status')->get();
        return view('admin.students', compact('students', 'programs', 'statuses'));
    }

    public function show(string $npm)
    {
        $mahasiswa = Mahasiswa::with([
            'prodi.fakultas', 'statusMahasiswa', 'jalurMasuk', 'dosenPa',
            'riwayatStatusSemester.statusMahasiswa',
            'krs.tahunAkademik', 'pembayaranUkt.tahunAkademik',
        ])->findOrFail($npm);
        return view('admin.student-profile', compact('mahasiswa'));
    }

    public function create()
    {
        return view('admin.form', [
            'type' => 'student', 'title' => 'Tambah Mahasiswa', 'record' => null,
            'action' => route('admin.students.store'), 'method' => 'POST',
            'programs' => Prodi::orderBy('nama_prodi')->get(),
            'statuses' => StatusMahasiswa::orderBy('nama_status')->get(),
            'pathways' => JalurMasuk::orderBy('nama_jalur')->get(),
            'lecturers' => Dosen::orderBy('nama_dosen')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        try {
            $data['semester_berjalan'] ??= 1;
            $data['ipk'] ??= 0;
            $data['total_sks_lulus'] ??= 0;
            Mahasiswa::create($data);
            return redirect()->route('admin.students.index')->with('success', 'Data mahasiswa berhasil ditambahkan.');
        } catch (QueryException $e) {
            return back()->withInput()->withErrors(['database' => 'Data gagal disimpan. Periksa NPM, email, NIK, dan relasi prodi/status.']);
        }
    }

    public function edit(string $npm)
    {
        $record = Mahasiswa::findOrFail($npm);
        return view('admin.form', [
            'type' => 'student', 'title' => 'Edit Mahasiswa', 'record' => $record,
            'action' => route('admin.students.update', $npm), 'method' => 'PATCH',
            'programs' => Prodi::orderBy('nama_prodi')->get(),
            'statuses' => StatusMahasiswa::orderBy('nama_status')->get(),
            'pathways' => JalurMasuk::orderBy('nama_jalur')->get(),
            'lecturers' => Dosen::orderBy('nama_dosen')->get(),
        ]);
    }

    public function update(Request $request, string $npm)
    {
        $record = Mahasiswa::findOrFail($npm);
        $rules = $this->rules($npm);
        $data = $request->validate($rules);
        unset($data['npm']);
        try {
            $record->fill($data)->save();
            return redirect()->route('admin.students.show', $record->npm)->with('success', 'Data mahasiswa berhasil diperbarui.');
        } catch (QueryException $e) {
            return back()->withInput()->withErrors(['database' => 'Data gagal diperbarui. Periksa data unik dan relasi foreign key.']);
        }
    }

    private function rules(?string $ignoreNpm = null): array
    {
        $npmRule = Rule::unique('mahasiswa', 'npm');
        $nikRule = Rule::unique('mahasiswa', 'nik');
        $emailRule = Rule::unique('mahasiswa', 'email_kampus');
        $rawNpmRule = Rule::unique('mahasiswa', 'raw_npm_siakadu');
        if ($ignoreNpm !== null) {
            $npmRule->ignore($ignoreNpm, 'npm');
            $nikRule->ignore($ignoreNpm, 'npm');
            $emailRule->ignore($ignoreNpm, 'npm');
            $rawNpmRule->ignore($ignoreNpm, 'npm');
        }
        return [
            'npm' => [$ignoreNpm ? 'sometimes' : 'required', 'string', 'max:15', $npmRule],
            'raw_npm_siakadu' => ['nullable','string','max:40',$rawNpmRule],
            'nik' => ['nullable','string','size:16',$nikRule],
            'nisn' => ['nullable','string','max:10'],
            'nama' => ['required','string','max:150'],
            'jenis_kelamin' => ['required', Rule::in(['Laki-laki','Perempuan'])],
            'tempat_lahir' => ['nullable','string','max:100'],
            'tgl_lahir' => ['nullable','date'],
            'agama' => ['nullable','string','max:20'],
            'email_kampus' => ['nullable','email','max:120',$emailRule],
            'email_pribadi' => ['nullable','email','max:120'],
            'no_hp' => ['nullable','string','max:20'],
            'alamat_domisili' => ['nullable','string'],
            'kode_pos' => ['nullable','string','max:10'],
            'id_prodi' => ['required','integer','exists:prodi,id_prodi'],
            'id_status' => ['required','integer','exists:status_mahasiswa,id_status'],
            'id_jalur' => ['nullable','integer','exists:jalur_masuk,id_jalur'],
            'id_dosen_pa' => ['nullable','integer','exists:dosen,id_dosen'],
            'angkatan' => ['required','integer','between:1960,2100'],
            'tgl_masuk' => ['required','date'],
            'tgl_lulus' => ['nullable','date'],
            'semester_berjalan' => ['nullable','integer','between:1,20'],
            'ipk' => ['nullable','numeric','between:0,4'],
            'total_sks_lulus' => ['nullable','integer','min:0'],
            'golongan_ukt' => ['nullable','integer','between:1,12'],
            'nominal_ukt' => ['nullable','numeric','min:0'],
        ];
    }
}
