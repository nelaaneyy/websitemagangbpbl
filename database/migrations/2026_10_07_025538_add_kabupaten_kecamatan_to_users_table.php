<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'kabupaten')) {
        Schema::table('users', function (Blueprint $table) {
            $table->string('kabupaten', 100)->nullable()->after('desa');
        });
    }
        if (!Schema::hasColumn('users', 'kecamatan')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('kecamatan', 100)->nullable()->after('kabupaten');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'kecamatan')) {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('kecamatan');
        });
    }
        if (Schema::hasColumn('users', 'kabupaten')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('kabupaten');
            });
        }
    }
};
