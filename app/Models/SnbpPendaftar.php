<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SnbpPendaftar extends Model
{
    use HasFactory;

    protected $table = 'snbp_pendaftar';

    protected $fillable = [
        'murid_id',
    ];

    public function murid()
    {
        return $this->belongsTo(Murid::class);
    }
}
