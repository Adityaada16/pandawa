<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterSurvei extends Model
{
    protected $table = 'master_surveis';
    protected $primaryKey = 'id_master_survei';
    public $timestamps = true;

    protected $fillable = ['nama'];

    public function getRouteKeyName(): string
    {
        return 'id_master_survei';
    }

    public function surveis()
    {
        return $this->hasMany(Survei::class, 'id_master_survei', 'id_master_survei');
    }
}
