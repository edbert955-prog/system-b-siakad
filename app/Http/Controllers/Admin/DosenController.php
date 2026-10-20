<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\KelasKuliah;
use App\Models\Mahasiswa;
use App\Models\Prodi;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DosenController extends Controller
{
    public function index(Request $request)
    {
        $query = Dosen::with('prodiHomebase.fakultas')->withCount(['mahasiswaBimbingan', 'kelasDiampu']);
        if ($request->filled('search')) {
            $term = trim($request->string('search')->toString());
            $query->where(fn($q) => $q->where('nama_dosen','ilike',"%{$term}%")
                ->orWhere('nidn','ilike',"%{$term}%")
                ->orWhere('nip','ilike',"%{$term}%"));
        }
        if ($request->filled('id_prodi_homebase')) $query->where('id_prodi_homebase', $request->input('id_prodi_homebase'));
        $lecturers = $query->orderBy('nama_dosen')->paginate(15)->withQueryString();
        $programs = Prodi::orderBy('nama_prodi')->get();
        $stats = [
            'total_dosen' => Dosen::count(),
            'dosen_dengan_mahasiswa_pa' => (int) Mahasiswa::whereNotNull('id_dosen_pa')->distinct()->count('id_dosen_pa'),
            'total_kelas' => KelasKuliah::count(),
            'homebase_terpakai' => (int) Dosen::whereNotNull('id_prodi_homebase')->distinct()->count('id_prodi_homebase'),
        ];
        return view('admin.lecturers', compact('lecturers', 'programs', 'stats'));
    }

    public function create()
    {
        return view('admin.form', ['type'=>'lecturer','title'=>'Tambah Dosen','record'=>null,
            'action'=>route('admin.lecturers.store'),'method'=>'POST','programs'=>Prodi::orderBy('nama_prodi')->get()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        try { Dosen::create($data); return redirect()->route('admin.lecturers.index')->with('success','Data dosen berhasil ditambahkan.'); }
        catch (QueryException $e) { return back()->withInput()->withErrors(['database'=>'Data dosen gagal disimpan. Periksa NIDN, NIP, email, atau prodi homebase.']); }
    }

    public function edit(int $id)
    {
        return view('admin.form', ['type'=>'lecturer','title'=>'Edit Dosen','record'=>Dosen::findOrFail($id),
            'action'=>route('admin.lecturers.update',$id),'method'=>'PATCH','programs'=>Prodi::orderBy('nama_prodi')->get()]);
    }

    public function update(Request $request, int $id)
    {
        $record = Dosen::findOrFail($id);
        $data = $request->validate($this->rules($record));
        try { $record->fill($data)->save(); return redirect()->route('admin.lecturers.index')->with('success','Data dosen berhasil diperbarui.'); }
        catch (QueryException $e) { return back()->withInput()->withErrors(['database'=>'Data dosen gagal diperbarui. Periksa data unik dan relasi.']); }
    }

    public function destroy(int $id)
    {
        $record = Dosen::findOrFail($id);
        if ($record->mahasiswaBimbingan()->exists() || $record->kelasDiampu()->exists()) {
            return back()->withErrors(['delete'=>'Dosen tidak bisa dihapus karena masih menjadi DPA atau mengampu kelas.']);
        }
        try { $record->delete(); return back()->with('success','Data dosen berhasil dihapus.'); }
        catch (QueryException $e) { return back()->withErrors(['delete'=>'Dosen masih direferensikan oleh data lain.']); }
    }

    private function rules(?Dosen $record = null): array
    {
        $nidn = Rule::unique('dosen','nidn'); $nip = Rule::unique('dosen','nip'); $email = Rule::unique('dosen','email_dosen');
        if ($record) { $nidn->ignore($record->id_dosen,'id_dosen'); $nip->ignore($record->id_dosen,'id_dosen'); $email->ignore($record->id_dosen,'id_dosen'); }
        return [
            'nidn'=>['required','string','size:10',$nidn], 'nip'=>['nullable','string','max:20',$nip],
            'nama_dosen'=>['required','string','max:150'], 'gelar_depan'=>['nullable','string','max:30'],
            'gelar_belakang'=>['nullable','string','max:50'], 'jenis_kelamin'=>['required',Rule::in(['Laki-laki','Perempuan'])],
            'email_dosen'=>['nullable','email','max:120',$email], 'no_hp'=>['nullable','string','max:20'],
            'jabatan_fungsional'=>['nullable','string','max:50'], 'id_prodi_homebase'=>['required','integer','exists:prodi,id_prodi'],
        ];
    }
}
