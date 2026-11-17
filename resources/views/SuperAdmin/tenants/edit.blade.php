<!DOCTYPE html>
<html><body>
<h1>Edit {{ $school->name }}</h1>
@if (session('status')) <p>{{ session('status') }}</p> @endif
@if ($errors->any()) <ul>@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul> @endif

<form method="POST" action="{{ route('super-admin.tenants.update', $school->id) }}">
    @csrf @method('PUT')
    <p>Nama <input name="name" value="{{ old('name', $school->name) }}"></p>
    <p>Alamat <input name="address" value="{{ old('address', $school->address) }}"></p>
    <p>Email <input name="email" value="{{ old('email', $school->contact_email) }}"></p>
    <p>Telepon <input name="phone" value="{{ old('phone', $school->phone) }}"></p>
    <p>Maks. pengguna <input type="number" name="max_users" value="{{ old('max_users', $school->max_users) }}"> (terpakai {{ $terpakai['users'] }})</p>
    <p>Maks. kelas <input type="number" name="max_classes" value="{{ old('max_classes', $school->max_classes) }}"> (terpakai {{ $terpakai['classes'] }})</p>
    <button type="submit">Simpan</button>
</form>

<h2>Ubah status (sekarang: {{ $school->status }})</h2>
<form method="POST" action="{{ route('super-admin.tenants.status', $school->id) }}">
    @csrf @method('PATCH')
    <select name="status">
        <option value="active" @selected($school->status === 'active')>Aktif</option>
        <option value="suspended" @selected($school->status === 'suspended')>Ditangguhkan</option>
        <option value="inactive" @selected($school->status === 'inactive')>Nonaktif</option>
    </select>
    <input name="alasan" placeholder="Alasan" required>
    <button type="submit">Ubah Status</button>
</form>
</body></html>
