<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Nomor induk: NIP untuk guru, NIS untuk siswa.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'nomor_induk')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('nomor_induk', 30)->nullable()->after('email');
                $table->index(['school_id', 'nomor_induk']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'nomor_induk')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex(['school_id', 'nomor_induk']);
                $table->dropColumn('nomor_induk');
            });
        }
    }
};