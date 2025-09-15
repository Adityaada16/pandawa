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
            $table->string('sls', 50)->nullable()->after('nks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blok_sensuses', function (Blueprint $table) {
            $table->dropColumn('sls');
        });
    }
};
