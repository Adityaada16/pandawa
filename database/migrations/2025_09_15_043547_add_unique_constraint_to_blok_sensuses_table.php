<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Schema::table('blok_sensuses', function (Blueprint $table) {
        //     $table->unique([
        //         'id_survei',
        //         'id_kab_kota', 
        //         'kecamatan', 
        //         'desa', 
        //         'sls', 
        //         'nks'
        //     ], 'unique_survey_location');
        // });
        DB::statement("
        ALTER TABLE blok_sensuses
        ADD UNIQUE INDEX unique_survey_location
        (
            id_survei,
            id_kab_kota,
            kecamatan(100),
            desa(100),
            sls(50),
            nks(50)
        )
    ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::table('blok_sensuses', function (Blueprint $table) {
        //     $table->dropUnique('unique_survey_location');
        // });
        DB::statement('ALTER TABLE blok_sensuses DROP INDEX unique_survey_location');
    }
};
