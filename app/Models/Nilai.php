<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nilai extends Model
{
    use HasFactory;

    protected $table = 'nilai';

    protected $fillable = [
        'murid_id',
        'semester_id',
        'mata_pelajaran_id',
        'nilai',
    ];

    public function murid()
    {
        return $this->belongsTo(Murid::class);
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class);
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class, 'mata_pelajaran_id');
    }

    // Predikat Nilai (A, B, C, D, E)
    public function getPredikatAttribute()
    {
        $n = $this->nilai;
        if ($n >= 85) return 'A';
        if ($n >= 75) return 'B';
        if ($n >= 60) return 'C';
        if ($n >= 40) return 'D';
        return 'E';
    }

    // Keterangan Kelulusan Mapel
    public function getKeteranganAttribute()
    {
        return $this->nilai >= 75 ? 'Tuntas' : 'Tidak Tuntas';
    }
}
