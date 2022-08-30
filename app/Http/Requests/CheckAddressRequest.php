<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CheckAddressRequest extends FormRequest
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
            'street_address' => ['required', 'string'],
            'city' => ['required', 'string'],
            'zipcode' => ['required', 'string'],
        ];
    }

    public function messages()
    {
        return [
            'street_address.required' => 'L\'adresse est obligatoire',
            'street_address.string' => 'L\'adresse doit être une chaine de caratctères',
            'city.required' => 'La ville est obligatoire',
            'city.string' => 'La ville doit être une chaine de caratctères',
            'zipcode.required' => 'Le code postal est obligatoire',
            'zipcode.string' => 'Le code postal doit être une chaine de caratctères',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $response = response()->json($validator->errors(), 422);

        throw new HttpResponseException($response);
    }
}
