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
        if (!Schema::hasTable('pengajuan_batches')) {
            Schema::create('pengajuan_batches', function (Blueprint $table) {
                $table->id();
                $table->string('kode_batch')->unique();
                $table->string('desa');
                $table->string('kecamatan')->nullable();
                $table->string('kabupaten')->nullable();
                $table->integer('tahun_anggaran');
                $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('kades_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('status')->default('draft_staff'); // draft_staff, dikirim_ke_kades, diverifikasi_kades, diajukan_ke_esdm, disetujui_esdm, ditolak
                $table->integer('total_warga')->default(0);
                $table->string('nomor_surat_pengantar')->nullable();
                $table->timestamp('sptjm_accepted_at')->nullable();
                $table->text('catatan_kades')->nullable();
                $table->timestamps();
            });
        }

        Schema::table('wargas', function (Blueprint $table) {
            if (!Schema::hasColumn('wargas', 'batch_id')) {
                $table->foreignId('batch_id')->nullable()->after('id')->constrained('pengajuan_batches')->nullOnDelete();
            }
            if (!Schema::hasColumn('wargas', 'desil')) {
                $table->string('desil', 30)->nullable()->after('rt_rw');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wargas', function (Blueprint $table) {
            if (Schema::hasColumn('wargas', 'batch_id')) {
                $table->dropForeign(['batch_id']);
                $table->dropColumn('batch_id');
            }
            if (Schema::hasColumn('wargas', 'desil')) {
                $table->dropColumn('desil');
            }
        });

        Schema::dropIfExists('pengajuan_batches');
    }
};
