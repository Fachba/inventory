<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Helpers\Response;

class LogStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'modul' => 'required|string',
            'id' => 'required|integer',
            'current_status' => 'required|integer',
            'next_status' => 'required|integer',
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
