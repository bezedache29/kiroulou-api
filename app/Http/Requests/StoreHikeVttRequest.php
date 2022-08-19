<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreHikeVttRequest extends FormRequest
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
            'description' => ['required', 'string'],
            'public_price' => ['required', 'string'],
            'private_price' => ['string'],
            'date' => ['required', 'date'],
            'street_address' => ['required', 'string'],
            'lat' => ['required'],
            'lng' => ['required'],
            'region' => ['required'],
            'department' => ['required'],
            'department_code' => ['required'],
            'city' => ['required', 'string'],
            'zipcode' => ['required', 'string'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Le nom est obligatoire',
            'name.string' => 'Le nom doit être une chaine de caractères',
            'description.required' => 'La description est obligatoire',
            'description.string' => 'La description doit être une chaine de caractères',
            'public_price.required' => 'Le prix public est obligatoire',
            'public_price.string' => 'Le prix public doit être une chaine de caractères',
            'private_price.string' => 'Le prix licencié doit être une chaine de caractères',
            'date.required' => 'La date est obligatoire',
            'date.date' => 'La date doit être une date valide',
            'club_id.required' => 'L\'id du club est obligatoire',
            'address_id.required' => 'L\'id de l\'adresse est obligatoire',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $response = response()->json($validator->errors(), 422);

        throw new HttpResponseException($response);
    }
}
