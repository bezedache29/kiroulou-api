<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreAuthRequest extends FormRequest
{
    public $messages = [
        'email.required' => 'L\'adresse email est obligatoire',
        'email.email' => 'L\'adresse email doit être une adresse email valide',
        'password.required' => 'Le mot de passe est obligatoire',
    ];

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
        if (request()->routeIs('api.register')) {
            $email_rule = ['required', 'email', 'unique:App\Models\User,email'];
            $this->messages['email.unique'] = 'Un compte avec cette adresse email existe déjà';
        } else if (request()->routeIs('api.login')) {
            $email_rule = ['required', 'email'];
        }

        return [
            'email' => $email_rule,
            'password' => ['required'],
        ];
    }

    public function messages()
    {
        return $this->messages;
    }

    public function failedValidation(Validator $validator)
    {
        if ($validator->errors()->first() == 'Un compte avec cette adresse email existe déjà') {
            $response = response()->json(["message" => $validator->errors()->first()], 409);
            throw new HttpResponseException($response);
        }

        $response = response()->json($validator->errors(), 422);

        throw new HttpResponseException($response);
    }
}
