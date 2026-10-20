<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SIAKADU') · Sistem Informasi Akademik</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/siakadu-admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/siakadu-portal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/siakadu-table-fix.css') }}">
    <link rel="stylesheet" href="{{ asset('css/siakadu-ui-controls-fix.css') }}">
    <link rel="stylesheet" href="{{ asset('css/siakadu-navigation-icons.css') }}">
    <script src="{{ asset('js/siakadu-table-scroll.js') }}" defer></script>
    @stack('styles')
</head>
<body>
<div class="app-shell">
    <div class="mobile-overlay" id="mobileOverlay" onclick="toggleSidebar(false)"></div>
    <aside class="sidebar" id="sidebar">
        <a href="{{ route('admin.dashboard') }}" class="brand">
            <span class="brand-mark"><x-siakadu-icon name="landmark" /></span>
            <span><span class="brand-title">SIAKADU</span><span class="brand-subtitle" style="display:block">Academic Services</span></span>
        </a>
        <div class="sidebar-role">● &nbsp; Admin / Staf Akademik</div>
        @include('partials.admin-navigation')
        <div class="sidebar-bottom">
            <div class="user-mini">
                <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}</div>
                <div style="min-width:0;flex:1">
                    <div class="user-mini-name">{{ auth()->user()->name ?? 'Admin' }}</div>
                    <div class="user-mini-role">{{ auth()->user()->role ?? 'Admin' }}</div>
                </div>
            </div>
            <a class="sidebar-account-link {{ request()->routeIs('admin.account-settings*') ? 'active' : '' }}"
               href="{{ route('admin.account-settings') }}">
                <span aria-hidden="true"><x-siakadu-icon name="settings" /></span><span>Pengaturan Akun</span>
            </a>
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
                @php($layoutActiveTerm = \App\Models\TahunAkademik::where('is_aktif', true)->first())
                <span class="term-pill"><span class="term-dot"></span>{{ $layoutActiveTerm?->nama_semester ?? 'Semester aktif belum diatur' }}{{ $layoutActiveTerm?->tahun_ajaran ? ' · '.$layoutActiveTerm->tahun_ajaran : '' }}</span>
                <span class="topbar-user">{{ auth()->user()->name ?? 'Administrator' }}</span>
            </div>
        </header>
        <section class="content">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-error">
                    <strong>Periksa kembali:</strong>
                    <ul style="margin:6px 0 0;padding-left:19px">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif
            @yield('content')
        </section>
        <footer class="footer-note">SIAKADU · Academic Services · Data ditampilkan berdasarkan database aplikasi yang terhubung.</footer>
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
