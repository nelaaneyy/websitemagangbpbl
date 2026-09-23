<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wargas', function (Blueprint $table) {
            $table->string('no_kk', 16)->nullable()->after('nik');
            $table->string('no_nidi')->nullable()->after('catatan');
            $table->string('no_slo')->nullable()->after('no_nidi');
            $table->string('file_bast')->nullable()->after('no_slo');
            $table->string('surat_pengantar_desa')->nullable()->after('file_bast');
            $table->string('redundancy_flag')->default('CLEAR')->after('catatan_duplikasi');
            $table->string('foto_sekat_fisik')->nullable()->after('redundancy_flag');
            $table->text('redundancy_resolution_note')->nullable()->after('foto_sekat_fisik');
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->onDelete('set null')->after('redundancy_resolution_note');
        });
    }

    public function down(): void
    {
        Schema::table('wargas', function (Blueprint $table) {
            $table->dropForeign(['created_by_user_id']);
            $table->dropColumn([
                'no_kk',
                'no_nidi',
                'no_slo',
                'file_bast',
                'surat_pengantar_desa',
                'redundancy_flag',
                'foto_sekat_fisik',
                'redundancy_resolution_note',
                'created_by_user_id',
            ]);
        });
    }
};
