<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PostUser;
use App\Models\ClubMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreAuthRequest;
use App\Http\Requests\CheckTokenRequest;
use App\Http\Requests\StoreNewPasswordRequest;
use App\Http\Requests\StoreForgotPasswordRequest;
use App\Notifications\ForgotPasswordNotification;

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
    public function register(StoreAuthRequest $request)
    {
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
    public function login(StoreAuthRequest $request)
    {
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

        $full_me = $me->load('address')->load('posts')->load('bikes')->load('followers')->load('followings')->load('subscriptions')->load('clubFollows')->load('myMembershipRequests')->load('club')->load('clubPostComments')->load('postUserImages');

        // $admin = ClubMember::where('user_id', $request->user()->id)->where('is_user_admin', true)->first();

        return response()->json($full_me);
    }

    /**
     * @OA\Post(
     *   path="/forgot",
     *   summary="Send email for forgot password",
     *   description="Envoie d'un email pour mot de passe oublié",
     *   tags={"Auth"},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       required={"email"},
     *       @OA\Property(
     *         property="email",
     *         type="string",
     *         example="test@gmail.com",
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response=201,
     *     ref="#/components/responses/Created"
     *   ),
     *   @OA\Response(
     *     response=422,
     *     ref="#/components/responses/UnprocessableEntity"
     *   )
     * )
     */
    public function forgot(StoreForgotPasswordRequest $request)
    {
        $email = $request->email;
        $token = rand(1000, 9999);

        // On supprime tous les token reliant cette adresse email de la DB
        DB::table('password_resets')->where('email', $email)->delete();

        // On enregistre le token pour cette adresse mail
        DB::table('password_resets')->insert([
            'email' => $email,
            'token' => $token
        ]);

        $user = User::where('email', $email)->first();

        // On envoie un mail au user avec le token
        $user->notify(new ForgotPasswordNotification($token));

        return response()->json(['message' => 'email send'], 201);
    }

    /**
     * @OA\Post(
     *   path="/verifyResetPassword",
     *   summary="Check token for reset password",
     *   description="Check le token recu par mail pour le changement de mot de passe",
     *   tags={"Auth"},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       required={"email", "token"},
     *       @OA\Property(property="email", type="string", example="test@gmail.com"),
     *       @OA\Property(property="token", type="string", example="8154"),
     *     )
     *   ),
     *   @OA\Response(
     *     response=201,
     *     ref="#/components/responses/Created"
     *   ),
     *   @OA\Response(
     *     response=422,
     *     ref="#/components/responses/UnprocessableEntity"
     *   ),
     *   @OA\Response(
     *     response=404,
     *     ref="#/components/responses/NotFound"
     *   )
     * )
     */
    public function verifyResetPassword(CheckTokenRequest $request)
    {
        $passwordRequest = DB::table('password_resets')->where('token', $request->token)->first();

        if (!$passwordRequest) {
            return response()->json(['message' => 'token not found'], 404);
        }

        $user = User::where('email', $passwordRequest->email)->first();

        if (!$user) {
            return response()->json(['message' => 'user does not exist'], 404);
        }

        return response()->json(['message' => 'token verified'], 201);
    }

    /**
     * @OA\Post(
     *   path="/resetPassword",
     *   summary="Reset user password",
     *   description="Change le mot de passe de l'utilisateur",
     *   tags={"Auth"},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       required={"password", "confirm_password"},
     *       @OA\Property(property="email", type="string", example="test@gmail.com"),
     *       @OA\Property(property="token", type="string", example="8154"),
     *       @OA\Property(property="password", type="string", example="password"),
     *       @OA\Property(property="confirm_password", type="string", example="password"),
     *     )
     *   ),
     *   @OA\Response(
     *     response=201,
     *     ref="#/components/responses/Created"
     *   ),
     *   @OA\Response(
     *     response=422,
     *     ref="#/components/responses/UnprocessableEntity"
     *   ),
     * )
     */
    public function resetPassword(StoreNewPasswordRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_resets')->where('token', $request->token)->where('email', $request->email)->delete();

        return response()->json(['message' => 'password reseted'], 201);
    }

    public function unauthenticated()
    {
        return response()->json(["message" => "unauthenticated"]);
    }
}
