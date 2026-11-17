<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EnsureTenantActive
{
   public function handle(Request $request, Closure $next) {
     $user = $request->user();

     if ($user && $user->role !== 'super_admin') {
         $sekolahAktif = ! $user->school_id
            || DB::table('schools')->where('id', $user->school_id)->value('status') === 'active';

         if (! $user->is_active || ! $sekolahAktif) {
             Auth::guard('web')->logout();
             $request->session()->invalidate();
             $request->session()->regenerateToken();

             return redirect('/login')->withErrors([
                 'email' => $sekolahAktif
                     ? 'Akun Anda dinonaktifkan. Hubungi admin sekolah.'
                     : 'Sekolah Anda sedang tidak aktif. Hubungi Super Admin.',
                ]);
            }
        }
     return $next($request);
   }
}
