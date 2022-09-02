<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreTripRequest extends FormRequest
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
            'distance' => ['required', 'string'],
            'height_difference' => ['string'],
            'difficulty' => ['required'],
            'supplies' => ['string'],
        ];
    }

    public function messages()
    {
        return [
            'distance.required' => 'La distance est obligatoire',
            'distance.string' => 'La distance doit être une distance valide',
            'height_difference.string' => 'Le dénivelé doit être un dénivelé valide',
            'difficulty.required' => 'La difficulté est obligatoire',
            'supplies.string' => 'Le ravitaillement doit être un chiffre',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $response = response()->json($validator->errors(), 422);

        throw new HttpResponseException($response);
    }
}
