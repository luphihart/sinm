<?php

namespace App\Repositories\Eloquent;

use App\Models\Semester;
use App\Repositories\Contracts\SemesterRepositoryInterface;

class SemesterRepository extends BaseRepository implements SemesterRepositoryInterface
{
    public function __construct(Semester $model)
    {
        parent::__construct($model);
    }
}
