<x-siswa-layout title="Profil" subtitle="Informasi akun dan data diri kamu.">
    @include('profile.partials.tampil', ['user' => $user, 'info' => $info])
</x-siswa-layout>