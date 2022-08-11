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
 * @OA\Response(
 *   response="Conflit",
 *   description="Conflit de ressource",
 *   @OA\JsonContent(
 *     @OA\Property(property="message", type="string", example="Un compte avec cette adresse email existe déjà")
 *   )
 * )
 * 
 * @OA\Response(
 *   response="Created",
 *   description="Ressource créée",
 *   @OA\JsonContent(
 *     @OA\Property(property="message", type="string", example="user created")
 *   )
 * ),
 * 
 * @OA\Response(
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
 */
class KiroulouController
{
}
