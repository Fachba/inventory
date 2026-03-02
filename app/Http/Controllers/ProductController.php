<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Helpers\Response;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $service
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $limit = $request->query('limit', 10);   // default 10
            $offset = $request->query('offset', 0);  // default 0

            $response = $this->service->list($limit, $offset);
            $information = Response::set('OK', $response);
        } catch (\Throwable $th) {
            //throw $th;
            $information = Response::setError($th);
        }

        return response()->json($information);
    }

    public function show($id): JsonResponse
    {
        try {
            $response = $this->service->detail($id);
            $information = Response::set('OK', $response);
        } catch (\Throwable $th) {
            //throw $th;
            $information = Response::setError($th);
        }

        return response()->json($information);
    }
}
