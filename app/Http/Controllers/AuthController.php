<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *   path="/register",
     *   summary="Inscription",
     *   description="Inscription d'un utilisateur",
     *   tags={"Auth"},
     *   @OA\RequestBody(ref="#/components/requestBodies/PostUser"),
     *   @OA\Response(
     *     response=201,
     *     ref="#/components/responses/Created"
     *   ),
     *   @OA\Response(
     *     response=409,
     *     ref="#/components/responses/Conflit"
     *   ),
     *   @OA\Response(
     *     response=422,
     *     ref="#/components/responses/UnprocessableEntity"
     *   )
     * )
     */
    public function register(Request $request)
    {
        // On check les requests
        $validator = Validator::make(
            $request->all(),
            [
                'email' => ['required', 'email', 'unique:App\Models\User,email'],
                'password' => ['required', Rules\Password::defaults()],
            ],
            [
                'email.required' => 'L\'adresse email est obligatoire',
                'email.email' => 'L\'adresse email doit être une adresse email valide',
                'email.unique' => 'Un compte avec cette adresse email existe déjà',
                'password.required' => 'Le mot de passe est obligatoire',
            ]
        );

        // S'il y a une erreur dans le check
        if ($validator->fails()) {
            if ($validator->errors()->first() == 'Un compte avec cette adresse email existe déjà') {
                return response()->json(["message" => $validator->errors()->first()], 409);
            }
            return response()->json($validator->errors(), 422);
        }

        // Hash du password
        $password_hashed = Hash::make($request->password);

        $user = [
            'email' => $request->email,
            'password' => $password_hashed,
        ];

        User::create($user);

        return response()->json(["message" => $password_hashed], 201);
    }
}
