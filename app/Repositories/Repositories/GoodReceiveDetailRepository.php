<?php

namespace App\Repositories\Repositories;

use App\Models\GoodReceiveDetail;
use App\Repositories\Interface\GoodReceiveDetailInterface;

class GoodReceiveDetailRepository implements GoodReceiveDetailInterface
{
    protected GoodReceiveDetail $model;

    public function __construct(GoodReceiveDetail $model)
    {
        $this->model = $model;
    }

    /**
     * Ambil semua data dengan pagination (limit & offset)
     */
    public function getAllByParent($idParent)
    {
        return $this->model
            ->where('good_receive_id', $idParent)
            ->whereNull('deleted_at')
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Create data baru
     */
    public function create(int $idParent, array $data)
    {
        $data['good_receive_id'] = $idParent;

        return $this->model->create($data);
    }

    /**
     * Update data
     */
    public function update(int $id, array $data)
    {
        $purchaseOrderDetail = $this->model->where('good_receive_detail_id', $id)->firstOrFail();
        $purchaseOrderDetail->update($data);
        return $purchaseOrderDetail;
    }

    /**
     * Hapus data (soft delete)
     */
    public function delete(int $userId, int $id)
    {
        $purchaseOrderDetail = $this->model->where('good_receive_detail_id', $id)->firstOrFail();
        $purchaseOrderDetail->update(['user_del' => $userId, 'deleted_at' => now()]);
        return true;
    }

    public function deleteByParent(int $userId, int $id)
    {
        $purchaseOrderDetail = $this->model->where('good_receive_id', $id);
        $purchaseOrderDetail->update(['user_del' => $userId, 'deleted_at' => now()]);
        return true;
    }
}
