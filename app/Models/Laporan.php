<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    protected $table = 'laporans';
    protected $primaryKey = 'id_laporan';
    public $incrementing = true;

    protected $fillable = ['id_rumah_tangga','id_pertanyaan','jawaban'];

    public function rumahTangga() {
        return $this->belongsTo(RumahTangga::class, 'id_rumah_tangga', 'id_rt');
    }

    public function pertanyaan()
    {
        return $this->belongsTo(Pertanyaan::class, 'id_pertanyaan', 'id_pertanyaan');
    }

    // Upsert batch untuk satu RT
    public static function upsertBatchForRt(int $idRt, array $items): void
    {
        foreach ($items as $it) {
            static::updateOrCreate(
                ['id_rumah_tangga' => $idRt, 'id_pertanyaan' => $it['id_pertanyaan']],
                ['jawaban' => $it['jawaban'] ?? null]
            );
        }
    }
}
