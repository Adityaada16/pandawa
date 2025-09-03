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
        Schema::create('kab_kotas', function (Blueprint $table) {
            $table->id('id_kab_kota');
            $table->string('kode', 4)->unique();
            $table->enum('name', ['bantul','gunungkidul','kulon_progo', 'sleman', 'yogyakarta'])->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kab_kotas');
    }
};
