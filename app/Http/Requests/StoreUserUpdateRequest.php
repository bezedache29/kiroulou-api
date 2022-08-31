<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreUserUpdateRequest extends FormRequest
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
            'email' => ['required', 'email', 'unique:users,email,' . $this->user_id],
            'firstname' => ['string', 'nullable'],
            'lastname' => ['string', 'nullable'],
            'address_id' => ['exists:addresses,id'],
            'user_id' => ['required']
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'L\'adresse email est obligatoire',
            'email.email' => 'L\'adresse email doit être une adresse email valide',
            'email.unique' => 'L\'adresse email est déjà enregistré sur l\'application',
            'firstname.string' => 'Le prénom doit être une chaine de caratctères',
            'lastname.string' => 'Le nom doit être une chaine de caratctères',
            'address_id.required' => 'L\'adresse est obligatoire',
            'user_id.required' => 'L\'id du user est obligatoire',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $response = response()->json($validator->errors(), 422);

        throw new HttpResponseException($response);
    }
}
