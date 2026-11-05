<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Data contoh (BUKAN data asli) supaya semua anggota tim punya isi database yang sama.
 * Aman dijalankan berulang: data yang sudah ada diperbarui, bukan digandakan.
 *
 *   php artisan db:seed --class=DemoSeeder
 */
class DemoSeeder extends Seeder
{
    private string $password;

    public function run(): void
    {
        $this->password = Hash::make('Smansa123'); 
        $now = now();

        // ---- Sekolah ----
        $namaSekolah = 'SMA Negeri 1 Banyuwangi';
        DB::table('schools')->updateOrInsert(['name' => $namaSekolah], $this->ada('schools', [
            'address'       => 'Jl. Merpati No. 1, Banyuwangi',
            'contact_email' => 'sman1banyuwangi@gmail.com',
            'phone'         => '0333000001',
            'status'        => 'active',
            'max_users'     => 500,
            'max_classes'   => 100,
            'created_at'    => $now,
            'updated_at'    => $now,
        ]));
        $schoolId = (int) DB::table('schools')->where('name', $namaSekolah)->value('id');

        // ---- Admin sekolah ----
        $this->user('admin_smansa@gmail.com', 'Admin Sekolah', 'school_admin', $schoolId);

        // ---- Guru ----
        $budi = $this->user('budi@gmail.com', 'Budi Santoso, S.Pd.', 'teacher', $schoolId, '198001012006041001', '081200000001');
        $siti = $this->user('siti@gmail.com', 'Siti Aminah, M.Kom.', 'teacher', $schoolId, '198503152010122002', '081200000002');
        $andi = $this->user('andi@gmail.com', 'Andi Pratama, S.Pd.', 'teacher', $schoolId, '198709062012021003', '081200000003');

        // ---- Siswa ----
        $namaSiswa = ['Alvincandra Winata', 'Rina Kartika', 'Dimas Saputra', 'Citra Dewi',
                      'Eka Rahmawati', 'Fajar Nugroho', 'Gita Permata', 'Hadi Prasetyo'];
        // Hapus akun siswa contoh lama (siswa1@smansa.test, dst.) dari versi seeder sebelumnya
        DB::table('users')->where('school_id', $schoolId)->where('email', 'like', 'siswa%@smansa.test')->delete();

        $siswa = [];
        $emailSiswa = [];
        foreach ($namaSiswa as $i => $nama) {
            // Email = nama siswa (huruf kecil, dipisah titik) + @gmail.com, contoh: rina.kartika@gmail.com
            $email = Str::slug($nama, '.').'@gmail.com';
            $emailSiswa[] = $email;
            $siswa[] = $this->user($email, $nama, 'student', $schoolId, sprintf('2026%04d', $i + 1));
        }

        // ---- Kelas: [nama, mata pelajaran, guru pengampu, wali kelas, kode undangan] ----
        $daftarKelas = [
            ['X MIPA 1',  'Matematika',            $budi, $budi, 'DEMO0001'],
            ['X MIPA 2',  'Fisika',                $andi, $andi, 'DEMO0002'],
            ['XI RPL 1',  'Pemrograman Web Dasar', $siti, $siti, 'DEMO0003'],
            ['XI RPL 1',  'Basis Data',            $siti, $siti, 'DEMO0004'],
        ];
        $kelasIds = [];
        foreach ($daftarKelas as [$nama, $mapel, $guru, $wali, $kode]) {
            DB::table('classes')->updateOrInsert(
                ['school_id' => $schoolId, 'nama' => $nama, 'mata_pelajaran' => $mapel],
                $this->ada('classes', [
                    'guru_id'           => $guru,
                    'wali_kelas_id'     => $wali,
                    'tahun_ajaran'      => '2026/2027',
                    'semester'          => 'Ganjil',
                    'kapasitas'         => 30,
                    'kode_undangan'     => $kode,
                    'perlu_persetujuan' => false,
                    'status'            => 'aktif',
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ])
            );
            $kelasIds[] = (int) DB::table('classes')
                ->where('school_id', $schoolId)->where('nama', $nama)->where('mata_pelajaran', $mapel)->value('id');
        }

        // ---- Pendaftaran: tiap siswa ikut 3 dari 4 kelas ----
        $terdaftar = []; // [index kelas => [id siswa, ...]]
        foreach ($siswa as $i => $uid) {
            foreach ($kelasIds as $k => $cid) {
                if ($k === $i % 4) {
                    continue;
                }
                DB::table('enrollments')->updateOrInsert(
                    ['school_id' => $schoolId, 'class_id' => $cid, 'user_id' => $uid],
                    ['status' => 'aktif', 'created_at' => $now, 'updated_at' => $now]
                );
                $terdaftar[$k][] = $uid;
            }
        }

        // ---- Tugas: [index kelas, judul, selisih hari deadline, jam, menit] ----
        $daftarTugas = [
            [0, 'Latihan Soal Turunan Fungsi',   1, 23, 59],
            [0, 'Quiz Integral',                -3, 12,  0],
            [1, 'Laporan Praktikum Gerak Lurus', 4, 15,  0],
            [1, 'Soal Hukum Newton',            -5, 12,  0],
            [2, 'Membuat Layout CRUD Laravel',   2, 12,  0],
            [2, 'Latihan HTML dan CSS',         -2, 23, 59],
            [3, 'Desain ERD Perpustakaan',       6, 23, 59],
            [3, 'Latihan Query SQL',            -4, 12,  0],
        ];

        foreach ($daftarTugas as $t => [$k, $judul, $hari, $jam, $menit]) {
            $deadline = now()->addDays($hari)->setTime($jam, $menit);

            DB::table('assignments')->updateOrInsert(
                ['school_id' => $schoolId, 'class_id' => $kelasIds[$k], 'judul' => $judul],
                [
                    'deskripsi'         => 'Tugas contoh: '.$judul.'.',
                    'deadline'          => $deadline,
                    'izinkan_terlambat' => false,
                    'nilai_maks'        => 100,
                    'created_at'        => now()->subDays(max(1, 7 - abs($hari))),
                    'updated_at'        => $now,
                ]
            );
            $assignmentId = (int) DB::table('assignments')
                ->where('school_id', $schoolId)->where('class_id', $kelasIds[$k])->where('judul', $judul)->value('id');

            // ---- Pengumpulan ----
            foreach (($terdaftar[$k] ?? []) as $j => $uid) {
                $lewat = $hari < 0;

                // Tugas lewat deadline: sebagian besar sudah dikumpulkan dan dinilai.
                // Tugas yang masih berjalan: sebagian kecil sudah dikumpulkan, belum dinilai.
                if ($lewat && $j % 4 === 3) { continue; }
                if (! $lewat && $j % 3 !== 0) { continue; }

                DB::table('submissions')->updateOrInsert(
                    ['school_id' => $schoolId, 'assignment_id' => $assignmentId, 'user_id' => $uid],
                    [
                        'jawaban_teks' => 'Jawaban contoh untuk '.$judul.'.',
                        'status'       => $lewat ? 'dinilai' : 'terkumpul',
                        'versi'        => 1,
                        'submitted_at' => $lewat ? $deadline->copy()->subHours(6 + $j) : now()->subHours(2 + $j),
                        'nilai'        => $lewat ? 70 + (($uid * 7 + $t * 5) % 30) : null,
                        'umpan_balik'  => $lewat ? 'Bagus, pertahankan.' : null,
                        'created_at'   => $now,
                        'updated_at'   => $now,
                    ]
                );
            }
        }

        if ($this->command) {
            $this->command->info('Data contoh siap. Semua akun memakai password: Smansa123');
            $this->command->table(['Peran', 'Email'], [
                ['Admin sekolah', 'admin_smansa@gmail.com'],
                ['Guru',          'budi@gmail.com, siti@gmail.com, andi@gmail.com'],
                ['Siswa',         implode(', ', array_slice($emailSiswa, 0, 3)).', ...'],
            ]);
        }
    }

    /** Buat atau perbarui akun, kembalikan id-nya. */
    private function user(string $email, string $name, string $role, ?int $schoolId, ?string $nomorInduk = null, ?string $hp = null): int
    {
        DB::table('users')->updateOrInsert(['email' => $email], $this->ada('users', [
            'name'              => $name,
            'password'          => $this->password,
            'role'              => $role,
            'school_id'         => $schoolId,
            'is_active'         => true,
            'nomor_induk'       => $nomorInduk,
            'no_telepon'        => $hp,
            'email_verified_at' => now(),
            'created_at'        => now(),
            'updated_at'        => now(),
        ]));

        return (int) DB::table('users')->where('email', $email)->value('id');
    }

    /** Hanya sertakan kolom yang benar-benar ada di tabel (supaya aman jika migration belum lengkap). */
    private function ada(string $tabel, array $data): array
    {
        return array_filter($data, fn ($kolom) => Schema::hasColumn($tabel, $kolom), ARRAY_FILTER_USE_KEY);
    }
}