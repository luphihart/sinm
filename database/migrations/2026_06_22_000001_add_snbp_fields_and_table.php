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
        Schema::table('jurusan', function (Blueprint $table) {
            $table->integer('kuota_snbp')->default(0)->after('nama_jurusan');
        });

        Schema::create('snbp_pendaftar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('murid_id')->unique()->constrained('murid')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('snbp_pendaftar');

        Schema::table('jurusan', function (Blueprint $table) {
            $table->dropColumn('kuota_snbp');
        });
    }
};
