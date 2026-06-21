<?php

namespace App\Repositories\Eloquent;

use App\Models\Murid;
use App\Repositories\Contracts\MuridRepositoryInterface;

class MuridRepository extends BaseRepository implements MuridRepositoryInterface
{
    public function __construct(Murid $model)
    {
        parent::__construct($model);
    }

    public function getWithDetails()
    {
        return $this->model->with(['user', 'kelas.jurusan'])->get();
    }

    public function findByNisOrNisn($identifier)
    {
        return $this->model->where('nis', $identifier)
                           ->orWhere('nisn', $identifier)
                           ->first();
    }
}
