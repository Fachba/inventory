<?php

namespace App\Http\Controllers;

use App\Services\LogStatusService;
use App\Http\Requests\LogStatusRequest;
use Illuminate\Http\JsonResponse;
use App\Helpers\Response; // pastikan class Response ada
use Illuminate\Http\Request;

class LogStatusController extends Controller
{
    public function __construct(private LogStatusService $service) {}

    protected $statuCode = 200;

    // List LogStatuss
    public function index(): JsonResponse
    {
        try {
            $limit = request('limit', 10);
            $offset = request('offset', 0);
            $modul = request('modul', '');
            $id = request('id', 0);
            $response = $this->service->list($limit, $offset, $modul, $id);
            $information = Response::set('OK', $response);
        } catch (\Throwable $th) {
            $information = Response::setError($th);
            $this->statuCode = $th->getCode() ?: 500;
        }

        return response()->json($information, $this->statuCode);
    }

    // Update LogStatus
    public function create(Request $request, LogStatusRequest $validate): JsonResponse
    {
        try {
            // Ambil data validasi
            $data = $validate->validated();

            $data['user_id'] = $request->auth->user_id;

            // Panggil service
            $response = $this->service->create($data, $data['modul'], $data['id']);
            $information = Response::set('Updated', $response);
        } catch (\Throwable $th) {
            $information = Response::setError($th);
            $this->statuCode = $th->getCode() ?: 500;
        }

        return response()->json($information, $this->statuCode);
    }
}
