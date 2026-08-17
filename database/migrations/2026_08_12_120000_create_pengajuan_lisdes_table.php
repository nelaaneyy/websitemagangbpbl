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
        Schema::create('pengajuan_lisdes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('desa');
            $table->string('nama_dusun');
            $table->integer('jumlah_kk');
            $table->integer('estimasi_jarak'); // Jarak ke jaringan PLN dalam meter
            $table->text('keterangan_wilayah')->nullable();
            $table->string('surat_permohonan'); // Path file PDF
            $table->string('proposal_lisdes');  // Path file PDF
            $table->string('foto_wilayah');     // Path file gambar
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('status')->default('menunggu_verifikasi'); // 'menunggu_verifikasi', 'disetujui', 'ditolak'
            $table->text('catatan_esdm')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_lisdes');
    }
};
