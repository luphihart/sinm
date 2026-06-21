<?php

namespace App\Repositories\Eloquent;

use App\Models\Kelas;
use App\Repositories\Contracts\KelasRepositoryInterface;

class KelasRepository extends BaseRepository implements KelasRepositoryInterface
{
    public function __construct(Kelas $model)
    {
        parent::__construct($model);
    }

    public function getWithJurusan()
    {
        return $this->model->with('jurusan')->get();
    }
}
