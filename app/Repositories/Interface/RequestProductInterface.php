<?php

namespace App\Repositories\Interface;

interface RequestProductInterface
{
    public function getAll($limit, $offset);
    public function find(int $id);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $userId, int $id);
}
