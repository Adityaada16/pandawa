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
    
}
