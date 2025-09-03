<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KabKota extends Model
{
    /** @use HasFactory<\Database\Factories\KabKotaFactory> */
    use HasFactory;

    /**
     * Atribut yang bisa diisi
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'kode', 'name'
    ];

     /**
     * Relasi ke model BlokSensus
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function blok_sensuses()
    {
        return $this->hasMany(BlokSensus::class, 'id_kab_kota', 'id_kab_kota'); // Relasi ke tabel blok_sensuses
    }
}
