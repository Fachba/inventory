<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Helpers\Response;

class RequestProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'request_date' => 'required|date',
            'details' => 'required|array|min:1',
            'details.*.product_id' => 'required|integer',
            'details.*.qty_rp' => 'required|numeric|min:1',
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
