<?php

namespace App\Services;

use App\Repositories\Interface\LogStatusInterface;
use App\Repositories\Interface\PurchaseOrderDetailInterface;
use App\Repositories\Interface\PurchaseOrderInterface;
use Exception;
use Illuminate\Support\Facades\DB;

class PurchaseOrderService
{
    public function __construct(
        private PurchaseOrderInterface $purchaseOrderRepository,
        private PurchaseOrderDetailInterface $purchaseOrderDetailRepository,
        private LogStatusInterface $LogStatusRepository
    ) {}

    // List PurchaseOrder
    public function list($limit, $offset)
    {
        try {
            return $this->purchaseOrderRepository->getAll($limit, $offset);
        } catch (\Exception $e) {
            throw new Exception("List Purchase Order failed: " . $e->getMessage(), is_int($e->getCode()) && $e->getCode() > 0 ? $e->getCode() : 500);
        }
    }

    // Detail PurchaseOrder beserta detail item
    public function detail(int $id)
    {
        try {
            $purchaseOrder = $this->purchaseOrderRepository->find($id);

            if (!$purchaseOrder) {
                throw new \Exception("Purchase Order with ID {$id} not found", 404);
            }

            $purchaseOrder['details'] = $this->purchaseOrderDetailRepository->getAllByParent($id);

            return $purchaseOrder;
        } catch (\Exception $e) {
            // Kembalikan response JSON dengan status 400
            throw $e;
        }
    }

    // Create PurchaseOrder beserta detail
    public function create(array $data)
    {
        DB::beginTransaction();
        try {
            // Ambil data detail jika ada
            $details = $data['details'] ?? [];
            unset($data['details']);

            $data['user_add'] = $data['user_id'];
            $data['user_upd'] = $data['user_id'];

            $purchaseOrder = $this->purchaseOrderRepository->create($data);

            foreach ($details as $item) {
                $this->purchaseOrderDetailRepository->create($purchaseOrder->purchase_order_id, $item);
            }

            $log['new_status'] = 1;
            $log['user_add'] = $data['user_id'];
            $log['user_upd'] = $data['user_id'];

            $this->LogStatusRepository->create($log, "PO", $purchaseOrder->purchase_order_id);

            DB::commit();
            return $this->detail($purchaseOrder->purchase_order_id);
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Create Purchase Order failed: " . $e->getMessage(), is_int($e->getCode()) && $e->getCode() > 0 ? $e->getCode() : 500);
        }
    }

    // Update PurchaseOrder beserta detail
    public function update(int $id, array $data)
    {
        DB::beginTransaction();
        try {
            $purchaseOrder = $this->purchaseOrderRepository->find($id);

            if (!$purchaseOrder) {
                throw new \Exception("Purchase Order with ID {$id} not found", 404);
            }

            $details = $data['details'] ?? [];
            unset($data['details']);

            $data['user_upd'] = $data['user_id'];

            $this->purchaseOrderRepository->update($id, $data);

            // Update detail: disederhanakan dengan delete + create baru
            $this->purchaseOrderDetailRepository->deleteByParent($data['user_id'], $id);

            foreach ($details as $item) {
                $item['purchase_order_id'] = $id;
                $this->purchaseOrderDetailRepository->create($id, $item);
            }

            DB::commit();
            return $this->detail($id);
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Update Purchase Order failed: " . $e->getMessage(), is_int($e->getCode()) && $e->getCode() > 0 ? $e->getCode() : 500);
        }
    }

    // Delete PurchaseOrder beserta detail
    public function delete(int $userId, int $id)
    {
        DB::beginTransaction();
        try {
            $purchaseOrder = $this->purchaseOrderRepository->find($id);

            if (!$purchaseOrder) {
                throw new \Exception("Purchase Order with ID {$id} not found", 404);
            }

            // Hapus semua detail dulu
            $details = $this->purchaseOrderDetailRepository->getAllByParent($id);
            foreach ($details as $d) {
                $this->purchaseOrderDetailRepository->delete($userId, $d['purchase_order_detail_id']);
            }

            // Hapus purchase order
            $this->purchaseOrderRepository->delete($userId, $id);

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Delete Purchase Order failed: " . $e->getMessage(), is_int($e->getCode()) && $e->getCode() > 0 ? $e->getCode() : 500);
        }
    }
}
