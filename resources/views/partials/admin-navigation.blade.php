<nav class="nav-section siakadu-side-nav" aria-label="Navigasi utama">
    <div class="nav-label">Workspace</div>

    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
        <span class="nav-icon"><x-siakadu-icon name="layout-dashboard" /></span><span class="nav-text">Dashboard</span>
    </a>
    <a class="nav-link {{ request()->routeIs('admin.students.*') ? 'active' : '' }}" href="{{ route('admin.students.index') }}">
        <span class="nav-icon"><x-siakadu-icon name="graduation-cap" /></span><span class="nav-text">Mahasiswa</span>
    </a>
    <a class="nav-link {{ request()->routeIs('admin.lecturers.*') ? 'active' : '' }}" href="{{ route('admin.lecturers.index') }}">
        <span class="nav-icon"><x-siakadu-icon name="contact-round" /></span><span class="nav-text">Dosen</span>
    </a>
    <a class="nav-link {{ request()->routeIs('admin.academic-structure', 'admin.faculties.*', 'admin.programs.*') ? 'active' : '' }}" href="{{ route('admin.academic-structure') }}">
        <span class="nav-icon"><x-siakadu-icon name="building-2" /></span><span class="nav-text">Fakultas &amp; Prodi</span>
    </a>
    <a class="nav-link {{ request()->routeIs('admin.courses.*') ? 'active' : '' }}" href="{{ route('admin.courses.index') }}">
        <span class="nav-icon"><x-siakadu-icon name="book-open" /></span><span class="nav-text">Mata Kuliah</span>
    </a>
    <a class="nav-link {{ request()->routeIs('admin.class-schedules.*') ? 'active' : '' }}" href="{{ route('admin.class-schedules.index') }}">
        <span class="nav-icon"><x-siakadu-icon name="calendar-days" /></span><span class="nav-text">Jadwal Kuliah</span>
    </a>
    <a class="nav-link {{ request()->routeIs('admin.academic-terms.*') ? 'active' : '' }}" href="{{ route('admin.academic-terms.index') }}">
        <span class="nav-icon"><x-siakadu-icon name="calendar-clock" /></span><span class="nav-text">Periode Akademik</span>
    </a>
    <a class="nav-link {{ request()->routeIs('admin.krs-management.*') ? 'active' : '' }}" href="{{ route('admin.krs-management.index') }}">
        <span class="nav-icon"><x-siakadu-icon name="file-pen-line" /></span><span class="nav-text">Rencana Studi (KRS)</span>
    </a>
    <a class="nav-link {{ request()->routeIs('admin.academic-results.*') ? 'active' : '' }}" href="{{ route('admin.academic-results.index') }}">
        <span class="nav-icon"><x-siakadu-icon name="chart-column" /></span><span class="nav-text">Hasil Studi (KHS)</span>
    </a>
    <a class="nav-link {{ request()->routeIs('admin.ukt-payments.*') ? 'active' : '' }}" href="{{ route('admin.ukt-payments.index') }}">
        <span class="nav-icon"><x-siakadu-icon name="wallet-cards" /></span><span class="nav-text">Pembayaran UKT</span>
    </a>
    <a class="nav-link {{ request()->routeIs('admin.reference-settings.*') ? 'active' : '' }}" href="{{ route('admin.reference-settings.index') }}">
        <span class="nav-icon"><x-siakadu-icon name="settings-2" /></span><span class="nav-text">Data Referensi</span>
    </a>
</nav>
