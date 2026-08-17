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
        Schema::create('desas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_desa');
            $table->string('kabupaten');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->integer('total_rt')->default(0);
            $table->integer('berlistrik_rt')->default(0);
            $table->integer('belum_berlistrik_rt')->default(0);
            $table->decimal('rasio_elektrifikasi', 5, 2)->default(0.00);
            $table->string('status')->default('sebagian'); // full, sebagian, belum
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('desas');
    }
};
