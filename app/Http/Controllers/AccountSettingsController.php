<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class AccountSettingsController extends Controller
{
    private function isStudent(Request $request): bool
    {
        return in_array(
            strtolower(trim((string) $request->user()->role)),
            ['mahasiswa', 'student'],
            true
        );
    }

    public function edit(Request $request)
    {
        $user = $request->user();
        $isStudent = $this->isStudent($request);
        $student = $isStudent ? $user->mahasiswa : null;

        return view('account.settings', compact('user', 'isStudent', 'student'));
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $isStudent = $this->isStudent($request);

        $rules = [
            'email' => [
                'required', 'email', 'max:150',
                Rule::unique('users', 'email')->ignore($user->id, 'id'),
            ],
            'phone' => ['nullable', 'string', 'max:20'],
        ];

        // Mahasiswa tidak boleh mengubah nama resmi di tabel akademik melalui pengaturan akun.
        if (!$isStudent) {
            $rules['name'] = ['required', 'string', 'max:150'];
        }

        $changingPassword = $request->filled('new_password');
        if ($changingPassword) {
            $rules['current_password'] = ['required', 'string', 'max:200'];
            $rules['new_password'] = ['required', 'string', 'min:8', 'confirmed'];
        }

        $data = $request->validate($rules);

        if ($changingPassword && !Hash::check(
            (string) $data['current_password'],
            (string) $user->password
        )) {
            return back()->withErrors([
                'current_password' => 'Password saat ini tidak sesuai.',
            ])->withInput($request->except([
                'current_password', 'new_password', 'new_password_confirmation',
            ]));
        }

        $user->email = $data['email'];
        $user->phone = $data['phone'] ?? null;

        if (!$isStudent) {
            $user->name = $data['name'];
        }

        if ($changingPassword) {
            $user->password = Hash::make($data['new_password']);
        }

        $user->save();

        return back()->with('success', 'Pengaturan akun berhasil diperbarui.');
    }
}
