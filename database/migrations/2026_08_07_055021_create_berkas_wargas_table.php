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
        Schema::create('berkas_wargas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warga_id')->constrained('wargas')->onDelete('cascade');

            $table->string('foto_ktp');
            $table->string('foto_kk');
            $table->string('foto_sktm');
            $table->string('foto_rumah_depan'); //pakai gps camera
            $table->string('foto_rumah_dalam'); //pakai gps camera
            $table->string('foto_kwh_rumah_terdekat'); //pakai gps camera
            $table->string('foto_tiang_rumah_terdekat'); //pakai gps camera

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('berkas_wargas');
    }
};
