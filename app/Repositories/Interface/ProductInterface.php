<?php

namespace App\Repositories\Interface;

interface ProductInterface
{
    public function getAll($limit, $offset);
    public function find(int $id);
    public function movementStockProduct(int $id, int $limit, int $offset);
}
