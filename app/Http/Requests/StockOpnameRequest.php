<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Helpers\Response;

class StockOpnameRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'stock_opname_period' => 'required|integer|between:1,12',
            'stock_opname_year'   => 'required|integer|digits:4|between:2000,' . date('Y'),
            'details' => 'required|array|min:1',
            'details.*.product_id' => 'required|integer|distinct',
            // 'details.*.system_stock' => 'required|numeric|min:1',
            // 'details.*.physical_stock' => 'required|numeric|min:1',
        ];
    }

    // Override agar return JSON 400
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->all();
        throw new HttpResponseException(
            response()->json(Response::set('Validation Error', $errors, false), 400)
        );
    }
}
