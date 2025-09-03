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
            $table->unsignedBigInteger('id_petugas_pcl')->nullable()->after('nks');
            $table->unsignedBigInteger('id_petugas_pml')->nullable()->after('id_petugas_pcl');
    
            // foreign key ke tabel petugas (PK = id_petugas)
            $table->foreign('id_petugas_pcl')
                  ->references('id_petugas')->on('petugas')
                  ->cascadeOnDelete();
    
            $table->foreign('id_petugas_pml')
                  ->references('id_petugas')->on('petugas')
                  ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blok_sensuses', function (Blueprint $table) {
            $table->dropForeign(['id_petugas_pcl']);
            $table->dropForeign(['id_petugas_pml']);
            $table->dropColumn(['id_petugas_pcl','id_petugas_pml']);
        });
    }
};
