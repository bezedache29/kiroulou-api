<?php

namespace App\Http\Controllers;

use App\Models\ClubPost;
use App\Models\PostUser;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * @OA\Get(
     *   tags={"Home"},
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
     *         @OA\Items(ref="#/components/schemas/ClubPostCounts"),
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
        $users_posts = PostUser::withCount('postUserLikes')
            ->withCount('postUserComments')
            ->orderBy('created_at', 'DESC')
            ->limit(20)
            ->get();

        // On récupère les 20 derniers posts des clubs
        $clubs_posts = ClubPost::withCount('postlikes')
            ->withCount('comments')
            ->orderBy('created_at', 'DESC')
            ->limit(20)
            ->get();

        // On merge les 2 collections avec une pagination
        $posts = collect($clubs_posts)->merge($users_posts)->sortByDesc('created_at')->values()->paginate(5);

        return response()->json($posts->toArray(), 200);
    }
}
