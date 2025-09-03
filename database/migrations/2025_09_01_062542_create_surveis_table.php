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
        Schema::create('surveis', function (Blueprint $table) {
            $table->id('id_survei');                      // PK: id_survei
            $table->string('kode', 20)->unique();         // contoh: SE2026, ST2025
            $table->string('nama');                       // nama survei
            $table->text('deskripsi')->nullable();        
            $table->unsignedSmallInteger('tahun')->index();   // 2026
            $table->string('periode', 20)->nullable()->index(); // mis: "Triwulan I", "2026-08"
            $table->date('tgl_mulai')->nullable();
            $table->date('tgl_selesai')->nullable();
            $table->enum('status', ['draft','aktif','selesai'])
                  ->default('draft')->index();            // status siklus
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surveis');
    }
};
