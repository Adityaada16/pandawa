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
             // drop kolom lama
             $table->dropColumn(['kode', 'deskripsi']);

             // tambah kolom baru
             $table->date('survei_mulai')->nullable()->after('periode');
             $table->date('survei_selesai')->nullable()->after('survei_mulai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surveis', function (Blueprint $table) {
             // rollback: kembalikan kode & deskripsi
             $table->string('kode', 20)->unique()->after('id_survei');
             $table->text('deskripsi')->nullable()->after('nama');
 
             // hapus kolom baru
             $table->dropColumn(['survei_mulai', 'survei_selesai']);
        });
    }
};
