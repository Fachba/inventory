<?php

namespace App\Http\Controllers;

use App\Services\PurchaseOrderService;
use App\Http\Requests\PurchaseOrderRequest;
use Illuminate\Http\JsonResponse;
use App\Helpers\Response; // pastikan class Response ada
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function __construct(private PurchaseOrderService $service) {}

    protected $statuCode = 200;

    // List PurchaseOrders
    public function index(): JsonResponse
    {
        try {
            $limit = request('limit', 10);
            $offset = request('offset', 0);
            $response = $this->service->list($limit, $offset);
            $information = Response::set('OK', $response);
        } catch (\Throwable $th) {
            $information = Response::setError($th);
            $this->statuCode = $th->getCode() ?: 500;
        }

        return response()->json($information, $this->statuCode);
    }

    // Detail PurchaseOrder
    public function show(int $id): JsonResponse
    {
        try {
            $response = $this->service->detail($id);
            $information = Response::set('OK', $response);
        } catch (\Throwable $th) {
            $information = Response::setError($th);
            $this->statuCode = $th->getCode() ?: 500;
        }

        return response()->json($information, $this->statuCode);
    }

    // Create PurchaseOrder
    public function store(Request $request, PurchaseOrderRequest $validate): JsonResponse
    {
        try {
            // Ambil data validasi
            $data = $validate->validated();

            $data['user_id'] = $request->auth->user_id;

            // Panggil service
            $response = $this->service->create($data);

            $information = Response::set('Created', $response);
        } catch (\Throwable $th) {
            $information = Response::setError($th);
            $this->statuCode = $th->getCode() ?: 500;
        }

        return response()->json($information, $this->statuCode);
    }

    // Update PurchaseOrder
    public function update(Request $request, PurchaseOrderRequest $validate, int $id): JsonResponse
    {
        try {
            // Ambil data validasi
            $data = $validate->validated();

            $data['user_id'] = $request->auth->user_id;

            // Panggil service
            $response = $this->service->update($id, $data);
            $information = Response::set('Updated', $response);
        } catch (\Throwable $th) {
            $information = Response::setError($th);
            $this->statuCode = $th->getCode() ?: 500;
        }

        return response()->json($information, $this->statuCode);
    }

    // Delete PurchaseOrder
    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            $request->auth->user_id;
            $this->service->delete($request->auth->user_id, $id);
            $information = Response::set('Deleted');
        } catch (\Throwable $th) {
            $information = Response::setError($th);
            $this->statuCode = $th->getCode() ?: 500;
        }

        return response()->json($information, $this->statuCode);
    }
}
