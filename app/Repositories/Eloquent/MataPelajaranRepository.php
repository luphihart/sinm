<?php

namespace App\Repositories\Eloquent;

use App\Models\MataPelajaran;
use App\Repositories\Contracts\MataPelajaranRepositoryInterface;

class MataPelajaranRepository extends BaseRepository implements MataPelajaranRepositoryInterface
{
    public function __construct(MataPelajaran $model)
    {
        parent::__construct($model);
    }

    public function all()
    {
        return $this->model->orderBy('urutan', 'asc')->get();
    }
}
