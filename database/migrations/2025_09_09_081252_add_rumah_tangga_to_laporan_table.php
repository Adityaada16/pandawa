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
        Schema::table('laporans', function (Blueprint $table) {
            $table->unsignedBigInteger('id_rumah_tangga');
            $table->foreign('id_rumah_tangga')
            ->references('id_rt')->on('rumah_tanggas')
            ->cascadeOnUpdate()
            ->restrictOnDelete();

            // 1 RT hanya boleh punya 1 jawaban per pertanyaan
            $table->unique(['id_rumah_tangga','id_pertanyaan'], 'uniq_rt_pertanyaan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporans', function (Blueprint $table) {
            $table->dropForeign(['id_rumah_tangga']);
            $table->dropColumn('id_rumah_tangga');;
        });
    }
};
