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
            // Hapus foreign key lama (CASCADE)
            $table->dropForeign(['id_survei']);

            // Buat foreign key baru dengan RESTRICT
            $table->foreign('id_survei')
                  ->references('id_survei')->on('surveis')
                  ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blok_sensuses', function (Blueprint $table) {
            $table->dropForeign(['id_survei']);

            // Kembalikan lagi ke CASCADE
            $table->foreign('id_survei')
                  ->references('id_survei')->on('surveis')
                  ->onDelete('cascade');
        });
    }
};
