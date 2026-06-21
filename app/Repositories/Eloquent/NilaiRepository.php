<?php

namespace App\Repositories\Eloquent;

use App\Models\Nilai;
use App\Repositories\Contracts\NilaiRepositoryInterface;

class NilaiRepository extends BaseRepository implements NilaiRepositoryInterface
{
    public function __construct(Nilai $model)
    {
        parent::__construct($model);
    }

    public function getGradesForStudent($studentId)
    {
        return $this->model->where('murid_id', $studentId)
                           ->with(['semester', 'mataPelajaran'])
                           ->get();
    }

    public function getGradesBySemester($studentId, $semesterId)
    {
        return $this->model->where('murid_id', $studentId)
                           ->where('semester_id', $semesterId)
                           ->with('mataPelajaran')
                           ->get();
    }

    public function checkDuplicate($studentId, $semesterId, $mapelId, $excludeId = null)
    {
        $query = $this->model->where('murid_id', $studentId)
                             ->where('semester_id', $semesterId)
                             ->where('mata_pelajaran_id', $mapelId);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
