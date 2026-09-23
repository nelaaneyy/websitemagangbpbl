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
        Schema::table('wargas', function (Blueprint $table) {
            $table->boolean('butuh_validasi_realisasi')->default(false)->after('status_verifikasi');
            $table->string('tahun_usulan', 4)->nullable()->after('butuh_validasi_realisasi');
            $table->text('keterangan_import')->nullable()->after('tahun_usulan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wargas', function (Blueprint $table) {
            $table->dropColumn([
                'butuh_validasi_realisasi',
                'tahun_usulan',
                'keterangan_import',
            ]);
        });
    }
};
