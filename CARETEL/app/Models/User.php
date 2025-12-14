<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'nim_nip',
        'password',
        'role',
        'phone',
        'foto_profil',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function laporan()
    {
        return $this->hasMany(Laporan::class);
    }

    public function tugasTeknisi()
    {
        return $this->hasMany(Penugasan::class, 'teknisi_id');
    }

    public function penugasanDibuat()
    {
        return $this->hasMany(Penugasan::class, 'admin_id');
    }
}