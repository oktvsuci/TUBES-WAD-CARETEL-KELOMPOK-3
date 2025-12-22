<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    use HasFactory;

    protected $table = 'laporan';

    protected $fillable = [
        'user_id',
        'kategori_id',
        'teknisi_id',
        'judul',
        'deskripsi',
        'lokasi',
        'foto',
        'prioritas',
        'status',
        'created_by_admin',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'created_by_admin' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public function teknisi()
    {
        return $this->belongsTo(User::class, 'teknisi_id');
    }

    public function penugasan()
    {
        return $this->hasOne(Penugasan::class);
    }

    public function statusHistory()
    {
        return $this->hasMany(StatusHistory::class);
    }

    public function rating()
    {
        return $this->hasOne(Rating::class);
    }

    // ✅ PENTING: Accessor untuk views
    public function getMahasiswaNamaAttribute()
    {
        return $this->user ? $this->user->name : 'Unknown';
    }

    public function getMahasiswaNimAttribute()
    {
        return $this->user ? $this->user->nim_nip : '-';
    }

    public function getTeknisiNamaAttribute()
    {
        return $this->teknisi ? $this->teknisi->name : 'Unassigned';
    }
}