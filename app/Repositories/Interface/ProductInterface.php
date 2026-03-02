<?php

namespace App\Repositories\Interface;

interface ProductInterface
{
    public function getAll($limit, $offset);
    public function find(int $id);
}
