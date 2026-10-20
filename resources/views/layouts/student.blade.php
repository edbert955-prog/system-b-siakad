<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Portal Mahasiswa') · SIAKADU</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/siakadu-admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/siakadu-portal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/siakadu-table-fix.css') }}">
    <link rel="stylesheet" href="{{ asset('css/siakadu-navigation-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('css/siakadu-ui-controls-fix.css') }}">
    <script src="{{ asset('js/siakadu-table-scroll.js') }}" defer></script>
    @stack('styles')
</head>
<body>
@php
    $portalUser = auth()->user();
    $portalStudent = $portalUser?->mahasiswa;
    $activeAcademicTerm = \App\Models\TahunAkademik::where('is_aktif', true)->first();
@endphp
<div class="app-shell">
    <div class="mobile-overlay" id="mobileOverlay" onclick="toggleSidebar(false)"></div>
    <aside class="sidebar student-sidebar" id="sidebar">
        <a href="{{ route('student.dashboard') }}" class="brand">
            <span class="brand-mark"><x-siakadu-icon name="landmark" /></span>
            <span>
                <span class="brand-title">SIAKADU</span>
                <span class="brand-subtitle" style="display:block">Portal Mahasiswa</span>
            </span>
        </a>
        <div class="sidebar-role">● &nbsp; {{ $portalStudent?->prodi?->nama_prodi ?? 'Mahasiswa' }}</div>
        @include('partials.student-navigation')
        <div class="sidebar-bottom">
            <a class="sidebar-account-link {{ request()->routeIs('student.account-settings*') ? 'active' : '' }}" href="{{ route('student.account-settings') }}">
                <span aria-hidden="true"><x-siakadu-icon name="settings" /></span><span>Pengaturan Akun</span>
            </a>
            <div class="user-mini">
                <div class="user-avatar">{{ strtoupper(substr($portalStudent?->nama ?? $portalUser?->name ?? 'M', 0, 1)) }}</div>
                <div style="min-width:0;flex:1">
                    <div class="user-mini-name">{{ $portalStudent?->nama ?? $portalUser?->name ?? 'Mahasiswa' }}</div>
                    <div class="user-mini-role">{{ $portalStudent?->npm ?? $portalUser?->nomor_induk }}</div>
                </div>
            </div>
            <form class="logout-form" method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="logout-button" type="submit"><x-siakadu-icon name="log-out" /><span>Keluar</span></button>
            </form>
        </div>
    </aside>
    <main class="main-area">
        <header class="topbar">
            <div style="display:flex;align-items:center;gap:12px;min-width:0">
                <button class="mobile-menu" type="button" aria-label="Buka navigasi" onclick="toggleSidebar()">☰</button>
                <div class="crumbs"><span>SIAKADU</span><span>›</span><strong>@yield('page-label', 'Dashboard')</strong></div>
            </div>
            <div class="topbar-right">
                <span class="term-pill"><span class="term-dot"></span>{{ $activeAcademicTerm?->nama_semester ?? 'Semester belum ditentukan' }}{{ $activeAcademicTerm?->tahun_ajaran ? ' · '.$activeAcademicTerm->tahun_ajaran : '' }}</span>
                <span class="topbar-user">{{ $portalStudent?->nama ?? $portalUser?->name }}</span>
            </div>
        </header>
        <section class="content">
            @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
            @if($errors->any())
                <div class="alert alert-error"><strong>Periksa kembali:</strong><ul style="margin:6px 0 0;padding-left:19px">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif
            @yield('content')
        </section>
        <footer class="footer-note">SIAKADU · Portal Mahasiswa · Informasi akademik berdasarkan data yang tersimpan di sistem.</footer>
    </main>
</div>
<script>
function toggleSidebar(force) {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('mobileOverlay');
    const show = typeof force === 'boolean' ? force : !sidebar.classList.contains('open');
    sidebar.classList.toggle('open', show);
    overlay.classList.toggle('show', show);
}
</script>
@stack('scripts')
</body>
</html>
