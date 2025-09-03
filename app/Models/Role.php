<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    /** @use HasFactory<\Database\Factories\RoleFactory> */
    use HasFactory;

    /**
     * Atribut yang bisa diisi
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', // Nama role (admin_prov dan admin_kabkota)
    ];

    /**
     * Relasi ke model User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class, 'id_role'); // Relasi ke tabel users
    }
}
