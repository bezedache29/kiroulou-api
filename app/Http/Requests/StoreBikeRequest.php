<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreBikeRequest extends FormRequest
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
            'brand' => ['required', 'string'],
            'model' => ['required', 'string'],
            'bike_type_id' => ['required'],
            'date' => ['date'],
            'weight' => ['string'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Le nom est obligatoire',
            'name.string' => 'Le nom doit être une chaîne de caractères',
            'brand.required' => 'La marque est obligatoire',
            'brand.string' => 'La marque doit être une chaîne de caractères',
            'model.required' => 'Le modèle est obligatoire',
            'model.string' => 'Le modèle doit être une chaîne de caractères',
            'bike_type_id.required' => 'Le type est obligatoire',
            'date.date' => 'La date doit être une date valide',
            'weight.string' => 'Le poids doit être une chaîne de caractères',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $response = response()->json($validator->errors(), 422);

        throw new HttpResponseException($response);
    }
}
