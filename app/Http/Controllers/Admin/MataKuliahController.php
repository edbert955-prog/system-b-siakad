<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KelasKuliah;
use App\Models\MataKuliah;
use App\Models\Prodi;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MataKuliahController extends Controller
{
    public function index(Request $request)
    {
        $query=MataKuliah::with('prodi.fakultas');
        if($request->filled('search')) { $term=trim($request->string('search')->toString()); $query->where(fn($q)=>$q->where('kode_mk','ilike',"%{$term}%")->orWhere('nama_mk','ilike',"%{$term}%")); }
        if($request->filled('id_prodi')) $query->where('id_prodi',$request->input('id_prodi'));
        if($request->filled('semester_paket')) $query->where('semester_paket',$request->input('semester_paket'));
        if($request->filled('jenis_mk')) $query->where('jenis_mk',$request->input('jenis_mk'));
        $courses=$query->orderBy('kode_mk')->paginate(15)->withQueryString();
        $programs=Prodi::orderBy('nama_prodi')->get();
        $stats = [
            'total' => MataKuliah::count(),
            'theory' => (int) MataKuliah::sum('sks_teori'),
            'practicum' => (int) MataKuliah::sum('sks_praktikum'),
            'required' => MataKuliah::where('jenis_mk', 'Wajib')->count(),
            'elective' => MataKuliah::where('jenis_mk', 'Pilihan')->count(),
        ];
        return view('admin.courses',compact('courses','programs','stats'));
    }
    public function create(){ return view('admin.form',['type'=>'course','title'=>'Tambah Mata Kuliah','record'=>null,'action'=>route('admin.courses.store'),'method'=>'POST','programs'=>Prodi::orderBy('nama_prodi')->get()]); }
    public function store(Request $request)
    {
        $data=$request->validate($this->rules());
        if(((int)$data['sks_teori']+(int)$data['sks_praktikum'])!==(int)$data['total_sks']) return back()->withInput()->withErrors(['total_sks'=>'Total SKS harus sama dengan SKS teori ditambah SKS praktikum.']);
        try { MataKuliah::create($data); return redirect()->route('admin.courses.index')->with('success','Mata kuliah berhasil ditambahkan.'); }
        catch(QueryException $e){ return back()->withInput()->withErrors(['database'=>'Mata kuliah gagal disimpan. Periksa kode mata kuliah dan program studi.']); }
    }
    public function edit(int $id){ return view('admin.form',['type'=>'course','title'=>'Edit Mata Kuliah','record'=>MataKuliah::findOrFail($id),'action'=>route('admin.courses.update',$id),'method'=>'PATCH','programs'=>Prodi::orderBy('nama_prodi')->get()]); }
    public function update(Request $request,int $id)
    {
        $row=MataKuliah::findOrFail($id); $data=$request->validate($this->rules($row));
        if(((int)$data['sks_teori']+(int)$data['sks_praktikum'])!==(int)$data['total_sks']) return back()->withInput()->withErrors(['total_sks'=>'Total SKS harus sama dengan SKS teori ditambah SKS praktikum.']);
        try { $row->update($data); return redirect()->route('admin.courses.index')->with('success','Mata kuliah berhasil diperbarui.'); }
        catch(QueryException $e){ return back()->withInput()->withErrors(['database'=>'Mata kuliah gagal diperbarui. Periksa data unik dan relasi.']); }
    }
    public function destroy(int $id)
    {
        $row=MataKuliah::findOrFail($id);
        if($row->kelasKuliah()->exists()) return back()->withErrors(['delete'=>'Mata kuliah tidak dapat dihapus karena sudah digunakan pada kelas kuliah.']);
        try { $row->delete(); return back()->with('success','Mata kuliah berhasil dihapus.'); }
        catch(QueryException $e){ return back()->withErrors(['delete'=>'Mata kuliah masih digunakan oleh data lain.']); }
    }
    private function rules(?MataKuliah $record=null): array
    {
        $code=Rule::unique('mata_kuliah','kode_mk'); if($record) $code->ignore($record->id_mk,'id_mk');
        return ['kode_mk'=>['required','string','max:15',$code],'nama_mk'=>'required|string|max:150','id_prodi'=>'required|integer|exists:prodi,id_prodi','sks_teori'=>'required|integer|min:0','sks_praktikum'=>'required|integer|min:0','total_sks'=>'required|integer|min:1','semester_paket'=>'required|integer|between:1,8','jenis_mk'=>['required',Rule::in(['Wajib','Pilihan','MKWU'])]];
    }
}
