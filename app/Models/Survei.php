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
        'kode','nama','deskripsi','tahun','periode','status','laporan'
    ];

    protected $casts = [
        'laporan' => 'boolean',
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

    public function laporans() {
        return $this->hasManyThrough(
            Laporan::class,
            Pertanyaan::class,
            'id_survei',      // FK di Pertanyaan
            'id_pertanyaan',  // FK di Laporan
            'id_survei',
            'id_pertanyaan'
        );
    }

    public function pertanyaans()
    {
        return $this->hasMany(Pertanyaan::class, 'id_survei', 'id_survei');
    }
    
    // ===== Helper progress untuk 1 RT =====

    public function totalPertanyaan(): int
    {
        return $this->pertanyaans()->count();
    }

    public function answeredCountForRt(int $idRt): int
    {
        return $this->laporans()
            ->where('laporans.id_rumah_tangga', $idRt)
            ->whereNotNull('laporans.jawaban')
            ->count();
    }

    public function unansweredForRt(int $idRt)
    {
        // pertanyaan di survei ini yang belum punya jawaban (non-null) untuk RT tsb
        return $this->pertanyaans()
            ->whereDoesntHave('laporans', function ($q) use ($idRt) {
                $q->where('id_rumah_tangga', $idRt)
                  ->whereNotNull('jawaban');
            })
            ->get(['id_pertanyaan','label','pic']);
    }

    public function progressForRt(int $idRt): array
    {
        $total = $this->totalPertanyaan();
        $answered = $this->answeredCountForRt($idRt);
        return [
            'answered' => $answered,
            'total'    => $total,
            'complete' => $total > 0 ? $answered === $total : true,
        ];
    }
}
