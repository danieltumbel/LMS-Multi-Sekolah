<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('SUPER_ADMIN_EMAIL', 'admin@sevima.test');

        // firstOrNew + forceFill: role, school_id, dan is_active sengaja tidak ada di $fillable
        $user = User::firstOrNew(['email' => $email]);

        $user->forceFill([
            'name'              => 'Kelompok 5',
            'password'          => Hash::make(env('SUPER_ADMIN_PASSWORD', 'kelompok5')),
            'role'              => 'super_admin',
            'school_id'         => null,
            'is_active'         => true,
            'email_verified_at' => now(),
        ])->save();
    }
}