<?php

namespace App\Repositories\Interface;

interface GoodReceiveDetailInterface
{
    public function getAllByParent(int $idParent);
    public function create(int $idParent, array $data);
    public function update(int $id, array $data);
    public function delete(int $userId, int $id);
    public function deleteByParent(int $userId, int $idParent);
}
