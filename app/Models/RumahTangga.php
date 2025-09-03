<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RumahTangga extends Model
{
    /** @use HasFactory<\Database\Factories\RumahTanggaFactory> */
    use HasFactory;
    protected $table = 'rumah_tanggas';
    protected $primaryKey = 'id_rt';
    protected $fillable = [
        'id_bs','nurt','krt','keterangan',
        'waktu_mulai','waktu_selesai','status_proses_pencacahan','belum_selesai',
        'status_sinyal','kirim_pml','ad_pencacahan','ad_pemeriksaan',
        'waktu_mulai_periksa','waktu_selesai_periksa',
        'ad_pengiriman_ke_kako','ad_penerimaan_di_kako','ad_penerimaan_di_pengolahan',
        'knf_kirimipds','ladt','longt','ladt_pml','longt_pml',
        'status_proses_pengawasan','waktu_pengawasan',
        'cacah1','cacah2',
        'periksa1','periksa2','periksa3','periksa4','periksa5','periksa6','periksa7','periksa8','periksa9',
        'periksa10','periksa11','periksa12','periksa13','periksa14','periksa15',
        'kirim1','kirim2',
    ];

    protected $casts = [
        // integer
        'nurt' => 'integer',
        'status_proses_pencacahan' => 'integer',
        'status_sinyal' => 'integer',
        'status_proses_pengawasan' => 'integer',

        // datetime
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
        'belum_selesai' => 'datetime',
        'kirim_pml' => 'datetime',
        'ad_pencacahan' => 'datetime',
        'ad_pemeriksaan' => 'datetime',
        'waktu_mulai_periksa' => 'datetime',
        'waktu_selesai_periksa' => 'datetime',
        'ad_pengiriman_ke_kako' => 'datetime',
        'ad_penerimaan_di_kako' => 'datetime',
        'ad_penerimaan_di_pengolahan' => 'datetime',
        'knf_kirimipds' => 'datetime',
        'waktu_pengawasan' => 'datetime',
    ];

    public function getRouteKeyName(): string
    {
        return 'id_rt';
    }

    /**
     * Relasi ke model BlokSensus
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function blokSensus()
    {
        return $this->belongsTo(BlokSensus::class, 'id_bs', 'id_bs'); // Hubungkan ke tabel bs
    }

}
