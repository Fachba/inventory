<?php

namespace App\Services;

use App\Repositories\Interface\LogStatusInterface;
use App\Repositories\Interface\RequestProductDetailInterface;
use App\Repositories\Interface\RequestProductInterface;
use Exception;
use Illuminate\Support\Facades\DB;

class RequestProductService
{
    public function __construct(
        private RequestProductInterface $requestProductRepository,
        private RequestProductDetailInterface $requestProductDetailRepository,
        private LogStatusInterface $LogStatusRepository
    ) {}

    // List RequestProduct
    public function list($limit, $offset)
    {
        try {
            return $this->requestProductRepository->getAll($limit, $offset);
        } catch (\Exception $e) {
            throw new Exception("List Request Product failed: " . $e->getMessage(), is_int($e->getCode()) && $e->getCode() > 0 ? $e->getCode() : 500);
        }
    }

    // Detail RequestProduct beserta detail item
    public function detail(int $id)
    {
        try {
            $requestProduct = $this->requestProductRepository->find($id);

            if (!$requestProduct) {
                throw new \Exception("Request Product with ID {$id} not found", 404);
            }

            $requestProduct['details'] = $this->requestProductDetailRepository->getAllByParent($id);

            return $requestProduct;
        } catch (\Exception $e) {
            // Kembalikan response JSON dengan status 400
            throw $e;
        }
    }

    // Create RequestProduct beserta detail
    public function create(array $data)
    {
        DB::beginTransaction();
        try {
            // Ambil data detail jika ada
            $details = $data['details'] ?? [];
            unset($data['details']);

            $data['user_add'] = $data['user_id'];
            $data['user_upd'] = $data['user_id'];

            $requestProduct = $this->requestProductRepository->create($data);

            foreach ($details as $item) {
                $this->requestProductDetailRepository->create($requestProduct->request_product_id, $item);
            }

            $log['new_status'] = 1;
            $log['user_add'] = $data['user_id'];
            $log['user_upd'] = $data['user_id'];

            $this->LogStatusRepository->create($log, "RM", $requestProduct->request_product_id);

            DB::commit();
            return $this->detail($requestProduct->request_product_id);
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Create Request Product failed: " . $e->getMessage(), is_int($e->getCode()) && $e->getCode() > 0 ? $e->getCode() : 500);
        }
    }

    // Update RequestProduct beserta detail
    public function update(int $id, array $data)
    {
        DB::beginTransaction();
        try {
            $requestProduct = $this->requestProductRepository->find($id);

            if (!$requestProduct) {
                throw new \Exception("Request Product with ID {$id} not found", 404);
            }

            $details = $data['details'] ?? [];
            unset($data['details']);

            $data['user_upd'] = $data['user_id'];

            $this->requestProductRepository->update($id, $data);

            // Update detail: disederhanakan dengan delete + create baru
            $this->requestProductDetailRepository->deleteByParent($data['user_id'], $id);

            foreach ($details as $item) {
                $item['request_product_id'] = $id;
                $this->requestProductDetailRepository->create($id, $item);
            }

            DB::commit();
            return $this->detail($id);
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Update Request Product failed: " . $e->getMessage(), is_int($e->getCode()) && $e->getCode() > 0 ? $e->getCode() : 500);
        }
    }

    // Delete RequestProduct beserta detail
    public function delete(int $userId, int $id)
    {
        DB::beginTransaction();
        try {
            $requestProduct = $this->requestProductRepository->find($id);

            if (!$requestProduct) {
                throw new \Exception("Request Product with ID {$id} not found", 404);
            }

            // Hapus semua detail dulu
            $details = $this->requestProductDetailRepository->getAllByParent($id);
            foreach ($details as $d) {
                $this->requestProductDetailRepository->delete($userId, $d['request_product_detail_id']);
            }

            // Hapus purchase order
            $this->requestProductRepository->delete($userId, $id);

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Delete Request Product failed: " . $e->getMessage(), is_int($e->getCode()) && $e->getCode() > 0 ? $e->getCode() : 500);
        }
    }
}
