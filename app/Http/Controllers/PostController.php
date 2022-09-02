<?php

namespace App\Http\Controllers;

use App\Models\ClubPost;
use App\Models\PostUser;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

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
     *       type="array",
     *       @OA\Items(ref="#/components/schemas/ClubPostClubAndCounts"),
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     ref="#/components/responses/NotFound"
     *   ),
     *   @OA\Response(
     *     response=401,
     *     ref="#/components/responses/Unauthorized"
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
        $clubs_posts = ClubPost::with('club')->with('hikeVtt')->withCount('postlikes')
            ->withCount('comments')
            ->orderBy('created_at', 'DESC')
            ->limit(20)
            ->get();

        // On check si une collection est vide pour eviter de merge quelque chose de vide
        if (count($users_posts) > 0 && count($clubs_posts) == 0) {
            $posts = PostUser::with('user')->withCount('postUserLikes')
                ->withCount('postUserComments')
                ->orderBy('created_at', 'DESC')
                ->limit(40)
                ->paginate(5)
                ->items();
        } else if (count($users_posts) == 0 && count($clubs_posts) > 0) {
            $posts = ClubPost::with('club')->with('hikeVtt')->withCount('postlikes')
                ->withCount('comments')
                ->orderBy('created_at', 'DESC')
                ->limit(40)
                ->paginate(5)
                ->items();
        } else {
            // On merge les 2 collections avec une pagination
            // Mettre values en dernier sinon il n'y a que la page 1 qui renvoie un tableau
            // Voit AppServiceProvider pour faire fonctionner le paginate sur un tableau
            $posts = collect($clubs_posts)->merge($users_posts)->sortByDesc('created_at')->paginate(5)->values();
            $posts = $posts->toArray();
        }

        return response()->json(['posts' => $posts], 200);
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
