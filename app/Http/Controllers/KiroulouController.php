<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *   version="0.0.1",
 *   title="Kiroulou API Documentation",
 *   description="Documentation de l'API utilisée par l'application mobile KiRoulOu, faite avec Laravel",
 *   @OA\Contact(email="bezedache29@gmail.com"),
 *   @OA\License(
 *     name="Apache 2.0",
 *     url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *   )
 * )
 *
 * @OA\Server(
 *   url=L5_SWAGGER_CONST_HOST,
 *   description="Server API KiRoulOu"
 * )
 * 
 * @OA\Tag(
 *     name="Auth",
 *     description="Opérations d'authentification"
 * )
 * 
 * @OA\Tag(
 *     name="Posts",
 *     description="Opérations en relation avec les articles users & clubs"
 * )
 * 
 * @OA\Tag(
 *     name="Users",
 *     description="Opérations en relation avec les utilisateurs"
 * )
 * 
 * @OA\Tag(
 *     name="Clubs",
 *     description="Opérations en relation avec les clubs"
 * )
 * 
 * @OA\SecurityScheme(
 *   type="http",
 *   description="S'authentifier avec email & password pour récupérer un token d'authentification",
 *   name="Authorization",
 *   in="header",
 *   scheme="bearer",
 *   bearerFormat="JWT",
 *   securityScheme="bearer_token",
 * )
 * 
 * @OA\Parameter(
 *   name="id",
 *   in="path",
 *   description="ID de la ressource",
 *   required=true,
 *   @OA\Schema(type="integer")
 * )
 * 
 * @OA\Parameter(
 *   name="club_id",
 *   in="path",
 *   description="ID du club",
 *   required=true,
 *   @OA\Schema(type="integer")
 * )
 * 
 * @OA\Parameter(
 *   name="post_id",
 *   in="path",
 *   description="ID du post (uuid)",
 *   required=true,
 *   @OA\Schema(type="string")
 * )
 * 
 * @OA\Parameter(
 *   name="user_id",
 *   in="path",
 *   description="ID du user",
 *   required=true,
 *   @OA\Schema(type="integer")
 * )
 * 
 * @OA\Parameter(
 *   name="page",
 *   in="path",
 *   description="Numéro de la page",
 *   required=true,
 *   @OA\Schema(type="integer")
 * )
 * 
 * @OA\Parameter(
 *   name="type",
 *   in="path",
 *   description="Type de ressource",
 *   required=true,
 *   @OA\Schema(type="string")
 * )
 * 
 * @OA\Response(
 *   response=409,
 *   response="Conflit",
 *   description="Conflit de ressource",
 *   @OA\JsonContent(
 *     @OA\Property(property="message", type="string", example="Un compte avec cette adresse email existe déjà")
 *   )
 * )
 * 
 * @OA\Response(
 *   response=201,
 *   response="Created",
 *   description="Ressource créée",
 *   @OA\JsonContent(
 *     @OA\Property(property="message", type="string", example="user created")
 *   )
 * ),
 * 
 * @OA\Response(
 *   response=422,
 *   response="UnprocessableEntity",
 *   description="Ressource non traitée",
 *   @OA\JsonContent(
 *     @OA\Property(
 *       property="email",
 *       type="array",
 *       @OA\Items(),
 *       example={
 *         "L'adresse email est obligatoire",
 *       },
 *     )
 *   )
 * ),
 * 
 * @OA\Response(
 *   response=403,
 *   response="Forbidden",
 *   description="Accès refusé à la ressource demandée",
 *   @OA\JsonContent(
 *     @OA\Property(property="message", type="string", example="email et/ou mot de passe incorrect(s)")
 *   )
 * ),
 * 
 * @OA\Response(
 *   response=404,
 *   response="NotFound",
 *   description="Ressource introuvable",
 *   @OA\JsonContent(
 *     @OA\Property(property="message", type="string", example="Resource not found")
 *   )
 * ),
 */
class KiroulouController
{
}
