<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\Interface\LogStatusInterface;
use App\Repositories\Interface\ProductInterface;
use App\Repositories\Interface\StockOpnameDetailInterface;
use App\Repositories\Interface\StockOpnameInterface;
use Exception;
use Illuminate\Support\Facades\DB;

class StockOpnameService
{
    public function __construct(
        private ProductInterface $productRepository,
        private StockOpnameInterface $stockOpnameRepository,
        private StockOpnameDetailInterface $stockOpnameDetailRepository,
        private LogStatusInterface $LogStatusRepository
    ) {}

    // List StockOpname
    public function list($limit, $offset)
    {
        try {
            return $this->stockOpnameRepository->getAll($limit, $offset);
        } catch (\Exception $e) {
            throw new Exception("List Stock Opname failed: " . $e->getMessage(), is_int($e->getCode()) && $e->getCode() > 0 ? $e->getCode() : 500);
        }
    }

    // Detail StockOpname beserta detail item
    public function detail(int $id)
    {
        try {
            $stockOpname = $this->stockOpnameRepository->find($id);

            if (!$stockOpname) {
                throw new \Exception("Stock Opname with ID {$id} not found", 404);
            }

            $stockOpname['details'] = $this->stockOpnameDetailRepository->getAllByParent($id);

            return $stockOpname;
        } catch (\Exception $e) {
            // Kembalikan response JSON dengan status 400
            throw $e;
        }
    }

    // Create StockOpname beserta detail
    public function create(array $data)
    {
        DB::beginTransaction();
        try {
            // Ambil data detail jika ada
            $details = $data['details'] ?? [];
            unset($data['details']);

            $data['user_add'] = $data['user_id'];
            $data['user_upd'] = $data['user_id'];

            $stockOpname = $this->stockOpnameRepository->create($data);

            foreach ($details as $item) {
                $product = $this->productRepository->find($item['product_id']);
                if (count($product) < 1) {
                    throw new \Exception("Product with ID {$item['product_id']} not found", 400);
                }
                $this->stockOpnameDetailRepository->create($stockOpname->stock_opname_id, $item);
            }

            $log['new_status'] = 1;
            $log['user_add'] = $data['user_id'];
            $log['user_upd'] = $data['user_id'];

            $this->LogStatusRepository->create($log, "SO", $stockOpname->stock_opname_id);

            DB::commit();
            return $this->detail($stockOpname->stock_opname_id);
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Create Stock Opname failed: " . $e->getMessage(), is_int($e->getCode()) && $e->getCode() > 0 ? $e->getCode() : 500);
        }
    }

    // Update StockOpname beserta detail
    public function update(int $id, array $data)
    {
        DB::beginTransaction();
        try {
            $stockOpname = $this->stockOpnameRepository->find($id);

            if (!$stockOpname) {
                throw new \Exception("Stock Opname with ID {$id} not found", 404);
            }

            $details = $data['details'] ?? [];
            unset($data['details']);

            $data['user_upd'] = $data['user_id'];

            $this->stockOpnameRepository->update($id, $data);

            // Update detail: disederhanakan dengan delete + create baru
            $this->stockOpnameDetailRepository->deleteByParent($data['user_id'], $id);

            foreach ($details as $item) {
                $product = $this->productRepository->find($item['product_id']);
                if (count($product) < 1) {
                    throw new \Exception("Product with ID {$item['product_id']} not found", 400);
                }
                $item['stock_opname_id'] = $id;
                $this->stockOpnameDetailRepository->create($id, $item);
            }

            DB::commit();
            return $this->detail($id);
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Update Stock Opname failed: " . $e->getMessage(), is_int($e->getCode()) && $e->getCode() > 0 ? $e->getCode() : 500);
        }
    }

    public function inputStockOpname(int $id, array $data)
    {
        DB::beginTransaction();
        try {
            $stockOpname = $this->stockOpnameRepository->find($id);

            if (!$stockOpname) {
                throw new \Exception("Stock Opname with ID {$id} not found", 404);
            }

            $details = $data['details'] ?? [];
            unset($data['details']);

            $data['user_upd'] = $data['user_id'];

            foreach ($details as $item) {
                // $item['stock_opname_id'] = $id;
                $cekOpname = $this->stockOpnameDetailRepository->find($id, $item['product_id']);
                if (!$cekOpname) {
                    throw new \Exception("Stock Opname with ID {$id} Product {$item['product_id']} not found", 400);
                }
                $product = $this->productRepository->find($item['product_id']);
                if (count($product) < 1) {
                    throw new \Exception("Product with ID {$item['product_id']} not found", 400);
                }
                $item['system_stock'] = $product[0]->product_stock ?? null;
                $this->stockOpnameDetailRepository->inputStockOpname($id, $item['product_id'], $item);
            }

            DB::commit();
            return $this->detail($id);
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Input Stock Opname failed: " . $e->getMessage(), is_int($e->getCode()) && $e->getCode() > 0 ? $e->getCode() : 500);
        }
    }

    // Delete StockOpname beserta detail
    public function delete(int $userId, int $id)
    {
        DB::beginTransaction();
        try {
            $stockOpname = $this->stockOpnameRepository->find($id);

            if (!$stockOpname) {
                throw new \Exception("Stock Opname with ID {$id} not found", 404);
            }

            // Hapus semua detail dulu
            $details = $this->stockOpnameDetailRepository->getAllByParent($id);
            foreach ($details as $d) {
                $this->stockOpnameDetailRepository->delete($userId, $d['stock_opname_detail_id']);
            }

            // Hapus purchase order
            $this->stockOpnameRepository->delete($userId, $id);

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Delete Stock Opname failed: " . $e->getMessage(), is_int($e->getCode()) && $e->getCode() > 0 ? $e->getCode() : 500);
        }
    }
}
