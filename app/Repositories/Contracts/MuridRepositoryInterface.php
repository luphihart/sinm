<?php

namespace App\Repositories\Contracts;

interface MuridRepositoryInterface extends BaseRepositoryInterface
{
    public function getWithDetails();
    public function findByNisOrNisn($identifier);
}
