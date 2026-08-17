<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('berkas_wargas', function (Blueprint $table) {
            $table->string('foto_sktm')->nullable()->after('foto_tiang_rumah_terdekat');
        });
    }

    public function down(): void
    {
        Schema::table('berkas_wargas', function (Blueprint $table) {
            $table->dropColumn('foto_sktm');
        });
    }
};
