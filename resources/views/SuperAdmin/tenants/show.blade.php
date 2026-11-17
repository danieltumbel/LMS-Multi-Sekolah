<!DOCTYPE html>
<html><body>
<h1>{{ $school->name }} ({{ $school->status }})</h1>
<p>{{ $school->address }} | {{ $school->contact_email }} | {{ $school->phone }}</p>
<p>Admin: {{ $admin->name ?? '-' }} ({{ $admin->email ?? '-' }})</p>
<p>Guru: {{ $peran['teacher'] ?? 0 }} | Siswa: {{ $peran['student'] ?? 0 }} | Kelas: {{ $jmlKelas }}</p>
<p>Kuota pengguna: {{ $peran->sum() }} / {{ $school->max_users }}</p>
<h2>Riwayat</h2>
<ul>
@foreach ($riwayat as $r)
    <li>{{ $r->created_at }} - {{ $r->action }} - {{ $r->description }}</li>
@endforeach
</ul>
<a href="{{ route('super-admin.tenants.index') }}">Kembali</a>
</body></html>
