<?php

namespace App\Repositories\Contracts;

interface NilaiRepositoryInterface extends BaseRepositoryInterface
{
    public function getGradesForStudent($studentId);
    public function getGradesBySemester($studentId, $semesterId);
    public function checkDuplicate($studentId, $semesterId, $mapelId, $excludeId = null);
}
