<?php

namespace App\Services;

use App\Repositories\Interface\GoodReceiveDetailInterface;
use App\Repositories\Interface\GoodReceiveInterface;
use App\Repositories\Interface\LogStatusInterface;
use App\Repositories\Interface\PurchaseOrderInterface;
use Exception;
use Illuminate\Support\Facades\DB;

class GoodReceiveService
{
    public function __construct(
        private PurchaseOrderInterface $purchaseOrderRepository,
        private GoodReceiveInterface $goodReceiveRepository,
        private GoodReceiveDetailInterface $goodReceiveDetailRepository,
        private LogStatusInterface $LogStatusRepository
    ) {}

    // List GoodReceive
    public function list($limit, $offset)
    {
        try {
            return $this->goodReceiveRepository->getAll($limit, $offset);
        } catch (\Exception $e) {
            throw new Exception("List Good Receive failed: " . $e->getMessage(), is_int($e->getCode()) && $e->getCode() > 0 ? $e->getCode() : 500);
        }
    }

    // Detail GoodReceive beserta detail item
    public function detail(int $id)
    {
        try {
            $goodReceive = $this->goodReceiveRepository->find($id);

            if (!$goodReceive) {
                throw new \Exception("Good Receive with ID {$id} not found", 404);
            }

            $goodReceive['details'] = $this->goodReceiveDetailRepository->getAllByParent($id);

            return $goodReceive;
        } catch (\Exception $e) {
            // Kembalikan response JSON dengan status 400
            throw $e;
        }
    }

    // Create GoodReceive beserta detail
    public function create(array $data)
    {
        DB::beginTransaction();
        try {

            $goodReceive = $this->purchaseOrderRepository->find($data['purchase_order_id']);

            if (!$goodReceive) {
                throw new \Exception("Purchase Order with ID {$data['purchase_order_id']} not found", 400);
            }

            // Ambil data detail jika ada
            $details = $data['details'] ?? [];
            unset($data['details']);

            $data['user_add'] = $data['user_id'];
            $data['user_upd'] = $data['user_id'];

            $goodReceive = $this->goodReceiveRepository->create($data);

            foreach ($details as $item) {
                $this->goodReceiveDetailRepository->create($goodReceive->good_receive_id, $item);
            }

            $log['new_status'] = 1;
            $log['user_add'] = $data['user_id'];
            $log['user_upd'] = $data['user_id'];

            $this->LogStatusRepository->create($log, "GR", $goodReceive->good_receive_id);

            DB::commit();
            return $this->detail($goodReceive->good_receive_id);
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Create Good Receive failed: " . $e->getMessage(), is_int($e->getCode()) && $e->getCode() > 0 ? $e->getCode() : 500);
        }
    }

    // Update GoodReceive beserta detail
    public function update(int $id, array $data)
    {
        DB::beginTransaction();
        try {
            $goodReceive = $this->goodReceiveRepository->find($id);

            if (!$goodReceive) {
                throw new \Exception("Good Receive with ID {$id} not found", 404);
            }

            $goodReceive = $this->purchaseOrderRepository->find($data['purchase_order_id']);

            if (!$goodReceive) {
                throw new \Exception("Purchase Order with ID {$data['purchase_order_id']} not found", 400);
            }

            $details = $data['details'] ?? [];
            unset($data['details']);

            $data['user_upd'] = $data['user_id'];

            $this->goodReceiveRepository->update($id, $data);

            // Update detail: disederhanakan dengan delete + create baru
            $this->goodReceiveDetailRepository->deleteByParent($data['user_id'], $id);

            foreach ($details as $item) {
                $item['good_receive_id'] = $id;
                $this->goodReceiveDetailRepository->create($id, $item);
            }

            DB::commit();
            return $this->detail($id);
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Update Good Receive failed: " . $e->getMessage(), is_int($e->getCode()) && $e->getCode() > 0 ? $e->getCode() : 500);
        }
    }

    // Delete GoodReceive beserta detail
    public function delete(int $userId, int $id)
    {
        DB::beginTransaction();
        try {
            $goodReceive = $this->goodReceiveRepository->find($id);

            if (!$goodReceive) {
                throw new \Exception("Good Receive with ID {$id} not found", 404);
            }

            // Hapus semua detail dulu
            $details = $this->goodReceiveDetailRepository->getAllByParent($id);
            foreach ($details as $d) {
                $this->goodReceiveDetailRepository->delete($userId, $d['good_receive_detail_id']);
            }

            // Hapus purchase order
            $this->goodReceiveRepository->delete($userId, $id);

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Delete Good Receive failed: " . $e->getMessage(), is_int($e->getCode()) && $e->getCode() > 0 ? $e->getCode() : 500);
        }
    }
}
