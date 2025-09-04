<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survei extends Model
{
    /** @use HasFactory<\Database\Factories\SurveiFactory> */
    use HasFactory;

    protected $table = 'surveis';
    protected $primaryKey = 'id_survei';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'kode','nama','deskripsi','tahun','periode',
        'tgl_mulai','tgl_selesai','status'
    ];

    protected $casts = [
        'tgl_mulai'   => 'date',
        'tgl_selesai' => 'date'
    ];

    public function getRouteKeyName(): string
    {
        return 'id_survei';
    }

    public function rumahTangga()
    {
        return $this->hasManyThrough(
            RumahTangga::class, 
            BlokSensus::class,  
            'id_survei',                    // FK di BlokSensus -> Survei
            'id_bs',                        // FK di RumahTangga -> BlokSensus
            'id_survei',                    // PK di Survei
            'id_bs'                         // PK di BlokSensus
        );
    }
    
}
