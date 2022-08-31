<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserClubAdminRequest extends FormRequest
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
            'is_club_admin' => ['required', 'boolean'],
            'club_id' => ['required', 'exists:clubs,id'],
        ];
    }

    public function messages()
    {
        return [
            'is_club_admin.required' => 'L\'admin club est obligatoire',
            'is_club_admin.boolean' => 'L\'admin club est vrai ou faux',
            'club_id.required' => 'L\'id du club est obligatoire',
            'club_id.exists' => 'L\'id du club n\'existe pas',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $response = response()->json($validator->errors(), 422);

        throw new HttpResponseException($response);
    }
}
