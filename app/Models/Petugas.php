<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Petugas extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\PetugasFactory> */
    use HasFactory, Notifiable, HasApiTokens;
    
    protected $table = 'petugas';
    protected $primaryKey = 'id_petugas';
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = [
        'username',
        'password',
        'nama',
        'fp',
        'email',
        'id_role',
        'nip_pegawai',
    ];

    public function getRouteKeyName(): string
    {
        return 'id_petugas';
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi ke model Role
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'id_role'); // Hubungkan ke tabel roles
    }

    /**
     * Cek apakah pengguna memiliki role tertentu
     *
     * @param string|array $roles
     * @return bool
     */
    public function hasRole($roles): bool
    {
        if (is_array($roles)) {
            return in_array($this->role->name, $roles);
        }

        return $this->role->name === $roles;
    }

    /**
     * Relasi ke model BlokSensus
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function blokSensusesSebagaiPcl()
    {
        return $this->hasMany(BlokSensus::class, 'id_petugas_pcl', 'id_petugas'); // Relasi ke tabel blok sensus
    }

    public function blokSensusesSebagaiPml()
    {
        return $this->hasMany(BlokSensus::class, 'id_petugas_pml', 'id_petugas'); // Relasi ke tabel blok sensus
    }
}
