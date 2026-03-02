<?php

namespace App\Repositories\Interface;

interface StockOpnameDetailInterface
{
    public function getAllByParent(int $idParent);
    public function find(int $id, int $productId);
    public function create(int $idParent, array $data);
    public function update(int $id, array $data);
    public function inputStockOpname(int $id, int $productId, array $data);
    public function delete(int $userId, int $id);
    public function deleteByParent(int $userId, int $idParent);
}
