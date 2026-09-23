<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lisdes_clusters', function (Blueprint $table) {
            $table->id();
            $table->string('kode_cluster')->unique();
            $table->string('nama_cluster');
            $table->string('desa');
            $table->string('kecamatan')->nullable();
            $table->string('kabupaten')->nullable();
            $table->integer('jumlah_usulan')->default(0);
            $table->double('total_panjang_jaringan_meter')->default(0);
            $table->decimal('centroid_lat', 10, 8)->nullable();
            $table->decimal('centroid_lng', 11, 8)->nullable();
            $table->enum('status', ['draft_proposal', 'diajukan_esdm', 'disetujui_paket', 'dalam_pembangunan', 'selesai'])->default('draft_proposal');
            $table->json('proposal_data')->nullable(); // Data array ID wargas/node pinning
            $table->double('estimasi_anggaran')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lisdes_clusters');
    }
};
