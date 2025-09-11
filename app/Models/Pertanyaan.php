<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pertanyaan extends Model
{
    protected $table = 'pertanyaans';
    protected $primaryKey = 'id_pertanyaan';
    public $incrementing = true;

    protected $fillable = ['id_survei','label','pic'];

    public function survei()
    {
        return $this->belongsTo(Survei::class, 'id_survei', 'id_survei');
    }

    public function laporans()
    {
        return $this->hasMany(Laporan::class, 'id_pertanyaan', 'id_pertanyaan');
    }

}
