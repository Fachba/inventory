<?php

namespace App\Repositories\Repositories;

use App\Models\PurchaseOrderDetail;
use App\Repositories\Interface\PurchaseOrderDetailInterface;

class PurchaseOrderDetailRepository implements PurchaseOrderDetailInterface
{
    protected PurchaseOrderDetail $model;

    public function __construct(PurchaseOrderDetail $model)
    {
        $this->model = $model;
    }

    /**
     * Ambil semua data dengan pagination (limit & offset)
     */
    public function getAllByParent($idParent)
    {
        return $this->model
            ->where('purchase_order_id', $idParent)
            ->whereNull('deleted_at')
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Create data baru
     */
    public function create(int $idParent, array $data)
    {
        $data['purchase_order_id'] = $idParent;

        return $this->model->create($data);
    }

    /**
     * Update data
     */
    public function update(int $id, array $data)
    {
        $purchaseOrderDetail = $this->model->where('purchase_order_detail_id', $id)->firstOrFail();
        $purchaseOrderDetail->update($data);
        return $purchaseOrderDetail;
    }

    /**
     * Hapus data (soft delete)
     */
    public function delete(int $userId, int $id)
    {
        $purchaseOrderDetail = $this->model->where('purchase_order_detail_id', $id)->firstOrFail();
        $purchaseOrderDetail->update(['user_del' => $userId, 'deleted_at' => now()]);
        return true;
    }

    public function deleteByParent(int $userId, int $id)
    {
        $purchaseOrderDetail = $this->model->where('purchase_order_id', $id);
        $purchaseOrderDetail->update(['user_del' => $userId, 'deleted_at' => now()]);
        return true;
    }
}
