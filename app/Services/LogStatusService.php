<?php

namespace App\Services;

use App\Repositories\Interface\GoodReceiveInterface;
use App\Repositories\Interface\LogStatusInterface;
use App\Repositories\Interface\PurchaseOrderInterface;
use App\Repositories\Interface\RequestProductInterface;
use App\Repositories\Interface\StockOpnameInterface;
use Exception;
use Illuminate\Support\Facades\DB;

class LogStatusService
{
    public function __construct(
        private LogStatusInterface $LogStatusRepository,
        private RequestProductInterface $requestProductRepository,
        private PurchaseOrderInterface $purchaseOrderRepository,
        private GoodReceiveInterface $goodReceiveRepository,
        private StockOpnameInterface $stockOpnameRepository,

    ) {}

    // List LogStatus
    public function list($limit, $offset, $modul, $id)
    {
        try {
            return $this->LogStatusRepository->getAll($limit, $offset, $modul, $id);
        } catch (\Exception $e) {
            throw new Exception("List Log Status failed: " . $e->getMessage(), is_int($e->getCode()) && $e->getCode() > 0 ? $e->getCode() : 500);
        }
    }

    public function create(array $data, $modul, $id)
    {
        DB::beginTransaction();
        try {
            $data['user_add'] = $data['user_id'];
            $data['user_upd'] = $data['user_id'];

            $LogStatus = $this->LogStatusRepository->create($data, $modul, $id);

            $updatedStatus['status_id'] = $data['next_status'];
            $updatedStatus['user_upd'] = $data['user_id'];
            $updatedStatus['new_status'] = $data['new_status'];

            switch ($modul) {
                case 'RM':
                    # code...
                    $this->requestProductRepository->update($id, $updatedStatus);
                    break;
                case 'PO':
                    # code...
                    $this->purchaseOrderRepository->update($id, $updatedStatus);

                    break;
                case 'GR':
                    # code...
                    $this->goodReceiveRepository->update($id, $updatedStatus);

                    break;
                case 'SO':
                    # code...
                    $this->stockOpnameRepository->update($id, $updatedStatus);

                    break;

                default:
                    # code...
                    break;
            }

            DB::commit();
            return $LogStatus;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Create Log Status failed: " . $e->getMessage(), is_int($e->getCode()) && $e->getCode() > 0 ? $e->getCode() : 500);
        }
    }
}
