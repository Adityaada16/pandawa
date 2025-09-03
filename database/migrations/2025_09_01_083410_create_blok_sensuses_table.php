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
        Schema::create('blok_sensuses', function (Blueprint $table) {
            $table->id('id_bs');
            $table->string('kode', 4);
            $table->string('kecamatan');
            $table->string('desa');
            $table->string('nks');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blok_sensuses');
    }
};
