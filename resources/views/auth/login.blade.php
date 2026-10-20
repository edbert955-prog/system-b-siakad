<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login · SIAKADU</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/siakadu-ui-controls-fix.css') }}">
</head>
<body style="margin:0">
<div class="login-page">
    <section class="login-brand-panel">
        <div class="login-brand-kicker">Academic Services Platform</div>
        <h1 class="login-brand-title">SIAKADU</h1>
        <p class="login-brand-description">
            Sistem Informasi Akademik Terpadu untuk pengelolaan data mahasiswa,
            dosen, kurikulum, dan rekam akademik.
        </p>
        <div class="login-feature-grid">
            <div class="login-feature">
                <strong>Akademik</strong>
                <span>Data terpusat</span>
            </div>
            <div class="login-feature">
                <strong>Terpadu</strong>
                <span>Satu sistem</span>
            </div>
            <div class="login-feature">
                <strong>Aman</strong>
                <span>Akses terbatas</span>
            </div>
        </div>
    </section>

    <main class="login-form-panel">
        <div class="login-card">
            <div class="login-eyebrow">SIAKADU · Academic Portal</div>
            <h2 class="login-heading">Selamat datang kembali</h2>
            <p class="login-subheading">
                Masuk menggunakan akun SIAKADU. Sistem akan mengarahkanmu sesuai peran akun.
            </p>

            @if(session('success'))
                <div class="login-alert login-alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="login-alert login-alert-error">
                    <strong>Login belum berhasil.</strong>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form class="login-form" method="POST" action="{{ route('login.submit') }}">
                @csrf
                <div class="field">
                    <label for="identity">Email atau Nomor Induk</label>
                    <input id="identity"
                           name="identity"
                           type="text"
                           value="{{ old('identity') }}"
                           placeholder="Email atau NPM"
                           autocomplete="username"
                           maxlength="150"
                           required
                           autofocus>
                </div>

                <div class="field">
                    <label for="password">Password</label>
                    <input id="password"
                           name="password"
                           type="password"
                           placeholder="Masukkan password"
                           autocomplete="current-password"
                           required>
                </div>

                <button class="login-submit" type="submit">Masuk ke SIAKADU</button>
            </form>

            <p class="login-footnote">
                Portal ini mendukung admin/staf akademik dan mahasiswa yang memiliki akun aktif.
                Jangan bagikan kredensial kepada orang lain.
            </p>
        </div>
    </main>
</div>
</body>
</html>
