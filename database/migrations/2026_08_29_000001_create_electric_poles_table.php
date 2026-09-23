<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('electric_poles', function (Blueprint $table) {
            $table->id();
            $table->string('kode_tiang')->unique();
            $table->enum('jenis', ['TR', 'TM', 'Gardu'])->default('TR');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->integer('kapasitas_kva')->nullable()->default(0);
            $table->string('status')->default('aktif'); // aktif, perbaikan, terencana
            $table->string('kondisi')->default('baik');
            $table->string('alamat')->nullable();
            $table->string('desa')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kabupaten')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('electric_poles');
    }
};
