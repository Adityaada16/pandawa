<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Petugas extends Model
{
    /** @use HasFactory<\Database\Factories\PetugasFactory> */
    use HasFactory;
    
    protected $table = 'petugas';
    protected $primaryKey = 'id_petugas';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = ['username','password','nama','status','fp','email'];

    public function getRouteKeyName(): string
    {
        return 'id_petugas';
    }

    /**
     * Relasi ke model BlokSensus
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function blokSensusesSebagaiPcl()
    {
        return $this->hasMany(BlokSensus::class, 'id_petugas_pcl', 'id_petugas'); // Relasi ke tabel rumah tangga
    }

    public function blokSensusesSebagaiPml()
    {
        return $this->hasMany(BlokSensus::class, 'id_petugas_pml', 'id_petugas'); // Relasi ke tabel rumah tangga
    }

}
