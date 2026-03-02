<?php

namespace App\Repositories\Interface;

interface LogStatusInterface
{
    public function getAll($limit, $offset, $modul, $id);
    public function create(array $data, $modul, $id);
}
