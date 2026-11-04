<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'no_telepon')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('no_telepon', 20)->nullable()->after('nomor_induk');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'no_telepon')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('no_telepon');
            });
        }
    }
};