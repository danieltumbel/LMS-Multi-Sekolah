<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kolom tambahan untuk form "Tambah Kelas / Rombel".
     * guru_id pada tabel classes dipakai sebagai Guru Pengampu.
     */
    public function up(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            if (! Schema::hasColumn('classes', 'tahun_ajaran')) {
                $table->string('tahun_ajaran', 9)->nullable()->after('mata_pelajaran');   // contoh: 2026/2027
            }
            if (! Schema::hasColumn('classes', 'semester')) {
                $table->string('semester', 10)->nullable()->after('tahun_ajaran');        // Ganjil | Genap
            }
            if (! Schema::hasColumn('classes', 'wali_kelas_id')) {
                $table->foreignId('wali_kelas_id')->nullable()->after('guru_id')
                      ->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('classes', 'kapasitas')) {
                $table->unsignedSmallInteger('kapasitas')->nullable()->after('semester'); // jumlah siswa maksimal
            }
        });
    }

    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            if (Schema::hasColumn('classes', 'wali_kelas_id')) {
                $table->dropConstrainedForeignId('wali_kelas_id');
            }
            foreach (['tahun_ajaran', 'semester', 'kapasitas'] as $kolom) {
                if (Schema::hasColumn('classes', $kolom)) {
                    $table->dropColumn($kolom);
                }
            }
        });
    }
};