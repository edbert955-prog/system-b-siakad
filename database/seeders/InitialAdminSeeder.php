<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class InitialAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = 'admin@siakadu.local';
        if (DB::table('users')->where('email', $email)->exists()) {
            $this->command?->warn("Akun {$email} sudah ada; tidak diubah agar data lama tetap aman.");
            return;
        }

        $candidate = [
            'name' => 'Administrator SIAKADU',
            'email' => $email,
            'password' => Hash::make('Admin123!'),
            'nomor_induk' => 'ADMIN-SIAKAD-001',
            'role' => 'staff_akademik',
            'phone' => null,
            'dosen_pa_id' => null,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $row = [];
        foreach ($candidate as $column => $value) {
            if (Schema::hasColumn('users', $column)) {
                $row[$column] = $value;
            }
        }
        DB::table('users')->insert($row);
        $this->command?->info("Admin demo dibuat: {$email} | password: Admin123!");
        $this->command?->warn('Ganti password demo setelah login pertama. Akun ini hanya untuk pengembangan lokal.');
    }
}
