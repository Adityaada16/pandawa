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
        Schema::table('surveis', function (Blueprint $table) {
            $table->unsignedBigInteger('id_master_survei')->after('id_survei');

            // FK: satu master_surveis bisa punya banyak surveis.
            $table->foreign('id_master_survei')
                  ->references('id_master_survei')->on('master_surveis')
                  ->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surveis', function (Blueprint $table) {
            $table->dropForeign(['id_master_survei']);
            $table->dropColumn('id_master_survei');
        });
    }
};
