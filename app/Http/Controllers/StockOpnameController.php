<?php

namespace App\Http\Controllers;

use App\Services\StockOpnameService;
use App\Http\Requests\StockOpnameRequest;
use Illuminate\Http\JsonResponse;
use App\Helpers\Response; // pastikan class Response ada
use App\Http\Requests\InputStockOpnameRequest;
use Illuminate\Http\Request;

class StockOpnameController extends Controller
{
    public function __construct(private StockOpnameService $service) {}

    protected $statuCode = 200;

    // List StockOpnames
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

    // Detail StockOpname
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

    // Create StockOpname
    public function store(Request $request, StockOpnameRequest $validate): JsonResponse
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

    // Update StockOpname
    public function update(Request $request, StockOpnameRequest $validate, int $id): JsonResponse
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

    // Input StockOpname
    public function inputOpname(Request $request, InputStockOpnameRequest $validate, int $id): JsonResponse
    {
        try {
            // Ambil data validasi
            $data = $validate->validated();

            $data['user_id'] = $request->auth->user_id;

            // Panggil service
            $response = $this->service->inputStockOpname($id, $data);
            $information = Response::set('Updated', $response);
        } catch (\Throwable $th) {
            $information = Response::setError($th);
            $this->statuCode = $th->getCode() ?: 500;
        }

        return response()->json($information, $this->statuCode);
    }

    // Delete StockOpname
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
