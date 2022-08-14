<?php

namespace App\Http\Controllers;

use App\Models\ClubMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;
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
                'password' => ['required', 'string'],
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

        return response()->json(["message" => "user created"], 201);
    }

    /**
     * @OA\Post(
     *   path="/login",
     *   summary="Connexion",
     *   description="Connexion d'un utilisateur",
     *   tags={"Auth"},
     *   @OA\RequestBody(ref="#/components/requestBodies/PostUser"),
     *   @OA\Response(
     *     response=200,
     *     description="Accès autorisé à la ressource demandée",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(
     *         property="auth_token",
     *         description="Le token d'authentification reçu par Sanctum de Laravel ainsi que le type de token",
     *         type="object",
     *         example={
     *           "token": "2|kqRoYkSd01vGLlDwkYet4CmNdtic46lOYooJZn8A",
     *           "type": "Bearer",
     *         },
     *         @OA\Property(
     *           property="token",
     *           type="string",
     *           description="Le token fourni par Sanctum de Laravel"
     *         ),
     *         @OA\Property(
     *           property="type",
     *           type="string",
     *           description="Le type de token reçu"
     *         ),
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response=403,
     *     ref="#/components/responses/Forbidden"
     *   ),
     *   @OA\Response(
     *     response=422,
     *     ref="#/components/responses/UnprocessableEntity"
     *   )
     * )
     */
    public function login(Request $request)
    {
        // On check les requests
        $validator = Validator::make(
            $request->all(),
            [
                'email' => ['required', 'email'],
                'password' => ['required'],
            ],
            [
                'email.required' => 'L\'adresse email est obligatoire',
                'email.email' => 'L\'adresse email doit être une adresse email valide',
                'password.required' => 'Le mot de passe est obligatoire',
            ]
        );

        // S'il y a une erreur dans le check
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Check si l'email et le Pwd correspondent
        // Return null si pas trouvé
        $user = User::where('email', $request->email)->first();

        if ($user == null) {
            return response()->json(['message' => "email et/ou mot de passe incorrect(s)"], 403);
        }

        // Return false si pwd invalide
        $is_user_password = Hash::check($request->password, $user->password);

        if (!$is_user_password) {
            return response()->json(['message' => "email et/ou mot de passe incorrect(s)"], 403);
        }

        if ($user !== null && $is_user_password) {
            $token = $user->createToken('auth_token')->plainTextToken;
        }

        return response()->json([
            "auth_token" => [
                "token" => $token,
                "type" => "Bearer",
            ]
        ], 200);
    }

    public function disconnect(Request $request)
    {
        // $user->tokens()->delete();
        $request->user()->tokens()->delete();
    }

    /**
     * @OA\Post(
     *   path="/me",
     *   summary="User",
     *   description="Récupère toutes les infos du user connecté",
     *   tags={"Tests"},
     *   security={{ "bearer_token": {} }},
     *   @OA\Response(
     *     response=200,
     *     description="Accès autorisé à la ressource demandée",
     *   )
     * )
     */
    public function me(Request $request)
    {

        $me = $request->user();

        $full_me = $me->load('address')->load('posts')->load('bikes')->load('followers')->load('followings')->load('subscriptions')->load('clubMember')->load('clubFollows');

        // $admin = ClubMember::where('user_id', $request->user()->id)->where('is_user_admin', true)->first();

        return response()->json($full_me);
    }

    public function unauthenticated()
    {
        return response()->json(["message" => "unauthenticated"]);
    }
}
