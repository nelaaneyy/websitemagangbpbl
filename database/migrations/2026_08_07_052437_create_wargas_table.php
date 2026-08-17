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
        Schema::create('wargas', function (Blueprint $table) {
            $table->id();
            $table->string('nik', 16)->unique();
            $table->string('nama');
            $table->string('kabupaten');
            $table->string('kecamatan');
            $table->string('desa');
            $table->string('rt_rw', 3);
            $table->string('alamat');
            $table->enum('status_verifikasi',[
                'terkirim',
                'pending',
                'diverifikasi_kades',
                'menunggu_verifikasi_pusat',
                'lolos_verifikasi_pusat',
                'ditolak/perlu_perbaikan'
                ])->default('terkirim');
                
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wargas');
    }
};
