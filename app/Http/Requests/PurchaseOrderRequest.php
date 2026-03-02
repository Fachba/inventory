<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Helpers\Response;

class PurchaseOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vendor' => 'sometimes|string',
            'po_date' => 'required|date',
            'details' => 'required|array|min:1',
            'details.*.product_id' => 'required|integer',
            'details.*.qty_po' => 'required|numeric|min:1',
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
