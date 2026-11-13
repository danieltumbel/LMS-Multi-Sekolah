<x-adminsekolah-layout title="Profil Admin Sekolah" subtitle="Informasi akun dan data sekolah Anda.">
    @include('profile.partials.tampil', ['user' => $user, 'info' => $info])
</x-adminsekolah-layout>