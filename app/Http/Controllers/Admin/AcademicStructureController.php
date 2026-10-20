<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fakultas;
use App\Models\Mahasiswa;
use App\Models\Prodi;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AcademicStructureController extends Controller
{
    public function index(Request $request)
    {
        $search = trim($request->string('search')->toString());
        $facultyFilter = $request->input('id_fakultas');
        $levelFilter = $request->input('jenjang');

        // Fakultas ditampilkan utuh; kolom pencarian menyaring tabel program studi.
        $faculties = Fakultas::withCount('prodi')
            ->orderBy('nama_fakultas')
            ->get();

        $programQuery = Prodi::with('fakultas')
            ->withCount(['mahasiswa', 'dosen', 'mataKuliah'])
            ->orderBy('nama_prodi');
        if ($search !== '') {
            $programQuery->where(function ($q) use ($search) {
                $q->where('nama_prodi', 'ilike', "%{$search}%")
                  ->orWhere('kode_prodi', 'ilike', "%{$search}%");
            });
        }
        if ($facultyFilter) {
            $programQuery->where('id_fakultas', $facultyFilter);
        }
        if ($levelFilter) {
            $programQuery->where('jenjang', $levelFilter);
        }
        $programs = $programQuery->paginate(12)->withQueryString();
        $allFaculties = Fakultas::orderBy('nama_fakultas')->get();
        $stats = [
            'faculties' => Fakultas::count(),
            'programs' => Prodi::count(),
            'students' => \App\Models\Mahasiswa::count(),
        ];

        return view('admin.academic-structure', compact(
            'faculties', 'programs', 'allFaculties', 'stats',
            'search', 'facultyFilter', 'levelFilter'
        ));
    }

    public function createFaculty() { return view('admin.form',['type'=>'faculty','title'=>'Tambah Fakultas','record'=>null,'action'=>route('admin.faculties.store'),'method'=>'POST']); }
    public function editFaculty(int $id) { return view('admin.form',['type'=>'faculty','title'=>'Edit Fakultas','record'=>Fakultas::findOrFail($id),'action'=>route('admin.faculties.update',$id),'method'=>'PATCH']); }
    public function storeFaculty(Request $request)
    {
        $data=$request->validate(['kode_fakultas'=>'required|string|max:10|unique:fakultas,kode_fakultas','nama_fakultas'=>'required|string|max:100']);
        try { Fakultas::create($data); return redirect()->route('admin.academic-structure')->with('success','Fakultas berhasil ditambahkan.'); }
        catch(QueryException $e){ return back()->withInput()->withErrors(['database'=>'Fakultas gagal disimpan. Periksa kode fakultas.']); }
    }
    public function updateFaculty(Request $request,int $id)
    {
        $row=Fakultas::findOrFail($id); $data=$request->validate(['kode_fakultas'=>['required','string','max:10',Rule::unique('fakultas','kode_fakultas')->ignore($id,'id_fakultas')],'nama_fakultas'=>'required|string|max:100']);
        try { $row->update($data); return redirect()->route('admin.academic-structure')->with('success','Fakultas berhasil diperbarui.'); }
        catch(QueryException $e){ return back()->withInput()->withErrors(['database'=>'Fakultas gagal diperbarui.']); }
    }
    public function destroyFaculty(int $id)
    {
        $row=Fakultas::findOrFail($id);
        if($row->prodi()->exists()) return back()->withErrors(['delete'=>'Fakultas tidak dapat dihapus karena masih memiliki program studi.']);
        try { $row->delete(); return back()->with('success','Fakultas berhasil dihapus.'); }
        catch(QueryException $e){ return back()->withErrors(['delete'=>'Fakultas masih digunakan oleh data lain.']); }
    }

    public function createProgram() { return view('admin.form',['type'=>'program','title'=>'Tambah Program Studi','record'=>null,'action'=>route('admin.programs.store'),'method'=>'POST','faculties'=>Fakultas::orderBy('nama_fakultas')->get()]); }
    public function editProgram(int $id) { return view('admin.form',['type'=>'program','title'=>'Edit Program Studi','record'=>Prodi::findOrFail($id),'action'=>route('admin.programs.update',$id),'method'=>'PATCH','faculties'=>Fakultas::orderBy('nama_fakultas')->get()]); }
    public function storeProgram(Request $request)
    {
        $data=$request->validate(['kode_prodi'=>'nullable|string|max:15|unique:prodi,kode_prodi','id_fakultas'=>'required|integer|exists:fakultas,id_fakultas','nama_prodi'=>'required|string|max:150','jenjang'=>['required',Rule::in(['D3','D4','S1','S2','S3','Profesi','Sp-1'])],'akreditasi'=>'nullable|string|max:20','gelar_lulusan'=>'nullable|string|max:30','sks_minimal'=>'required|integer|min:1']);
        try { Prodi::create($data); return redirect()->route('admin.academic-structure')->with('success','Program studi berhasil ditambahkan.'); }
        catch(QueryException $e){ return back()->withInput()->withErrors(['database'=>'Program studi gagal disimpan. Periksa kode dan fakultas.']); }
    }
    public function updateProgram(Request $request,int $id)
    {
        $row=Prodi::findOrFail($id); $data=$request->validate(['kode_prodi'=>['nullable','string','max:15',Rule::unique('prodi','kode_prodi')->ignore($id,'id_prodi')],'id_fakultas'=>'required|integer|exists:fakultas,id_fakultas','nama_prodi'=>'required|string|max:150','jenjang'=>['required',Rule::in(['D3','D4','S1','S2','S3','Profesi','Sp-1'])],'akreditasi'=>'nullable|string|max:20','gelar_lulusan'=>'nullable|string|max:30','sks_minimal'=>'required|integer|min:1']);
        try { $row->update($data); return redirect()->route('admin.academic-structure')->with('success','Program studi berhasil diperbarui.'); }
        catch(QueryException $e){ return back()->withInput()->withErrors(['database'=>'Program studi gagal diperbarui.']); }
    }
    public function destroyProgram(int $id)
    {
        $row=Prodi::findOrFail($id);
        if($row->mahasiswa()->exists() || $row->dosen()->exists() || $row->mataKuliah()->exists()) return back()->withErrors(['delete'=>'Program studi tidak dapat dihapus karena masih direferensikan data akademik.']);
        try { $row->delete(); return back()->with('success','Program studi berhasil dihapus.'); }
        catch(QueryException $e){ return back()->withErrors(['delete'=>'Program studi masih digunakan oleh data lain.']); }
    }
}
