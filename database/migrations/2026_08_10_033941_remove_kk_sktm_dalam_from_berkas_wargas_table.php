<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('berkas_wargas', function (Blueprint $table) {
            $table->dropColumn(['foto_kk', 'foto_sktm', 'foto_rumah_dalam']);
        });
    }

    public function down(): void
    {
        Schema::table('berkas_wargas', function (Blueprint $table) {
            $table->string('foto_kk')->nullable();
            $table->string('foto_sktm')->nullable();
            $table->string('foto_rumah_dalam')->nullable();
        });
    }
};
