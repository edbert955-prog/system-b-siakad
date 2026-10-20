# SIAKADU Sidebar Icon Consistency Patch

Patch ini mengganti glyph/emoji pada sidebar dengan ikon SVG garis yang konsisten, mengikuti gaya ikon pada desain Google Stitch. Tidak mengubah database, migration, controller, atau API.

## 1. Salin file
Salin isi folder `resources` dan `public` dari patch ini ke root project Laravel:
`D:\System_B\system-b-siakad`

File baru:
- `resources/views/components/siakadu-icon.blade.php`
- `resources/views/partials/admin-navigation.blade.php`
- `resources/views/partials/student-navigation.blade.php`
- `public/css/siakadu-navigation-icons.css`

## 2. Update layout Admin
Buka `resources/views/layouts/admin.blade.php`.

Di dalam `<head>`, tambahkan setelah stylesheet yang sudah ada:
```blade
<link rel="stylesheet" href="{{ asset('css/siakadu-navigation-icons.css') }}">
```

Cari seluruh blok `<nav class="nav-section" ...> ... </nav>` sidebar admin dan ganti seluruh blok itu dengan:
```blade
@include('partials.admin-navigation')
```

Di brand sidebar, ganti huruf `S` di dalam `brand-mark`:
```blade
<span class="brand-mark">S</span>
```
menjadi:
```blade
<span class="brand-mark"><x-siakadu-icon name="landmark" /></span>
```

Di tautan Pengaturan Akun, ganti simbol gear `⚙` dengan:
```blade
<x-siakadu-icon name="settings" />
```
Di tombol Keluar, ganti `↪ &nbsp; Keluar` dengan:
```blade
<x-siakadu-icon name="log-out" /><span>Keluar</span>
```

## 3. Update layout Mahasiswa
Buka `resources/views/layouts/student.blade.php`.

Di dalam `<head>`, tambahkan:
```blade
<link rel="stylesheet" href="{{ asset('css/siakadu-navigation-icons.css') }}">
```

Ganti blok `<nav class="nav-section" ...> ... </nav>` dengan:
```blade
@include('partials.student-navigation')
```

Ganti huruf `S` di dalam `brand-mark` dengan:
```blade
<span class="brand-mark"><x-siakadu-icon name="landmark" /></span>
```

Ganti simbol `⚙` pada link Pengaturan Akun dengan:
```blade
<x-siakadu-icon name="settings" />
```
Ganti `↪ &nbsp; Keluar` pada tombol Keluar dengan:
```blade
<x-siakadu-icon name="log-out" /><span>Keluar</span>
```

## 4. Tautan Transkrip Akademik
Pada nav mahasiswa, tautan Transkrip Akademik menuju `/student/khs?view=transcript#transcript`. Agar browser menggulir ke bagian transkrip, tambahkan `id="transcript"` ke `<section>` atau kontainer judul yang membungkus tabel transkrip pada:
`resources/views/student/khs.blade.php`

Contoh:
```blade
<section class="card panel" id="transcript">
```
Gunakan ID tersebut pada section transkrip yang memang sudah ada; jangan membuat tabel duplikat.

## 5. Bersihkan cache
Di terminal VS Code:
```powershell
php artisan optimize:clear
```
Lalu muat ulang browser dengan `Ctrl + F5`.

Jika layout memiliki edit tambahan dari projectmu, jangan menimpa seluruh file layout; cukup ganti blok nav dan menambah stylesheet seperti langkah di atas.
