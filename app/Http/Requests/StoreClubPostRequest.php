<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreClubPostRequest extends FormRequest
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
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'hike_vtt_id' => ['required', 'exists:hikes_vtt,id']
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Le titre est obligatoire',
            'title.string' => 'Le titre doit être une chaine de caractères',
            'description.required' => 'La description est obligatoire',
            'description.string' => 'La description doit être une chaine de caractères',
            'hike_vtt_id.required' => 'L\'id de la rando est obligatoire',
            'hike_vtt_id.exists' => 'L\'id de la rando n\'existe pas',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $response = response()->json($validator->errors(), 422);

        throw new HttpResponseException($response);
    }
}
