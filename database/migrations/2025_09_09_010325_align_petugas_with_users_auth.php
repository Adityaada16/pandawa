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
        Schema::table('petugas', function (Blueprint $table) {
            // 1) Hapus kolom lama
            if (Schema::hasColumn('petugas', 'status')) {
                $table->dropColumn('status');
            }

            // 2) Tambah kolom role
            if (!Schema::hasColumn('petugas', 'id_role')) {
                $table->unsignedBigInteger('id_role')->after('id_petugas');
                $table->foreign('id_role')
                  ->references('id')
                  ->on('roles')
                  ->onDelete('cascade'); // Relasi foreign key ke tabel roles
            }

             // 3) Tambah kolom nip
            if (!Schema::hasColumn('petugas', 'nip_pegawai')) {
                $table->string('nip_pegawai', 25)->unique()->nullable()->after('nama');
            }

            // 4) Kolom khas users Laravel
            // email_verified_at
            if (!Schema::hasColumn('petugas', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable()->after('email');
            }

            // remember_token (varchar(100) nullable)
            if (!Schema::hasColumn('petugas', 'remember_token')) {
                $table->rememberToken();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('petugas', function (Blueprint $table) {
            // Lepas FK dulu
            if (Schema::hasColumn('petugas', 'id_role')) {
                $table->dropForeign(['id_role']);
                $table->dropColumn('id_role');
            }

            // Kembalikan kolom status
            if (!Schema::hasColumn('petugas', 'status')) {
                $table->tinyInteger('status')->nullable();
            }

            // Hapus kolom lain yang ditambahkan
            if (Schema::hasColumn('petugas', 'nip_pegawai')) {
                $table->dropColumn('nip_pegawai');
            }
            if (Schema::hasColumn('petugas', 'email_verified_at')) {
                $table->dropColumn('email_verified_at');
            }
            if (Schema::hasColumn('petugas', 'remember_token')) {
                $table->dropColumn('remember_token');
            }
        });
    }
};
