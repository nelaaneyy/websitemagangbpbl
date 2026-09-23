<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wargas', function (Blueprint $table) {
            $table->string('id_pelanggan')->nullable()->after('nik');
            $table->decimal('exif_latitude', 10, 8)->nullable()->after('longitude');
            $table->decimal('exif_longitude', 11, 8)->nullable()->after('exif_latitude');
            $table->string('exif_device')->nullable()->after('exif_longitude');
            $table->timestamp('exif_timestamp')->nullable()->after('exif_device');
            $table->boolean('is_exif_valid')->default(true)->after('exif_timestamp');
            $table->double('exif_deviation_meters')->nullable()->after('is_exif_valid');
            $table->double('jarak_tiang_calc')->nullable()->after('exif_deviation_meters');
            $table->string('risiko_duplikasi')->default('rendah')->after('jarak_tiang_calc'); // rendah, sedang, tinggi
            $table->text('catatan_duplikasi')->nullable()->after('risiko_duplikasi');
        });
    }

    public function down(): void
    {
        Schema::table('wargas', function (Blueprint $table) {
            $table->dropColumn([
                'id_pelanggan',
                'exif_latitude',
                'exif_longitude',
                'exif_device',
                'exif_timestamp',
                'is_exif_valid',
                'exif_deviation_meters',
                'jarak_tiang_calc',
                'risiko_duplikasi',
                'catatan_duplikasi',
            ]);
        });
    }
};
