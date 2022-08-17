<?php

namespace App\Http\Controllers;

use App\Models\ClubPost;
use App\Models\PostUser;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * @OA\Get(
     *   tags={"Posts"},
     *   path="/posts?page={page}",
     *   summary="All posts",
     *   description="Les derniers articles des users et clubs par ordre decroissant de création paginés",
     *   security={{ "bearer_token": {} }},
     *   @OA\Parameter(ref="#/components/parameters/page"),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="current_page",
     *         type="number",
     *         example=1,
     *       ),
     *       @OA\Property(
     *         property="data",
     *         type="array",
     *         @OA\Items(ref="#/components/schemas/ClubPostClubAndCounts"),
     *       ),
     *       @OA\Property(
     *         property="last_page",
     *         type="number",
     *         example=3
     *       ),
     *       @OA\Property(
     *         property="total",
     *         type="number",
     *         example=11
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     ref="#/components/responses/NotFound"
     *   )
     * )
     */
    public function posts()
    {
        // On récupère les 20 derniers posts des users
        $users_posts = PostUser::with('user')->withCount('postUserLikes')
            ->withCount('postUserComments')
            ->orderBy('created_at', 'DESC')
            ->limit(20)
            ->get();

        // On récupère les 20 derniers posts des clubs
        $clubs_posts = ClubPost::with('club')->withCount('postlikes')
            ->withCount('comments')
            ->orderBy('created_at', 'DESC')
            ->limit(20)
            ->get();

        // On merge les 2 collections avec une pagination
        $posts = collect($clubs_posts)->merge($users_posts)->sortByDesc('created_at')->values()->paginate(5);

        return response()->json($posts->toArray(), 200);
    }

    /**
     * @OA\Get(
     *   tags={"Posts"},
     *   path="/posts/{post_id}/{type}/show",
     *   summary="Post details",
     *   description="Les détails d'un article avec les images/photos",
     *   security={{ "bearer_token": {} }},
     *   @OA\Parameter(ref="#/components/parameters/post_id"),
     *   @OA\Parameter(ref="#/components/parameters/type"),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       ref="#/components/schemas/ClubPostFull"
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     ref="#/components/responses/NotFound"
     *   )
     * )
     */
    public function show(String $id, String $type)
    {
        switch ($type) {
            case 'club':
                $post = ClubPost::with('images')
                    ->with('club')
                    ->withCount('postlikes')
                    ->withCount('comments')
                    ->findOrFail($id);
                return response()->json($post, 200);
            case 'user':
                $post = PostUser::with('images')
                    ->with('user')
                    ->withCount('postUserLikes')
                    ->withCount('postUserComments')
                    ->findOrFail($id);
                return response()->json($post, 200);

            default:
                return response()->json(['message' => 'ressource not found'], 404);
        }
    }
}
