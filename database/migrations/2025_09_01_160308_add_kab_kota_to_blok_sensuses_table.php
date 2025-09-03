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
        Schema::table('blok_sensuses', function (Blueprint $table) {
          $table->unsignedBigInteger('id_kab_kota')->nullable()->after('kode');

          // relasi ke tabel kab_kotas.id
          $table->foreign('id_kab_kota')
                ->references('id_kab_kota')
                ->on('kab_kotas')
                ->onDelete('cascade'); // hapus BS jika kab/kota-nya dihapus
      });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blok_sensuses', function (Blueprint $table) {
            $table->dropForeign(['id_kab_kota']);
            $table->dropColumn('id_kab_kota');
        });
    }
};
