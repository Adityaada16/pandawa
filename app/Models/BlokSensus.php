<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlokSensus extends Model
{
    /** @use HasFactory<\Database\Factories\BlokSensusFactory> */
    use HasFactory;

    protected $table = 'blok_sensuses';
    protected $primaryKey = 'id_bs';

    protected $fillable = [
        'kode',  
        'id_kab_kota',     
        'kecamatan',
        'desa',
        'nks',  
        'id_petugas_pcl',
        'id_petugas_pml',   
    ];

    public function getRouteKeyName(): string
    {
        return 'id_bs';
    }

    /**
     * Relasi ke model KabKota
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function kabKota()
    {
        return $this->belongsTo(KabKota::class, 'id_kab_kota', 'id_kab_kota'); // Hubungkan ke tabel kabkota
    }

    // Relasi ke model PCL
    public function petugasPcl()
    {
        return $this->belongsTo(Petugas::class, 'id_petugas_pcl', 'id_petugas');
    }

    // Relasi ke model PML
    public function petugasPml()
    {
        return $this->belongsTo(Petugas::class, 'id_petugas_pml', 'id_petugas');
    }

    /**
     * Relasi ke model RumahTangga
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function blok_sensuses()
    {
        return $this->hasMany(RumahTangga::class, 'id_bs', 'id_bs'); // Relasi ke tabel rumah tangga
    }

    
}
