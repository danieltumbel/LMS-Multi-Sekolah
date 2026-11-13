{{--
    Kartu profil bersama untuk semua role.
    Parameter: $user, dan $kolom = kolom tambahan khusus role, contoh:
    [['name' => 'nomor_induk', 'label' => 'NIP', 'readonly' => true], ['name' => 'no_telepon', 'label' => 'No. Telepon', 'type' => 'tel']]
--}}
@php
    $kolom    = $kolom ?? [];
    $namaRole = ['super_admin' => 'Super Admin', 'school_admin' => 'Admin Sekolah', 'teacher' => 'Guru', 'student' => 'Siswa'][$user->role] ?? $user->role;
    $fotoUrl  = $user->foto ? \Illuminate\Support\Facades\Storage::disk('public')->url($user->foto) : null;
@endphp

@if (session('status') === 'profile-updated')
    <div class="pf-ok" role="status">Profil berhasil diperbarui.</div>
@endif

<form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="pf-card" id="formProfil" novalidate>
    @csrf
    @method('PATCH')

    <div class="pf-head">
        <div class="pf-av" id="pvWrap">
            @if ($fotoUrl)
                <img src="{{ $fotoUrl }}" alt="Foto profil" id="pvImg">
            @else
                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="4.500"/><path d="M3.500 21c0-4.700 3.800-7.500 8.500-7.500s8.500 2.800 8.500 7.500v.5h-17V21Z"/></svg>
            @endif
        </div>

        <div class="pf-who">
            <b>{{ $user->name }}</b>
            <span>{{ $user->email }}</span>
            <small id="pvNama">JPG, PNG, atau WEBP. Maksimal 2 MB.</small>
        </div>

        <button type="button" class="pf-photo" id="btnFoto">Ubah Foto</button>
        <input type="file" name="foto" id="foto" accept="image/png,image/jpeg,image/webp" hidden>
    </div>
    @error('foto')<small class="pf-err" style="margin:-14px 0 14px">{{ $message }}</small>@enderror

    <div class="pf-field">
        <label for="name">Nama Lengkap <b>*</b></label>
        <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" maxlength="255" required class="{{ $errors->has('name') ? 'bad' : '' }}">
        @error('name')<small class="pf-err">{{ $message }}</small>@enderror
    </div>

    <div class="pf-field">
        <label for="email">Email <b>*</b></label>
        <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" maxlength="255" required class="{{ $errors->has('email') ? 'bad' : '' }}">
        @error('email')<small class="pf-err">{{ $message }}</small>@enderror
    </div>

    @foreach ($kolom as $k)
        <div class="pf-field">
            <label for="f-{{ $k['name'] }}">{{ $k['label'] }}</label>
            @if (! empty($k['readonly']))
                <input id="f-{{ $k['name'] }}" type="text" value="{{ $user->{$k['name']} ?: '–' }}" readonly>
            @else
                <input id="f-{{ $k['name'] }}" name="{{ $k['name'] }}" type="{{ $k['type'] ?? 'text' }}"
                       value="{{ old($k['name'], $user->{$k['name']}) }}" placeholder="{{ $k['placeholder'] ?? '' }}"
                       maxlength="{{ $k['max'] ?? 255 }}" class="{{ $errors->has($k['name']) ? 'bad' : '' }}">
                @error($k['name'])<small class="pf-err">{{ $message }}</small>@enderror
            @endif
        </div>
    @endforeach

    <div class="pf-field">
        <label for="role">Role</label>
        <input id="role" type="text" value="{{ $namaRole }}" readonly>
    </div>

    <div class="pf-foot">
        @isset($batal)
            <a href="{{ $batal }}" class="pf-cancel">Batal</a>
        @endisset
        <button type="submit" class="pf-save" id="btnSimpan">Simpan Perubahan</button>
    </div>
</form>