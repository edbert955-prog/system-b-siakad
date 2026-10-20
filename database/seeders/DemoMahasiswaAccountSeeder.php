<?php

namespace Database\Seeders;

use App\Models\Mahasiswa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class DemoMahasiswaAccountSeeder extends Seeder
{
    public function run(): void
    {
        // Demo NPM ini pernah dipakai pada data contoh proyek. Ubah hanya jika NPM tersebut tidak ada di database lokal.
        $npm = '2415061114';
        $student = Mahasiswa::find($npm);

        if (!$student) {
            $this->command?->error("Mahasiswa dengan NPM {$npm} tidak ditemukan. Ubah NPM di DemoMahasiswaAccountSeeder.php ke NPM dummy yang memang ada di tabel mahasiswa.");
            return;
        }

        if (DB::table('users')->where('nomor_induk', $npm)->exists()) {
            $this->command?->warn("Akun dengan nomor_induk {$npm} sudah ada. Seeder tidak mengubah akun atau password lama.");
            return;
        }

        $constraint = DB::selectOne("SELECT pg_get_constraintdef(oid) AS definition FROM pg_constraint WHERE conrelid = 'public.users'::regclass AND conname = 'users_role_check'");
        $definition = (string) ($constraint->definition ?? '');

        if ($definition !== '' && !preg_match("/'mahasiswa'/i", $definition)) {
            throw new RuntimeException('Constraint users_role_check tidak tampak mengizinkan role mahasiswa. Periksa aturan role di PostgreSQL sebelum membuat akun mahasiswa demo.');
        }

        $email = filter_var($student->email_kampus, FILTER_VALIDATE_EMAIL)
            ? $student->email_kampus
            : "mahasiswa.{$npm}@siakadu.local";

        if (DB::table('users')->where('email', $email)->exists()) {
            $email = "mahasiswa.{$npm}@siakadu.local";
        }

        if (DB::table('users')->where('email', $email)->exists()) {
            $this->command?->error("Email demo {$email} sudah digunakan. Ubah alamat fallback di seeder.");
            return;
        }

        $candidate = [
            'name' => $student->nama,
            'email' => $email,
            'password' => Hash::make('Mahasiswa123!'),
            'nomor_induk' => $student->npm,
            'role' => 'mahasiswa',
            'phone' => $student->no_hp,
            'dosen_pa_id' => $student->id_dosen_pa,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $insert = [];
        foreach ($candidate as $column => $value) {
            if (Schema::hasColumn('users', $column)) {
                $insert[$column] = $value;
            }
        }

        DB::table('users')->insert($insert);

        $this->command?->info("Akun mahasiswa demo dibuat untuk NPM {$student->npm}.");
        $this->command?->info("Email: {$email}");
        $this->command?->warn('Password demo: Mahasiswa123! — ganti setelah login pertama kali.');
    }
}
