<?php

namespace App\Repositories\Repositories;

use App\Models\RequestProductDetail;
use App\Repositories\Interface\RequestProductDetailInterface;

class RequestProductDetailRepository implements RequestProductDetailInterface
{
    protected RequestProductDetail $model;

    public function __construct(RequestProductDetail $model)
    {
        $this->model = $model;
    }

    /**
     * Ambil semua data dengan pagination (limit & offset)
     */
    public function getAllByParent($idParent)
    {
        return $this->model
            ->where('request_product_id', $idParent)
            ->whereNull('deleted_at')
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Create data baru
     */
    public function create(int $idParent, array $data)
    {
        $data['request_product_id'] = $idParent;

        return $this->model->create($data);
    }

    /**
     * Update data
     */
    public function update(int $id, array $data)
    {
        $purchaseOrderDetail = $this->model->where('request_product_detail_id', $id)->firstOrFail();
        $purchaseOrderDetail->update($data);
        return $purchaseOrderDetail;
    }

    /**
     * Hapus data (soft delete)
     */
    public function delete(int $userId, int $id)
    {
        $purchaseOrderDetail = $this->model->where('request_product_detail_id', $id)->firstOrFail();
        $purchaseOrderDetail->update(['user_del' => $userId, 'deleted_at' => now()]);
        return true;
    }

    public function deleteByParent(int $userId, int $id)
    {
        $purchaseOrderDetail = $this->model->where('request_product_id', $id);
        $purchaseOrderDetail->update(['user_del' => $userId, 'deleted_at' => now()]);
        return true;
    }
}
