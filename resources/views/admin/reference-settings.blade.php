@extends('layouts.admin')
@section('title', 'Data Referensi & Pengaturan')
@section('page-label', 'Data Referensi')

@section('content')
<div class="page-head">
    <div>
        <div class="eyebrow">Konfigurasi sistem</div>
        <h1>Data Referensi &amp; Pengaturan</h1>
        <p>Kelola status mahasiswa, jalur masuk, dan ruangan yang digunakan oleh formulir akademik.</p>
    </div>
</div>

<div class="stat-grid">
    <div class="stat-card"><div class="stat-label">Status Mahasiswa</div><div class="stat-value">{{ number_format($counts['statuses']) }}</div><div class="stat-note">Referensi status akademik</div></div>
    <div class="stat-card"><div class="stat-label">Jalur Masuk</div><div class="stat-value">{{ number_format($counts['pathways']) }}</div><div class="stat-note">Referensi penerimaan mahasiswa</div></div>
    <div class="stat-card"><div class="stat-label">Ruangan</div><div class="stat-value">{{ number_format($counts['rooms']) }}</div><div class="stat-note">Referensi ruang perkuliahan</div></div>
    <div class="stat-card"><div class="stat-label">Sumber Data</div><div class="stat-value" style="font-size:19px">PostgreSQL</div><div class="stat-note">Data dari database terhubung</div></div>
</div>

<div class="actions" style="margin-bottom:16px">
    <a class="btn {{ $resource === 'status-mahasiswa' ? 'btn-primary' : '' }}" href="{{ route('admin.reference-settings.index', ['type' => 'status-mahasiswa']) }}">Status Mahasiswa</a>
    <a class="btn {{ $resource === 'jalur-masuk' ? 'btn-primary' : '' }}" href="{{ route('admin.reference-settings.index', ['type' => 'jalur-masuk']) }}">Jalur Masuk</a>
    <a class="btn {{ $resource === 'ruangan' ? 'btn-primary' : '' }}" href="{{ route('admin.reference-settings.index', ['type' => 'ruangan']) }}">Ruangan</a>
</div>

<form class="filter-bar" method="GET" action="{{ route('admin.reference-settings.index') }}">
    <input type="hidden" name="type" value="{{ $resource }}">
    <div class="field field-grow">
        <label for="search">Cari {{ strtolower($config['label']) }}</label>
        <input id="search" name="search" value="{{ request('search') }}" placeholder="Cari berdasarkan nama atau kode">
    </div>
    <button class="btn btn-primary" type="submit">Cari</button>
    <a class="btn" href="{{ route('admin.reference-settings.index', ['type' => $resource]) }}">Reset</a>
</form>

