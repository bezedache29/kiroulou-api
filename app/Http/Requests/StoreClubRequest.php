<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreClubRequest extends FormRequest
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
            'name' => ['required', 'string'],
            'short_name' => ['string'],
            'street_address' => ['required', 'string'],
            'lat' => ['required'],
            'lng' => ['required'],
            'region' => ['required'],
            'department' => ['required'],
            'department_code' => ['required'],
            'city' => ['required', 'string'],
            'zipcode' => ['required', 'string'],
            'website' => ['string'],
            'organization' => ['required'],
            'avatar' => ['string'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Le nom est obligatoire',
            'street_address.required' => 'L\'adresse est obligatoire',
            'city.required' => 'L\'adresse est obligatoire',
            'zipcode.required' => 'L\'adresse est obligatoire',
            'lat.required' => 'L\'adresse est obligatoire',
            'lng.required' => 'L\'adresse est obligatoire',
            'region.required' => 'L\'adresse est obligatoire',
            'department.required' => 'L\'adresse est obligatoire',
            'department_code.required' => 'L\'adresse est obligatoire',
            'organization.required' => 'L\'organisation est obligatoire',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $response = response()->json($validator->errors(), 422);

        throw new HttpResponseException($response);
    }
}
