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
        Schema::table('surveis', function (Blueprint $table) {
            $table->boolean('laporan')->default(false)->after('status');

            // hapus kolom tanggal
            $table->dropColumn(['tgl_mulai', 'tgl_selesai']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surveis', function (Blueprint $table) {
             $table->dateTime('tgl_mulai')->nullable()->after('periode');
             $table->dateTime('tgl_selesai')->nullable()->after('tgl_mulai');
 
             // hapus kembali kolom laporan
             $table->dropColumn('laporan');
        });
    }
};
