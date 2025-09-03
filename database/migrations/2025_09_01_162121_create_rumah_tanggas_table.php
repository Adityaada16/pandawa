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
        Schema::create('rumah_tanggas', function (Blueprint $table) {
            $table->id('id_rt');
            $table->integer('nurt');
    
            // Identitas & keterangan
            $table->string('krt', 50);
            $table->text('keterangan')->nullable();
    
            // Waktu pencacahan
            $table->dateTime('waktu_mulai')->nullable();
            $table->dateTime('waktu_selesai')->nullable();
            $table->integer('status_proses_pencacahan')->nullable();
            $table->dateTime('belum_selesai')->nullable();
    
            // Sinyal
            $table->integer('status_sinyal')->nullable()->comment('1 ada, 2 tidak');
    
            // Alur administrasi
            $table->dateTime('kirim_pml')->nullable();
            $table->dateTime('ad_pencacahan')->nullable();
            $table->dateTime('ad_pemeriksaan')->nullable();
            $table->dateTime('waktu_mulai_periksa')->nullable();
            $table->dateTime('waktu_selesai_periksa')->nullable();
            $table->dateTime('ad_pengiriman_ke_kako')->nullable();
            $table->dateTime('ad_penerimaan_di_kako')->nullable();
            $table->dateTime('ad_penerimaan_di_pengolahan')->nullable();
            $table->dateTime('knf_kirimipds')->nullable();
    
            // Koordinat 
            $table->text('ladt')->nullable();
            $table->text('longt')->nullable();
            $table->text('ladt_pml')->nullable();
            $table->text('longt_pml')->nullable();
    
            // Pengawasan
            $table->integer('status_proses_pengawasan')->default(0);
            $table->dateTime('waktu_pengawasan')->nullable();
    
            // Flag/catatan (maks 5 char)
            $table->string('cacah1', 5)->nullable();
            $table->string('cacah2', 5)->nullable();
    
            $table->string('periksa1', 5)->nullable();
            $table->string('periksa2', 5)->nullable();
            $table->string('periksa3', 5)->nullable();
            $table->string('periksa4', 5)->nullable();
            $table->string('periksa5', 5)->nullable();
            $table->string('periksa6', 5)->nullable();
            $table->string('periksa7', 5)->nullable();
            $table->string('periksa8', 5)->nullable();
            $table->string('periksa9', 5)->nullable();
            $table->string('periksa10', 5)->nullable();
            $table->string('periksa11', 5)->nullable();
            $table->string('periksa12', 5)->nullable();
            $table->string('periksa13', 5)->nullable();
            $table->string('periksa14', 5)->nullable();
            $table->string('periksa15', 5)->nullable();
    
            $table->string('kirim1', 5)->nullable();
            $table->string('kirim2', 5)->nullable();
    
            $table->timestamps();
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rumah_tanggas');
    }
};
