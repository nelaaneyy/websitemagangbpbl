<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nipd')->nullable()->after('name');
            $table->string('no_hp')->nullable()->after('desa');
            $table->string('sk_file')->nullable()->after('no_hp');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved')->after('sk_file');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nipd', 'no_hp', 'sk_file', 'status']);
        });
    }
};
