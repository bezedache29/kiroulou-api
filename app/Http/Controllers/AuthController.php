<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
     *     response=200,
     *     description="Utilisateur créé",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="user created")
     *     )
     *   )
     * )
     */
    public function register(Request $request)
    {
        return response()->json(["message" => "user created !"]);
    }
}
