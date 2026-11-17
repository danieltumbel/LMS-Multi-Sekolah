{{-- Formulir ubah profil untuk Admin Sekolah, Guru, dan Siswa. Layout dipilih sesuai role. --}}
<x-dynamic-component :component="$layout" title="Ubah Profil" subtitle="Perbarui informasi akun Anda.">
    @include('profile.partials.style')
    @include('profile.partials.kartu-profil', ['user' => $user, 'kolom' => $kolom, 'batal' => route('profile.edit')])
    @include('profile.partials.ganti-password')
    @include('profile.partials.script')
</x-dynamic-component>