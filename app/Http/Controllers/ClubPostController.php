<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClubPostCommentRequest;
use App\Http\Requests\StoreClubPostRequest;
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
     *   path="/clubs/{club_id}/posts?page={page}",
     *   summary="All club posts",
     *   security={{ "bearer_token": {} }},
     *   @OA\Parameter(ref="#/components/parameters/club_id"),
     *   @OA\Parameter(ref="#/components/parameters/page"),
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
        $posts = ClubPost::where('club_id', $club->id)
            ->with('hikeVtt')
            ->withCount('postlikes')
            ->withCount('comments')
            ->paginate(10)
            ->items();

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
    public function storePost(StoreClubPostRequest $request, Club $club)
    {
        $data = $request->all();
        $data['club_id'] = $club->id;

        $post = ClubPost::create($data);

        $post = ClubPost::findOrFail($post->id);

        return response()->json(['message' => 'post created', 'post' => $post], 201);
    }

    /**
     * @OA\Put(
     *   tags={"Clubs"},
     *   path="/clubs/{club_id}/posts/{post_id}",
     *   summary="Update club post",
     *   description="Modification d'un article par un club",
     *   security={{ "bearer_token": {} }},
     *   @OA\Parameter(ref="#/components/parameters/club_id"),
     *   @OA\Parameter(ref="#/components/parameters/post_id"),
     *   @OA\RequestBody(ref="#/components/requestBodies/AddClubPost"),
     *   @OA\Response(
     *     response=201,
     *     description="Article d'un club modifié",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="message",
     *         type="string",
     *         example="post updated"
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
     *   @OA\Response(
     *     response=404, 
     *     ref="#/components/responses/NotFound"
     *   ),
     *   @OA\Response(
     *     response=401, 
     *     ref="#/components/responses/Unauthorized"
     *   ),
     * )
     */
    public function updatePost(StoreClubPostRequest $request, Club $club, ClubPost $post)
    {
        $data = $request->all();

        $post->update($data);

        if ($request->image) {
            //TODO Check si les images on été changé
            // TODO: Ajout image en store
            // ClubPostImage::create([
            //     'user_id' => $request->user()->id,
            //     'club_post_id' => $post->id,
            //     'image' => 'image-name.png'
            // ]);
        }

        $post = ClubPost::findOrFail($post->id);

        return response()->json([
            'message' => 'post updated',
            'post' => $post
        ], 201);
    }

    /**
     * @OA\Delete(
     *   tags={"Clubs"},
     *   path="/clubs/{club_id}/posts/{post_id}",
     *   summary="Delete club post",
     *   description="Suppression d'un article par un club",
     *   security={{ "bearer_token": {} }},
     *   @OA\Parameter(ref="#/components/parameters/club_id"),
     *   @OA\Parameter(ref="#/components/parameters/post_id"),
     *   @OA\Response(
     *     response=201,
     *     ref="#/components/responses/Created"
     *   ),
     *   @OA\Response(
     *     response=404, 
     *     ref="#/components/responses/NotFound"
     *   ),
     *   @OA\Response(
     *     response=401, 
     *     ref="#/components/responses/Unauthorized"
     *   ),
     * )
     */
    public function deletePost(Club $club, ClubPost $post)
    {
        $post->delete();

        return response()->json(['message' => 'post deleted'], 201);
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
    public function storeComment(StoreClubPostCommentRequest $request, Club $club, ClubPost $post)
    {
        $data = [
            'user_id' => $request->user()->id,
            'club_post_id' => $post->id,
            'message' => $request->message
        ];

        $comment = ClubPostComment::create($data);

        $comment = ClubPostComment::with('user')->findOrFail($comment->id);

        return response()->json([
            'message' => 'comment created',
            'comment' => $comment
        ], 201);
    }

    /**
     * @OA\Get(
     *   tags={"Clubs"},
     *   path="/clubs/{club_id}/posts/{post_id}/comments?page={page}",
     *   summary="All club post comments",
     *   security={{ "bearer_token": {} }},
     *   @OA\Parameter(ref="#/components/parameters/club_id"),
     *   @OA\Parameter(ref="#/components/parameters/post_id"),
     *   @OA\Parameter(ref="#/components/parameters/page"),
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
        $comments = ClubPostComment::where('club_post_id', $post->id)
            ->with('user')
            ->orderBy('created_at', 'DESC')
            ->paginate(10)
            ->items();

        return response()->json($comments, 200);
    }

    /**
     * @OA\Put(
     *   tags={"Clubs"},
     *   path="/comments/{comment_id}",
     *   summary="Update club post comment",
     *   description="Modification d'un commentaire à un article de club",
     *   security={{ "bearer_token": {} }},
     *   @OA\Parameter(ref="#/components/parameters/comment_id"),
     *   @OA\RequestBody(ref="#/components/requestBodies/AddClubPostComment"),
     *   @OA\Response(
     *     response=201,
     *     description="Commentaire d'article de club modifié",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="message",
     *         type="string",
     *         example="comment updated"
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
     *   @OA\Response(
     *     response=404, 
     *     ref="#/components/responses/NotFound"
     *   ),
     *   @OA\Response(
     *     response=401, 
     *     ref="#/components/responses/Unauthorized"
     *   ),
     * )
     */
    public function updateComment(StoreClubPostCommentRequest $request, ClubPostComment $comment)
    {
        $comment->update($request->all());

        $comment = ClubPostComment::with('user')->findOrFail($comment->id);

        return response()->json([
            'message' => 'comment updated',
            'comment' => $comment
        ], 201);
    }

    /**
     * @OA\Delete(
     *   tags={"Clubs"},
     *   path="/comments/{comment_id}",
     *   summary="Delete club post comment",
     *   description="Suppression d'un commentaire à un article de club",
     *   security={{ "bearer_token": {} }},
     *   @OA\Parameter(ref="#/components/parameters/comment_id"),
     *   @OA\Response(
     *     response=201,
     *     ref="#/components/responses/Created"
     *   ),
     *   @OA\Response(
     *     response=404, 
     *     ref="#/components/responses/NotFound"
     *   ),
     *   @OA\Response(
     *     response=401, 
     *     ref="#/components/responses/Unauthorized"
     *   ),
     * )
     */
    public function deleteComment(ClubPostComment $comment)
    {
        $comment->delete();

        return response()->json(['message' => 'comment deleted'], 201);
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

        $post = ClubPost::withCount('postLikes')->findOrFail($post->id);

        return response()->json([
            'message' => $message,
            'post' => $post,
        ], 201);
    }

    /**
     * @OA\Get(
     *   tags={"Clubs"},
     *   path="/clubs/{club_id}/posts/{post_id}/isPostLiked",
     *   summary="User like post ?",
     *   description="Permet de savoir si un utilisateur a liké l'article",
     *   security={{ "bearer_token": {} }},
     *   @OA\Parameter(ref="#/components/parameters/club_id"),
     *   @OA\Parameter(ref="#/components/parameters/post_id"),
     *   @OA\Response(
     *     response=200,
     *     ref="#/components/responses/Created"
     *   ),
     *   @OA\Response(
     *     response=404,
     *     ref="#/components/responses/NotFound"
     *   ),
     *   @OA\Response(
     *     response=401, 
     *     ref="#/components/responses/Unauthorized"
     *   ),
     * )
     */
    public function isPostLiked(Request $request, Club $club, ClubPost $post)
    {
        $post = ClubPostLike::where('user_id', $request->user()->id)->where('club_post_id', $post->id)->first();

        if (!$post) {
            return response()->json(['message' => 'unlike'], 404);
        }

        return response()->json(['message' => 'like'], 200);
    }
}
