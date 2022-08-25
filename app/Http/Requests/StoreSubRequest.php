<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreSubRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'subscription_id' => ['required', 'string'],
            'subscription_type_id' => ['required', 'numeric'],
        ];
    }

    public function messages()
    {
        return [
            'subscription_id.required' => 'L\'ID du sub est obligatoire',
            'subscription_id.string' => 'L\'ID du sub doit être une chaîne de caractère',
            'subscription_type_id.required' => 'L\'ID du type de sub est obligatoire',
            'subscription_type_id.numeric' => 'L\'ID du type doit être un id valide',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $response = response()->json($validator->errors(), 422);

        throw new HttpResponseException($response);
    }
}
