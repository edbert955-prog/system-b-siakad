<nav class="nav-section siakadu-side-nav" aria-label="Navigasi mahasiswa">
    <div class="nav-label">Akademik Saya</div>

    <a class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}" href="{{ route('student.dashboard') }}">
        <span class="nav-icon"><x-siakadu-icon name="layout-dashboard" /></span><span class="nav-text">Dashboard</span>
    </a>
    <a class="nav-link {{ request()->routeIs('student.krs*') ? 'active' : '' }}" href="{{ route('student.krs') }}">
        <span class="nav-icon"><x-siakadu-icon name="file-pen-line" /></span><span class="nav-text">Registrasi &amp; KRS</span>
    </a>
    <a class="nav-link {{ request()->routeIs('student.khs') && request()->query('view') !== 'transcript' ? 'active' : '' }}" href="{{ route('student.khs') }}">
        <span class="nav-icon"><x-siakadu-icon name="chart-column" /></span><span class="nav-text">Hasil Studi (KHS)</span>
    </a>
    <a class="nav-link {{ request()->routeIs('student.schedule') ? 'active' : '' }}" href="{{ route('student.schedule') }}">
        <span class="nav-icon"><x-siakadu-icon name="calendar-days" /></span><span class="nav-text">Jadwal Kuliah</span>
    </a>
    <a class="nav-link {{ request()->routeIs('student.khs') && request()->query('view') === 'transcript' ? 'active' : '' }}" href="{{ route('student.khs', ['view' => 'transcript']) }}#transcript">
        <span class="nav-icon"><x-siakadu-icon name="graduation-cap" /></span><span class="nav-text">Transkrip Akademik</span>
    </a>
    <a class="nav-link {{ request()->routeIs('student.ukt-payments') ? 'active' : '' }}" href="{{ route('student.ukt-payments') }}">
        <span class="nav-icon"><x-siakadu-icon name="wallet-cards" /></span><span class="nav-text">Pembayaran UKT</span>
    </a>
    <a class="nav-link {{ request()->routeIs('student.profile') ? 'active' : '' }}" href="{{ route('student.profile') }}">
        <span class="nav-icon"><x-siakadu-icon name="user-round" /></span><span class="nav-text">Profil Saya</span>
    </a>
</nav>
