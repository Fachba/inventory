<?php

namespace App\Repositories\Repositories;

use App\Models\StockOpname;
use App\Repositories\Interface\StockOpnameInterface;

class StockOpnameRepository implements StockOpnameInterface
{
    protected StockOpname $model;

    public function __construct(StockOpname $model)
    {
        $this->model = $model;
    }

    /**
     * Ambil semua data dengan pagination (limit & offset)
     */
    public function getAll($limit, $offset)
    {
        return $this->model
            ->with('details') // relasi jika ada
            ->whereNull('deleted_at')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->offset($offset)
            ->get();
    }

    /**
     * Cari berdasarkan ID
     */
    public function find(int $id)
    {
        return $this->model
            ->with('details')
            ->where('stock_opname_id', $id)
            ->whereNull('deleted_at')
            ->first();
    }

    /**
     * Create data baru
     */
    public function create(array $data)
    {
        // Set default status jika tidak ada
        if (!isset($data['status_id'])) {
            $data['status_id'] = 1;
        }

        return $this->model->create($data);
    }

    /**
     * Update data
     */
    public function update(int $id, array $data)
    {
        $purchaseOrder = $this->model->where('stock_opname_id', $id)->firstOrFail();
        $purchaseOrder->update($data);
        return $purchaseOrder;
    }

    /**
     * Hapus data (soft delete)
     */
    public function delete(int $userId, int $id)
    {
        $purchaseOrder = $this->model->where('stock_opname_id', $id)->firstOrFail();
        $purchaseOrder->update(['user_del' => $userId, 'deleted_at' => now()]);
        return true;
    }
}
