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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('id_role')->default(3)->after('nip_pegawai'); // Tambahkan kolom id_role
            $table->foreign('id_role')
                  ->references('id')
                  ->on('roles')
                  ->onDelete('cascade'); // Relasi foreign key ke tabel roles
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['id_role']); // Hapus foreign key
            $table->dropColumn('id_role');   // Hapus kolom id_role
        });
    }
};
