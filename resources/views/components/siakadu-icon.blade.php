
@props([
    'name' => 'circle-help',
    'size' => 18,
])

@php
    $iconSize = max(12, min(64, (int) $size));
@endphp

<svg
    xmlns="http://www.w3.org/2000/svg"
    width="{{ $iconSize }}"
    height="{{ $iconSize }}"
    viewBox="0 0 24 24"
    fill="none"
    stroke="currentColor"
    stroke-width="1.8"
    stroke-linecap="round"
    stroke-linejoin="round"
    aria-hidden="true"
    focusable="false"
    style="
        display: block !important;
        position: static !important;
        flex: 0 0 {{ $iconSize }}px !important;
        width: {{ $iconSize }}px !important;
        height: {{ $iconSize }}px !important;
        min-width: {{ $iconSize }}px !important;
        max-width: {{ $iconSize }}px !important;
        margin: 0 !important;
        padding: 0 !important;
        transform: none !important;
        filter: none !important;
        overflow: visible;
        vertical-align: middle;
    "
>
    @switch($name)

        {{-- Settings: satu ikon roda gigi --}}
        @case('settings')
        <path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543-.94 3.31.827 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 0 0-1.066 2.573c.94 1.543-.827 3.31-2.37 2.37a1.724 1.724 0 0 0-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 0 0-2.573-1.066c-1.543.94-3.31-.827-2.37-2.37a1.724 1.724 0 0 0-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 0 0 1.066-2.573c-.94-1.543.827-3.31 2.37-2.37.996.607 2.296.095 2.572-1.065z"/>
        <circle cx="12" cy="12" r="3"/>
        @break

        {{-- Dashboard --}}
        @case('dashboard')
        @case('layout-dashboard')
        <rect x="3" y="3" width="8" height="8" rx="1"/>
        <rect x="14" y="3" width="7" height="5" rx="1"/>
        <rect x="14" y="11" width="7" height="10" rx="1"/>
        <rect x="3" y="14" width="8" height="7" rx="1"/>
        @break

        {{-- Mahasiswa dan pengguna --}}
        @case('users')
        <path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
        <circle cx="10" cy="7" r="4"/>
        <path d="M20 21v-2a4 4 0 0 0-3-3.87"/>
        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
        @break

        @case('user')
        @case('user-round')
        <circle cx="12" cy="8" r="4"/>
        <path d="M5 21v-2a7 7 0 0 1 14 0v2"/>
        @break

        {{-- Dosen --}}
        @case('graduation-cap')
        <path d="m2 10 10-5 10 5-10 5-10-5z"/>
        <path d="M6 12v5c3 3 9 3 12 0v-5"/>
        <path d="M22 10v6"/>
        @break

        {{-- Fakultas --}}
        @case('building-2')
        <path d="M3 21h18"/>
        <path d="M5 21V7l7-4 7 4v14"/>
        <path d="M9 9h1"/>
        <path d="M14 9h1"/>
        <path d="M9 13h1"/>
        <path d="M14 13h1"/>
        <path d="M10 21v-4h4v4"/>
        @break

        {{-- Mata kuliah --}}
        @case('book-open')
        <path d="M2 4.5A2.5 2.5 0 0 1 4.5 2H12v19H4.5A2.5 2.5 0 0 0 2 23.5z"/>
        <path d="M22 4.5A2.5 2.5 0 0 0 19.5 2H12v19h7.5a2.5 2.5 0 0 1 2.5 2.5z"/>
        @break

        {{-- Jadwal --}}
        @case('calendar-days')
        @case('calendar')
        <rect x="3" y="5" width="18" height="16" rx="2"/>
        <path d="M16 3v4M8 3v4M3 10h18"/>
        <path d="M8 14h.01M12 14h.01M16 14h.01M8 17h.01M12 17h.01"/>
        @break

        {{-- KRS --}}
        @case('list-checks')
        @case('clipboard-list')
        <path d="m3 6 2 2 4-4"/>
        <path d="M12 6h9"/>
        <path d="m3 12 2 2 4-4"/>
        <path d="M12 12h9"/>
        <path d="m3 18 2 2 4-4"/>
        <path d="M12 18h9"/>
        @break

        {{-- KHS dan transkrip --}}
        @case('file-text')
        @case('file')
        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
        <path d="M14 2v6h6M8 13h8M8 17h8"/>
        @break

        {{-- Pembayaran --}}
        @case('credit-card')
        <rect x="2" y="5" width="20" height="14" rx="2"/>
        <path d="M2 10h20M6 15h4"/>
        @break

        {{-- Keluar --}}
        @case('log-out')
        <path d="M10 17l5-5-5-5"/>
        <path d="M15 12H3"/>
        <path d="M12 3h6a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-6"/>
        @break

        {{-- Panah kembali --}}
        @case('arrow-left')
        <path d="m12 19-7-7 7-7"/>
        <path d="M19 12H5"/>
        @break

        {{-- Bantuan atau ikon tidak dikenal --}}
        @default
        <circle cx="12" cy="12" r="9"/>
        <path d="M9.5 9a2.5 2.5 0 1 1 4.2 1.8c-1.1.9-1.7 1.2-1.7 2.7"/>
        <path d="M12 17h.01"/>
        @break

    @endswitch
</svg>
