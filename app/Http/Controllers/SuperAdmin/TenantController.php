<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Mail\KredensialAdminSekolah;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $schools = DB::table('schools')
            ->select('schools.*')
            ->selectSub(
                DB::table('users')->selectRaw('count(*)')->whereColumn('users.school_id', 'schools.id'),
                'users_count'
            )
            ->selectSub(
                DB::table('users')->select('name')
                    ->whereColumn('users.school_id', 'schools.id')
                    ->where('role', 'school_admin')
                    ->orderBy('id')->limit(1),
                'admin_name'
            )
            ->when($q !== '', fn ($b) => $b->where('schools.name', 'like', '%'.$q.'%'))
            ->orderByDesc('schools.id')
            ->paginate(5)
            ->withQueryString();

        return view('SuperAdmin.tenants.index', compact('schools', 'q'));
    }

    // FR-SA-02: ubah status + alasan + catat ke audit_logs
    public function updateStatus(Request $request, int $tenant)
    {
        $data = $request->validate([
            'status' => ['required', 'in:active,suspended,inactive'],
            'alasan' => ['required', 'string', 'max:500'],
        ], [
            'status.in'       => 'Status tidak valid.',
            'alasan.required' => 'Alasan perubahan status wajib diisi.',
        ]);

        $school = DB::table('schools')->where('id', $tenant)->first();
        abort_if(! $school, 404);

        if ($school->status === $data['status']) {
            return back()->with('status', 'Status sekolah sudah '.$data['status'].'.');
        }

        DB::transaction(function () use ($school, $data) {
            DB::table('schools')->where('id', $school->id)
                ->update(['status' => $data['status'], 'updated_at' => now()]);

            AuditLog::create([
                'school_id'   => $school->id,
                'user_id'     => Auth::id(),
                'action'      => 'tenant.status_changed',
                'description' => $data['alasan'],
                'old_values'  => ['status' => $school->status],
                'new_values'  => ['status' => $data['status']],
            ]);
        });

        return back()->with('status', 'Status '.$school->name.' berhasil diubah.');
    }

    public function show(int $tenant)
    {
        $school = DB::table('schools')->find($tenant);
        abort_if(! $school, 404);

        $peran = DB::table('users')->where('school_id', $tenant)
            ->select('role', DB::raw('count(*) as total'))->groupBy('role')->pluck('total', 'role');
        $jmlKelas = DB::table('classes')->where('school_id', $tenant)->count();
        $admin = DB::table('users')->where('school_id', $tenant)
            ->where('role', 'school_admin')->orderBy('id')->first();
        $riwayat = DB::table('audit_logs')->where('school_id', $tenant)
            ->orderByDesc('id')->limit(10)->get();

        return view('SuperAdmin.tenants.show', compact('school', 'peran', 'jmlKelas', 'admin', 'riwayat'));
    }

    public function edit(int $tenant)
    {
        $school = DB::table('schools')->find($tenant);
        abort_if(! $school, 404);

        $terpakai = [
            'users'   => DB::table('users')->where('school_id', $tenant)->count(),
            'classes' => DB::table('classes')->where('school_id', $tenant)->count(),
        ];

        return view('SuperAdmin.tenants.edit', compact('school', 'terpakai'));
    }

    // FR-SA-04: ubah data dan kuota (kuota tidak boleh di bawah pemakaian saat ini)
    public function update(Request $request, int $tenant)
    {
        $school = DB::table('schools')->find($tenant);
        abort_if(! $school, 404);

        $jmlUser  = DB::table('users')->where('school_id', $tenant)->count();
        $jmlKelas = DB::table('classes')->where('school_id', $tenant)->count();

        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255', Rule::unique('schools', 'name')->ignore($tenant)],
            'address'     => ['required', 'string', 'max:255'],
            'email'       => ['required', 'email', 'max:255', Rule::unique('schools', 'contact_email')->ignore($tenant)],
            'phone'       => ['required', 'regex:/^[0-9+\-\s]{8,20}$/'],
            'max_users'   => ['required', 'integer', 'min:'.max(1, $jmlUser), 'max:100000'],
            'max_classes' => ['required', 'integer', 'min:'.max(1, $jmlKelas), 'max:10000'],
        ], [
            'name.unique'     => 'Nama sekolah sudah terdaftar.',
            'email.unique'    => 'Email sekolah sudah terdaftar.',
            'phone.regex'     => 'No. telepon tidak valid (8–20 digit, boleh diawali +).',
            'max_users.min'   => "Kuota pengguna tidak boleh di bawah jumlah pengguna saat ini ($jmlUser).",
            'max_classes.min' => "Kuota kelas tidak boleh di bawah jumlah kelas saat ini ($jmlKelas).",
        ]);

        DB::transaction(function () use ($school, $data, $tenant) {
            DB::table('schools')->where('id', $tenant)->update([
                'name'          => $data['name'],
                'address'       => $data['address'],
                'contact_email' => $data['email'],
                'phone'         => $data['phone'],
                'max_users'     => $data['max_users'],
                'max_classes'   => $data['max_classes'],
                'updated_at'    => now(),
            ]);

            AuditLog::create([
                'school_id'  => $tenant,
                'user_id'    => Auth::id(),
                'action'     => 'tenant.updated',
                'old_values' => ['max_users' => $school->max_users, 'max_classes' => $school->max_classes],
                'new_values' => ['max_users' => $data['max_users'], 'max_classes' => $data['max_classes']],
            ]);
        });

        return redirect()->route('super-admin.tenants.index')->with('status', 'Data sekolah berhasil diperbarui.');
    }

    public function create()
    {
        return view('SuperAdmin.tenants.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255', 'unique:schools,name'],
            'address'     => ['required', 'string', 'max:255'],
            'email'       => ['required', 'email', 'max:255', 'unique:schools,contact_email'],
            'phone'       => ['required', 'regex:/^[0-9+\-\s]{8,20}$/'],
            'admin_name'  => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'max_users'   => ['required', 'integer', 'min:1', 'max:100000'],
            'max_classes' => ['required', 'integer', 'min:1', 'max:10000'],
        ], [
            'name.required'        => 'Nama sekolah wajib diisi.',
            'name.unique'          => 'Nama sekolah sudah terdaftar.',
            'address.required'     => 'Alamat wajib diisi.',
            'email.required'       => 'Email sekolah wajib diisi.',
            'email.email'          => 'Format email sekolah tidak valid.',
            'email.unique'         => 'Email sekolah sudah terdaftar.',
            'phone.required'       => 'No. telepon wajib diisi.',
            'phone.regex'          => 'No. telepon tidak valid (8–20 digit, boleh diawali +).',
            'admin_name.required'  => 'Nama admin wajib diisi.',
            'admin_email.required' => 'Email admin wajib diisi.',
            'admin_email.email'    => 'Format email admin tidak valid.',
            'admin_email.unique'   => 'Email admin sudah dipakai akun lain.',
            'max_users.required'   => 'Maks. pengguna wajib diisi.',
            'max_users.min'        => 'Maks. pengguna minimal 1.',
            'max_classes.required' => 'Maks. kelas wajib diisi.',
            'max_classes.min'      => 'Maks. kelas minimal 1.',
        ]);

        $passwordSementara = Str::random(12);

        DB::transaction(function () use ($data, $passwordSementara) {
            $schoolId = DB::table('schools')->insertGetId([
                'name'          => $data['name'],
                'address'       => $data['address'],
                'contact_email' => $data['email'],
                'phone'         => $data['phone'],
                'status'        => 'active',
                'max_users'     => $data['max_users'],
                'max_classes'   => $data['max_classes'],
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            User::forceCreate([
                'name'              => $data['admin_name'],
                'email'             => $data['admin_email'],
                'password'          => Hash::make($passwordSementara),
                'role'              => 'school_admin',
                'school_id'         => $schoolId,
                'is_active'         => true,
                'email_verified_at' => now(),
            ]);

            AuditLog::create([
                'school_id'  => $schoolId,
                'user_id'    => Auth::id(),
                'action'     => 'tenant.created',
                'new_values' => [
                    'name'        => $data['name'],
                    'admin_email' => $data['admin_email'],
                    'max_users'   => $data['max_users'],
                    'max_classes' => $data['max_classes'],
                ],
            ]);
        });

        // Email gagal tidak membatalkan pendaftaran; password tetap tampil di layar.
        try {
            Mail::to($data['admin_email'])->send(
                new KredensialAdminSekolah($data['name'], $data['admin_email'], $passwordSementara)
            );
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()
            ->route('super-admin.tenants.create')
            ->with('tenant_created', [
                'sekolah'  => $data['name'],
                'email'    => $data['admin_email'],
                'password' => $passwordSementara,
            ]);
    }
}
