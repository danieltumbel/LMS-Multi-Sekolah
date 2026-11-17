<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/**
 * Profil akun Super Admin: nama, email, dan foto.
 */
class AccountController extends Controller
{
    public function edit(Request $request)
    {
        return view('SuperAdmin.profile', ['user' => $request->user()]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'foto'  => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'name.required'  => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
            'email.unique'   => 'Email sudah dipakai akun lain.',
            'foto.image'     => 'File harus berupa gambar.',
            'foto.mimes'     => 'Format foto harus JPG, PNG, atau WEBP.',
            'foto.max'       => 'Ukuran foto maksimal 2 MB.',
        ]);

        $user->name  = $data['name'];
        $user->email = $data['email'];

        if ($request->hasFile('foto')) {
            if ($user->foto) {
                Storage::disk('public')->delete($user->foto);
            }
            $user->foto = $request->file('foto')->store('avatars', 'public');
        }

        $user->save();

        return redirect()->route('super-admin.profile.edit')->with('status', 'Profil berhasil diperbarui.');
    }
}