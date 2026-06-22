<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    use HasFactory;

    protected $table = 'jurusan';

    protected $fillable = [
        'kode_jurusan',
        'nama_jurusan',
        'kuota_snbp',
    ];

    public function kelas()
    {
        return $this->hasMany(Kelas::class);
    }

    public function murid()
    {
        return $this->hasManyThrough(Murid::class, Kelas::class);
    }
}
