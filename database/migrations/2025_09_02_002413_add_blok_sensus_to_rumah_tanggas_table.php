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
        Schema::table('rumah_tanggas', function (Blueprint $table) {
        $table->unsignedBigInteger('id_bs')->nullable()->after('id_rt');

        $table->foreign('id_bs')
              ->references('id_bs')
              ->on('blok_sensuses')
              ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rumah_tanggas', function (Blueprint $table) {
            $table->dropForeign(['id_bs']);
            $table->dropColumn('id_bs');
        });
    }
};
