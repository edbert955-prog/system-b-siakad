@extends($isStudent ? 'layouts.student' : 'layouts.admin')

@section('title', 'Pengaturan Akun')
@section('page-label', 'Pengaturan Akun')

@push('styles')
<style>
    /* Header khusus Pengaturan Akun — tanpa ikon */
    .settings-page-heading {
        display: block !important;
        position: relative;
        width: 100%;
        min-width: 0;
        margin: 0 0 22px;
        padding: 24px 26px;
        box-sizing: border-box;
        background: #ffffff;
        border: 1px solid #dce4ef;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(15, 35, 75, 0.04);
        overflow: hidden;
    }

    .settings-page-heading::before,
    .settings-page-heading::after {
        content: none !important;
        display: none !important;
    }

    .settings-page-heading svg,
    .settings-page-heading i,
    .settings-page-heading img,
    .settings-page-heading [class*="icon"],
    .settings-page-heading [data-icon] {
        display: none !important;
    }

    .settings-page-eyebrow {
        margin: 0 0 8px;
        color: #0f766e;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .settings-page-title {
        margin: 0 !important;
        padding: 0 !important;
        color: #122344;
        font-size: 28px !important;
        font-weight: 800;
        line-height: 1.3 !important;
        letter-spacing: -0.025em;
    }

    .settings-page-description {
        max-width: 720px;
        margin: 9px 0 0;
        color: #64748b;
        font-size: 13px;
        line-height: 1.7;
    }

    .settings-account-card {
        max-width: 900px;
        overflow: hidden;
    }

    .settings-account-profile {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 22px 24px;
        border-bottom: 1px solid #e5eaf2;
    }

    .settings-account-avatar {
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 48px;
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: #e0f7f4;
        color: #0f766e;
        font-size: 21px;
        font-weight: 800;
    }

    .settings-account-name {
        margin: 0 0 5px;
        color: #172554;
        font-size: 16px;
        font-weight: 800;
        overflow-wrap: anywhere;
    }

    .settings-account-meta {
        color: #64748b;
        font-size: 12px;
        overflow-wrap: anywhere;
    }

    .settings-account-body {
        padding: 24px;
    }

    .settings-section-title {
        margin: 0 0 5px;
        color: #172554;
        font-size: 14px;
        font-weight: 800;
    }

    .settings-section-description {
        margin: 0 0 18px;
        color: #64748b;
        font-size: 12px;
        line-height: 1.6;
    }

    .settings-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .settings-form-grid .field {
        min-width: 0;
    }

    .settings-form-grid .field input {
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
    }

    .settings-form-divider {
        height: 0;
        margin: 25px 0;
        border: 0;
        border-top: 1px solid #e5eaf2;
    }

    .settings-form-actions {
        display: flex;
        justify-content: flex-end;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 24px;
    }

    @media (max-width: 640px) {
        .settings-page-heading {
            padding: 20px;
        }

        .settings-page-title {
            font-size: 23px !important;
        }

        .settings-account-profile {
            padding: 18px;
        }

        .settings-account-body {
            padding: 18px;
        }

        .settings-form-grid {
            grid-template-columns: minmax(0, 1fr);
            gap: 15px;
        }
    }
</style>
@endpush

@section('content')

<div class="settings-page-heading">
    <div class="settings-page-eyebrow">
        Keamanan dan profil akun
    </div>

    <h1 class="settings-page-title">
        Pengaturan Akun
    </h1>

    <p class="settings-page-description">
        Perbarui informasi akun dan password untuk menjaga keamanan
        akses ke sistem SIAKADU.
    </p>
</div>

<section class="card settings-account-card">

    <div class="settings-account-profile">
        <div class="settings-account-avatar">
            {{ strtoupper(substr(
                $isStudent
                    ? ($student?->nama ?? $user->name)
                    : $user->name,
                0,
                1
            )) }}
        </div>

        <div style="min-width: 0;">
            <h2 class="settings-account-name">
                {{ $isStudent
                    ? ($student?->nama ?? $user->name)
                    : $user->name }}
            </h2>

            <div class="settings-account-meta">
                Nomor Induk:
                {{ $user->nomor_induk ?: 'Belum ditetapkan' }}
                · Role: {{ $user->role }}
            </div>

            <div class="settings-account-meta" style="margin-top: 5px;">
                Role akun ditetapkan oleh administrator dan tidak
                dapat diubah melalui halaman ini.
            </div>
        </div>
    </div>

    <div class="settings-account-body">

        <form
            method="POST"
            action="{{ $isStudent
                ? route('student.account-settings.update')
                : route('admin.account-settings.update') }}"
        >
            @csrf
            @method('PUT')

            <h3 class="settings-section-title">
                Informasi Akun
            </h3>

            <p class="settings-section-description">
                Pastikan email dan nomor telepon yang tersimpan
                masih benar dan dapat digunakan.
            </p>

            <div class="settings-form-grid">

                @unless($isStudent)
                    <div class="field">
                        <label for="name">Nama Tampilan *</label>

                        <input
                            id="name"
                            name="name"
                            type="text"
                            maxlength="150"
                            value="{{ old('name', $user->name) }}"
                            required
                        >
                    </div>
                @endunless

                <div class="field">
                    <label for="email">Email Akun *</label>

                    <input
                        id="email"
                        name="email"
                        type="email"
                        maxlength="150"
                        value="{{ old('email', $user->email) }}"
                        autocomplete="email"
                        required
                    >
                </div>

                <div class="field">
                    <label for="phone">Nomor Telepon</label>

                    <input
                        id="phone"
                        name="phone"
                        type="tel"
                        maxlength="20"
                        value="{{ old('phone', $user->phone) }}"
                        autocomplete="tel"
                        placeholder="Contoh: 0812xxxxxxxx"
                    >
                </div>

            </div>

            <hr class="settings-form-divider">

            <h3 class="settings-section-title">
                Ganti Password
            </h3>

            <p class="settings-section-description">
                Kosongkan ketiga kolom password jika tidak ingin
                menggantinya. Password baru minimal 8 karakter.
            </p>

            <div class="settings-form-grid">

                <div class="field">
                    <label for="current_password">
                        Password Saat Ini
                    </label>

                    <input
                        id="current_password"
                        name="current_password"
                        type="password"
                        autocomplete="current-password"
                    >
                </div>

                <div class="field">
                    <label for="new_password">
                        Password Baru
                    </label>

                    <input
                        id="new_password"
                        name="new_password"
                        type="password"
                        minlength="8"
                        autocomplete="new-password"
                    >
                </div>

                <div class="field">
                    <label for="new_password_confirmation">
                        Konfirmasi Password Baru
                    </label>

                    <input
                        id="new_password_confirmation"
                        name="new_password_confirmation"
                        type="password"
                        minlength="8"
                        autocomplete="new-password"
                    >
                </div>

            </div>

            <div class="settings-form-actions">
                <button
                    class="btn btn-primary"
                    type="submit"
                >
                    Simpan Pengaturan
                </button>
            </div>

        </form>

    </div>
</section>

@endsection