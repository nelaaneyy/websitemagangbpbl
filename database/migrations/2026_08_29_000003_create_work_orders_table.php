<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warga_id')->constrained('wargas')->onDelete('cascade');
            $table->foreignId('vendor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('petugas_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('nomor_wo')->unique();
            $table->date('tanggal_wo');
            $table->enum('status', ['ditugaskan', 'proses_pemasangan', 'selesai_pemasangan', 'terverifikasi'])->default('ditugaskan');
            $table->string('foto_pemasangan')->nullable();
            $table->string('foto_kwh_terpasang')->nullable();
            $table->string('nomor_kwh_meter')->nullable();
            $table->timestamp('tanggal_pasang')->nullable();
            $table->text('catatan_vendor')->nullable();
            $table->text('catatan_verifikator')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_orders');
    }
};
