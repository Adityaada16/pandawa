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
        Schema::create('laporans', function (Blueprint $table) {
            $table->id('id_laporan');
            $table->unsignedBigInteger('id_pertanyaan');
            $table->foreign('id_pertanyaan')
                  ->references('id_pertanyaan')->on('pertanyaans')
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();
            $table->text('jawaban')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporans', function (Blueprint $table) {
            $table->dropForeign(['id_pertanyaan']);
        });
        Schema::dropIfExists('laporans');
    }
};
