<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wargas', function (Blueprint $table) {
            $table->string('no_hp')->after('rt_rw');
            $table->decimal('latitude', 10, 7)->after('alamat');
            $table->decimal('longitude', 10, 7)->after('latitude');
        });

        // Fix kolom rt_rw yang terlalu kecil (3 -> 10)
        Schema::table('wargas', function (Blueprint $table) {
            $table->string('rt_rw', 10)->change();
        });
    }

    public function down(): void
    {
        Schema::table('wargas', function (Blueprint $table) {
            $table->dropColumn(['no_hp', 'latitude', 'longitude']);
            $table->string('rt_rw', 3)->change();
        });
    }
};
