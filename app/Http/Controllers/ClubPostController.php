<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\ClubPost;
use App\Models\ClubPostLike;
use Illuminate\Http\Request;
use App\Models\ClubPostImage;
use App\Models\ClubPostComment;
use Illuminate\Support\Facades\Validator;

class ClubPostController extends Controller
{
    /**
     * @OA\Get(
     *   tags={"Clubs"},
     *   path="/clubs/{club_id}/posts",
     *   summary="All club posts",
     *   security={{ "bearer_token": {} }},
     *   @OA\Parameter(ref="#/components/parameters/club_id"),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       type="array",
     *       @OA\Items(ref="#/components/schemas/ClubPostCounts"),
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     ref="#/components/responses/NotFound"
     *   )
     * )
     */
    public function posts(Request $request, Club $club)
    {
        $posts = ClubPost::where('club_id', $club->id)->withCount('postlikes')->withCount('comments')->get();

        return response()->json($posts, 200);
    }

    /**
     * @OA\Post(
     *   tags={"Clubs"},
     *   path="/clubs/{club_id}/posts",
     *   summary="Create club post",
     *   description="Ajout d'un article par un club",
     *   security={{ "bearer_token": {} }},
     *   @OA\Parameter(ref="#/components/parameters/club_id"),
     *   @OA\RequestBody(ref="#/components/requestBodies/AddClubPost"),
     *   @OA\Response(
     *     response=201,
     *     description="Article d'un club créé",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="message",
     *         type="string",
     *         example="post created"
     *       ),
     *       @OA\Property(
     *         property="post",
     *         ref="#/components/schemas/ClubPostSimple"
     *       )
     *     ),
     *   ),
     *   @OA\Response(
     *     response=422, 
     *     ref="#/components/responses/UnprocessableEntity"
     *   ),
     * )
     */
    public function storePost(Request $request, Club $club)
    {
        // On check les requests
        $validator = Validator::make(
            $request->all(),
            [
                'title' => ['required', 'string'],
                'description' => ['required', 'string'],
            ],
            [
                'title.required' => 'Le titre est obligatoire',
                'title.string' => 'Le titre doit être une chaine de caractères',
                'description.required' => 'La description est obligatoire',
                'description.string' => 'La description doit être une chaine de caractères',
            ]
        );

        // S'il y a une erreur dans la validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'club_id' => $club->id
        ];

        $post = ClubPost::create($data);

        if ($request->image) {
            // TODO: Ajout image en store
            ClubPostImage::create([
                'user_id' => $request->user()->id,
                'club_post_id' => $post->id,
                'image' => 'image-name.png'
            ]);
        }

        $post = ClubPost::findOrFail($post->id);

        return response()->json(['message' => 'post created', 'post' => $post], 201);
    }

    /**
     * @OA\Post(
     *   tags={"Clubs"},
     *   path="/clubs/{club_id}/posts/{post_id}/comments",
     *   summary="Create club post comment",
     *   description="Ajout d'un commentaire à un article de club",
     *   security={{ "bearer_token": {} }},
     *   @OA\Parameter(ref="#/components/parameters/club_id"),
     *   @OA\Parameter(ref="#/components/parameters/post_id"),
     *   @OA\RequestBody(ref="#/components/requestBodies/AddClubPostComment"),
     *   @OA\Response(
     *     response=201,
     *     description="Commentaire d'article de club créé",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="message",
     *         type="string",
     *         example="comment created"
     *       ),
     *       @OA\Property(
     *         property="comments",
     *         ref="#/components/schemas/ClubPostComment"
     *       )
     *     ),
     *   ),
     *   @OA\Response(
     *     response=422, 
     *     ref="#/components/responses/UnprocessableEntity"
     *   ),
     * )
     */
    public function storeComment(Request $request, Club $club, ClubPost $post)
    {
        // On check les requests
        $validator = Validator::make(
            $request->all(),
            [
                'message' => ['required', 'string'],
            ],
            [
                'message.required' => 'Le message est obligatoire',
                'message.string' => 'Le message doit être une chaine de caractères',
            ]
        );

        // S'il y a une erreur dans la validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = [
            'user_id' => $request->user()->id,
            'club_post_id' => $post->id,
            'message' => $request->message
        ];

        $comment = ClubPostComment::create($data);

        return response()->json([
            'message' => 'comment created',
            'comment' => $comment
        ], 201);
    }

    /**
     * @OA\Get(
     *   tags={"Clubs"},
     *   path="/clubs/{club_id}/posts/{post_id}/comments",
     *   summary="All club post comments",
     *   security={{ "bearer_token": {} }},
     *   @OA\Parameter(ref="#/components/parameters/club_id"),
     *   @OA\Parameter(ref="#/components/parameters/post_id"),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       type="array",
     *       @OA\Items(ref="#/components/schemas/ClubPostComment"),
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     ref="#/components/responses/NotFound"
     *   )
     * )
     */
    public function comments(Request $request, Club $club, ClubPost $post)
    {
        $comments = ClubPostComment::where('club_post_id', $post->id)->get();

        return response()->json($comments, 200);
    }

    /**
     * @OA\Post(
     *   tags={"Clubs"},
     *   path="/clubs/{club_id}/posts/{post_id}/likeOrUnlike",
     *   summary="Like or unlike club post",
     *   description="Aimer ou ne plus aimer un article d'un club",
     *   security={{ "bearer_token": {} }},
     *   @OA\Parameter(ref="#/components/parameters/club_id"),
     *   @OA\Parameter(ref="#/components/parameters/post_id"),
     *   @OA\Response(
     *     response=201,
     *     description="Article d'un user créé",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="message",
     *         type="string",
     *         example="liked"
     *       ),
     *       @OA\Property(
     *         property="post",
     *         ref="#/components/schemas/ClubPostLikes"
     *       )
     *     ),
     *   ),
     *   @OA\Response(
     *     response=404,
     *     ref="#/components/responses/NotFound"
     *   )
     * )
     */
    public function likeOrUnlike(Request $request, Club $club, ClubPost $post)
    {
        // Check si le post a été like par le user
        $is_liked = ClubPostLike::where('user_id', $request->user()->id)->where('club_post_id', $post->id)->first();

        if ($is_liked) {
            $is_liked->delete();
            $message = 'unliked';
        } else {
            ClubPostLike::create([
                'user_id' => $request->user()->id,
                'club_post_id' => $post->id
            ]);
            $message = 'liked';
        }

        $post = ClubPost::withCount('postlikes')->findOrFail($post->id);

        return response()->json([
            'message' => $message,
            'post' => $post,
        ], 201);
    }
}
