<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PostUser;
use App\Models\PostUserLike;
use Illuminate\Http\Request;
use App\Models\PostUserImage;
use App\Models\PostUserComment;
use Illuminate\Support\Facades\Validator;

class UserPostController extends Controller
{
    /**
     * @OA\Post(
     *   tags={"Users"},
     *   path="/users/posts",
     *   summary="Create user post",
     *   description="Ajout d'un article par un user",
     *   security={{ "bearer_token": {} }},
     *   @OA\RequestBody(ref="#/components/requestBodies/AddPostUser"),
     *   @OA\Response(
     *     response=201,
     *     description="Article d'un user créé",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="message",
     *         type="string",
     *         example="post created"
     *       ),
     *       @OA\Property(
     *         property="post_user_id",
     *         type="string",
     *         example="e6c64643-6ac9-4adb-87d2-bfe121dc4580"
     *       ),
     *     ),
     *   ),
     *   @OA\Response(
     *     response=422, 
     *     ref="#/components/responses/UnprocessableEntity"
     *   ),
     * )
     */
    public function storePost(Request $request)
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
            'user_id' => $request->user()->id
        ];

        $post = PostUser::create($data);

        if ($request->image) {
            //TODO: Enregistrement de l'image en DB

            PostUserImage::create([
                'post_user_id' => $post->id,
                'user_id' => $request->user()->id,
                'image' => 'image-name.png'
            ]);
        }

        // $post = PostUser::with('images')->findOrFail($post->id);

        return response()->json([
            'message' => 'post created',
            'post_user_id' => $post->id
        ], 201);
    }

    /**
     * @OA\Get(
     *   tags={"Users"},
     *   path="/users/{user_id}/posts?page={page}",
     *   summary="All user's posts",
     *   security={{ "bearer_token": {} }},
     *   @OA\Parameter(ref="#/components/parameters/user_id"),
     *   @OA\Parameter(ref="#/components/parameters/page"),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       type="array",
     *       @OA\Items(ref="#/components/schemas/PostUserCounts"),
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     ref="#/components/responses/NotFound"
     *   )
     * )
     */
    public function posts(User $user)
    {
        // On récupère les posts du user avec le nb de likes et de commentaires
        $posts = PostUser::where('user_id', $user->id)
            ->withCount('postUserLikes')
            ->withCount('postUserComments')
            ->paginate(10)
            ->items();

        return response()->json($posts, 200);
    }

    /**
     * @OA\Post(
     *   tags={"Users"},
     *   path="/users/posts/{post_id}/likeOrUnlike",
     *   summary="Like or unlike user post",
     *   description="Aimer ou ne plus aimer un article d'un user",
     *   security={{ "bearer_token": {} }},
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
     *         ref="#/components/schemas/PostUserCounts"
     *       )
     *     ),
     *   ),
     *   @OA\Response(
     *     response=422, 
     *     ref="#/components/responses/UnprocessableEntity"
     *   ),
     * )
     */
    public function likeOrUnlike(Request $request, PostUser $post)
    {
        // Check si le post a été like par le user
        $is_liked = PostUserLike::where('user_id', $request->user()->id)->where('post_user_id', $post->id)->first();

        if ($is_liked) {
            //unlike
            $is_liked->delete();
            $message = 'unliked';
        } else {
            PostUserLike::create([
                'user_id' => $request->user()->id,
                'post_user_id' => $post->id
            ]);
            $message = 'liked';
        }

        $post = PostUser::withCount('postUserLikes')->withCount('postUserComments')->findOrFail($post->id);

        // $likes_count = $post->postUserLikes->count();

        return response()->json([
            'message' => $message,
            'post' => $post,
        ], 201);
    }

    /**
     * @OA\Post(
     *   tags={"Users"},
     *   path="/users/posts/{post_id}/comments",
     *   summary="Create user post comment",
     *   description="Ajout d'un commentaire à un article d'un user",
     *   security={{ "bearer_token": {} }},
     *   @OA\Parameter(ref="#/components/parameters/post_id"),
     *   @OA\RequestBody(ref="#/components/requestBodies/AddPostUserComment"),
     *   @OA\Response(
     *     response=201,
     *     description="Commentaire d'article de user créé",
     *     @OA\JsonContent(
     *       @OA\Property(
     *         property="message",
     *         type="string",
     *         example="comment created"
     *       ),
     *       @OA\Property(
     *         property="comments",
     *         ref="#/components/schemas/PostUserComment"
     *       )
     *     ),
     *   ),
     *   @OA\Response(
     *     response=422, 
     *     ref="#/components/responses/UnprocessableEntity"
     *   ),
     * )
     */
    public function storeComment(Request $request, PostUser $post)
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
            'post_user_id' => $post->id,
            'message' => $request->message
        ];

        $comment = PostUserComment::create($data);

        $comment = PostUserComment::findOrFail($comment->id);

        return response()->json([
            'message' => 'comment created',
            'comment' => $comment
        ], 201);
    }

    /**
     * @OA\Get(
     *   tags={"Users"},
     *   path="/users/posts/{post_id}/comments?page={page}",
     *   summary="All user's posts",
     *   security={{ "bearer_token": {} }},
     *   @OA\Parameter(ref="#/components/parameters/post_id"),
     *   @OA\Parameter(ref="#/components/parameters/page"),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       type="array",
     *       @OA\Items(ref="#/components/schemas/PostUserComment"),
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     ref="#/components/responses/NotFound"
     *   )
     * )
     */
    public function comments(PostUser $post)
    {
        $comments = PostUserComment::where('post_user_id', $post->id)->paginate(10)->items();

        return response()->json($comments, 200);
    }
}
