<?php

namespace App\Repositories\Repositories;

use App\Models\StockOpnameDetail;
use App\Repositories\Interface\StockOpnameDetailInterface;

class StockOpnameDetailRepository implements StockOpnameDetailInterface
{
    protected StockOpnameDetail $model;

    public function __construct(StockOpnameDetail $model)
    {
        $this->model = $model;
    }

    /**
     * Ambil semua data dengan pagination (limit & offset)
     */
    public function getAllByParent($idParent)
    {
        return $this->model
            ->where('stock_opname_id', $idParent)
            ->whereNull('deleted_at')
            ->orderByDesc('created_at')
            ->get();
    }

    public function find(int $id, int $productId)
    {
        return $this->model
            ->where('stock_opname_id', $id)
            ->where('product_id', $productId)
            ->whereNull('deleted_at')
            ->first();
    }

    /**
     * Create data baru
     */
    public function create(int $idParent, array $data)
    {
        $data['stock_opname_id'] = $idParent;

        return $this->model->create($data);
    }

    /**
     * Update data
     */
    public function update(int $id, array $data)
    {
        $purchaseOrderDetail = $this->model->where('stock_opname_detail_id', $id)->firstOrFail();
        $purchaseOrderDetail->update($data);
        return $purchaseOrderDetail;
    }

    public function inputStockOpname(int $id, int $productId, array $data)
    {
        $purchaseOrderDetail = $this->model->where('stock_opname_id', $id)->where('product_id', $productId)->firstOrFail();
        $purchaseOrderDetail->update($data);
        return $purchaseOrderDetail;
    }

    /**
     * Hapus data (soft delete)
     */
    public function delete(int $userId, int $id)
    {
        $purchaseOrderDetail = $this->model->where('stock_opname_detail_id', $id)->firstOrFail();
        $purchaseOrderDetail->update(['user_del' => $userId, 'deleted_at' => now()]);
        return true;
    }

    public function deleteByParent(int $userId, int $id)
    {
        $purchaseOrderDetail = $this->model->where('stock_opname_id', $id);
        $purchaseOrderDetail->update(['user_del' => $userId, 'deleted_at' => now()]);
        return true;
    }
}
