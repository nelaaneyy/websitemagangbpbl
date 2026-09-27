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
        Schema::table('users', function (Blueprint $table) {
            // Menambahkan kolom alamat kantor kepala desa
            if (!Schema::hasColumn('users', 'alamat')) {
                $table->text('alamat')->nullable()->after('desa');
            }

            // Menambahkan logo kecamatan jika belum ada
            if (!Schema::hasColumn('users', 'logo_kecamatan')) {
                $table->string('logo_kecamatan')->nullable()->after('alamat');
            }

            // Menambahkan sk_file jika belum ada
            if (!Schema::hasColumn('users', 'sk_file')) {
                $table->string('sk_file')->nullable()->after('logo_kecamatan');
            }

            // Menambahkan status_akun (untuk verifikasi tim ESDM)
            if (!Schema::hasColumn('users', 'status_akun')) {
                $table->string('status_akun')->default('pending')->after('sk_file');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
