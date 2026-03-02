<?php

namespace App\Services;

use App\Repositories\Interface\ProductInterface;
use Exception;

class ProductService
{
    public function __construct(
        private ProductInterface $repository
    ) {}

    public function list($limit, $offset)
    {
        return $this->repository->getAll($limit, $offset);
    }

    public function detail(int $id)
    {
        return $this->repository->find($id);
    }
}