<section class="card panel">
    <div class="panel-head">
        <div><h2>{{ $config['label'] }}</h2><div class="secondary-text">{{ number_format($records->total()) }} data ditemukan</div></div>
    </div>

    <div class="panel-body">
        <h3 style="margin:0 0 12px;font-size:14px">Tambah {{ $config['label'] }}</h3>
        <form method="POST" action="{{ route('admin.reference-settings.store', $resource) }}">
            @csrf
            @if($resource === 'status-mahasiswa')
                <div class="form-grid">
                    <div class="field">
                        <label for="kode_status">Kode Status</label>
                        <input id="kode_status" name="kode_status" maxlength="10" value="{{ old('kode_status') }}" placeholder="Contoh: AKT">
                    </div>
                    <div class="field">
                        <label for="nama_status">Nama Status *</label>
                        <input id="nama_status" name="nama_status" maxlength="50" value="{{ old('nama_status') }}" placeholder="Aktif" required>
                    </div>
                    <div class="field field-full">
                        <input type="hidden" name="is_aktif_kuliah" value="0">
                        <label class="checkbox-control" for="is_aktif_kuliah">
                            <input id="is_aktif_kuliah" type="checkbox" name="is_aktif_kuliah" value="1" @checked(old('is_aktif_kuliah'))>
                            <span>Status ini berarti mahasiswa aktif kuliah</span>
                        </label>
                    </div>
                </div>
            @elseif($resource === 'jalur-masuk')
                <div class="form-grid">
                    <div class="field">
                        <label for="kode_jalur">Kode Jalur *</label>
                        <input id="kode_jalur" name="kode_jalur" maxlength="10" value="{{ old('kode_jalur') }}" required>
                    </div>
                    <div class="field">
                        <label for="nama_jalur">Nama Jalur *</label>
                        <input id="nama_jalur" name="nama_jalur" maxlength="50" value="{{ old('nama_jalur') }}" placeholder="SNBP" required>
                    </div>
                </div>
            @else
                <div class="form-grid">
                    <div class="field">
                        <label for="kode_ruangan">Kode Ruangan *</label>
                        <input id="kode_ruangan" name="kode_ruangan" maxlength="20" value="{{ old('kode_ruangan') }}" required>
                    </div>
                    <div class="field">
                        <label for="nama_gedung">Nama Gedung *</label>
                        <input id="nama_gedung" name="nama_gedung" maxlength="100" value="{{ old('nama_gedung') }}" required>
                    </div>
                    <div class="field">
                        <label for="kapasitas">Kapasitas *</label>
                        <input type="number" id="kapasitas" name="kapasitas" min="1" value="{{ old('kapasitas', 40) }}" required>
                    </div>
                    <div class="field">
                        <label for="id_fakultas">Fakultas</label>
                        <select id="id_fakultas" name="id_fakultas">
                            <option value="">Belum dipetakan</option>
                            @foreach($faculties as $faculty)
                                <option value="{{ $faculty->id_fakultas }}" @selected((string)old('id_fakultas') === (string)$faculty->id_fakultas)>{{ $faculty->nama_fakultas }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif
            <div class="form-actions"><button class="btn btn-primary" type="submit">Tambah Data</button></div>
        </form>
    </div>

    <div class="table-wrap">
        <table class="data-table">
            <thead>
                @if($resource === 'status-mahasiswa')
                    <tr><th>Kode</th><th>Nama Status</th><th>Aktif Kuliah?</th><th>Ubah Data</th><th>Aksi</th></tr>
                @elseif($resource === 'jalur-masuk')
                    <tr><th>Kode</th><th>Nama Jalur</th><th>Ubah Data</th><th>Aksi</th></tr>
                @else
                    <tr><th>Kode Ruangan</th><th>Gedung</th><th>Kapasitas</th><th>Fakultas</th><th>Ubah Data</th><th>Aksi</th></tr>
                @endif
            </thead>
            <tbody>
            @forelse($records as $item)
                <tr>
                @if($resource === 'status-mahasiswa')
                    <td>{{ $item->kode_status ?: '—' }}</td>
                    <td><strong>{{ $item->nama_status }}</strong></td>
                    <td>@if($item->is_aktif_kuliah)<span class="badge badge-success">Aktif</span>@else<span class="badge">Tidak aktif</span>@endif</td>
                    <td>
                        <form method="POST" action="{{ route('admin.reference-settings.update', [$resource, $item->id_status]) }}" class="reference-inline-form">
                            @csrf @method('PATCH')
                            <input name="kode_status" value="{{ $item->kode_status }}" maxlength="10" aria-label="Kode status">
                            <input name="nama_status" value="{{ $item->nama_status }}" maxlength="50" required aria-label="Nama status">
                            <label class="checkbox-control"><input type="hidden" name="is_aktif_kuliah" value="0"><input type="checkbox" name="is_aktif_kuliah" value="1" @checked($item->is_aktif_kuliah)><span>Aktif</span></label>
                            <button class="btn btn-sm btn-primary" type="submit">Simpan</button>
                        </form>
                    </td>
                    <td><form method="POST" action="{{ route('admin.reference-settings.destroy', [$resource, $item->id_status]) }}" onsubmit="return confirm('Hapus status ini?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger" type="submit">Hapus</button></form></td>
                @elseif($resource === 'jalur-masuk')
                    <td>{{ $item->kode_jalur }}</td>
                    <td><strong>{{ $item->nama_jalur }}</strong></td>
                    <td>
                        <form method="POST" action="{{ route('admin.reference-settings.update', [$resource, $item->id_jalur]) }}" class="reference-inline-form">
                            @csrf @method('PATCH')
                            <input name="kode_jalur" value="{{ $item->kode_jalur }}" maxlength="10" required aria-label="Kode jalur">
                            <input name="nama_jalur" value="{{ $item->nama_jalur }}" maxlength="50" required aria-label="Nama jalur">
                            <button class="btn btn-sm btn-primary" type="submit">Simpan</button>
                        </form>
                    </td>
                    <td><form method="POST" action="{{ route('admin.reference-settings.destroy', [$resource, $item->id_jalur]) }}" onsubmit="return confirm('Hapus jalur masuk ini?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger" type="submit">Hapus</button></form></td>
                @else
                    <td>{{ $item->kode_ruangan }}</td>
                    <td><strong>{{ $item->nama_gedung }}</strong></td>
                    <td>{{ $item->kapasitas }}</td>
                    <td>{{ $item->fakultas?->nama_fakultas ?? '—' }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.reference-settings.update', [$resource, $item->id_ruangan]) }}" class="reference-inline-form">
                            @csrf @method('PATCH')
                            <input name="kode_ruangan" value="{{ $item->kode_ruangan }}" maxlength="20" required aria-label="Kode ruangan">
                            <input name="nama_gedung" value="{{ $item->nama_gedung }}" maxlength="100" required aria-label="Nama gedung">
                            <input type="number" name="kapasitas" min="1" value="{{ $item->kapasitas }}" required aria-label="Kapasitas">
                            <select name="id_fakultas" aria-label="Fakultas"><option value="">Belum dipetakan</option>@foreach($faculties as $faculty)<option value="{{ $faculty->id_fakultas }}" @selected((string)$item->id_fakultas === (string)$faculty->id_fakultas)>{{ $faculty->nama_fakultas }}</option>@endforeach</select>
                            <button class="btn btn-sm btn-primary" type="submit">Simpan</button>
                        </form>
                    </td>
                    <td><form method="POST" action="{{ route('admin.reference-settings.destroy', [$resource, $item->id_ruangan]) }}" onsubmit="return confirm('Hapus ruangan ini?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger" type="submit">Hapus</button></form></td>
                @endif
                </tr>
            @empty
                <tr><td colspan="6"><div class="empty"><strong>Belum ada data referensi</strong>Tambahkan data menggunakan formulir di atas.</div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination">
        <span>Menampilkan {{ $records->firstItem() ?? 0 }}–{{ $records->lastItem() ?? 0 }} dari {{ number_format($records->total()) }} data</span>
        {{ $records->links() }}
    </div>
</section>
@endsection
