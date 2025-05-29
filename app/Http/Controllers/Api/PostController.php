<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Helpers\ActivityLogger;
use App\Models\Post;
use App\Models\Platform;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\PlatformValidators\PlatformContentValidatorFactory;
use App\Repositories\Post\PostRepositoryInterface;



class PostController extends Controller
{
    protected $postRepository;
    public function __construct(PostRepositoryInterface $postRepository)

    {
        $this->middleware('auth:sanctum');
        $this->postRepository = $postRepository;
    }

    /**
     * @OA\Get(
     *     path="/api/posts",
     *     summary="Get list of user's posts with filters",
     *     tags={"Posts"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter posts by status (draft, scheduled, published)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"draft", "scheduled", "published"})
     *     ),
     *     @OA\Parameter(
     *         name="from_date",
     *         in="query",
     *         description="Filter posts scheduled from this date (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="to_date",
     *         in="query",
     *         description="Filter posts scheduled up to this date (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of posts",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Post")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function index(Request $request)
    {
        $filters = $request->only(['status', 'from_date', 'to_date']);
        $userId = $request->user()->id;
    
        $posts = $this->postRepository->paginateByUser($userId, $filters, 10);
    
        return response()->json($posts);
    }

    /**
     * @OA\Post(
     *     path="/api/posts",
     *     summary="Create a new post",
     *     tags={"Posts"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title", "content", "status", "platforms"},
     *             @OA\Property(property="title", type="string", example="My first post"),
     *             @OA\Property(property="content", type="string", example="Content of the post"),
     *             @OA\Property(property="image_url", type="string", format="url", nullable=true, example="https://example.com/image.jpg"),
     *             @OA\Property(property="scheduled_at", type="string", format="date-time", nullable=true, example="2025-05-25T10:00:00Z"),
     *             @OA\Property(property="status", type="string", enum={"draft", "scheduled", "published"}, example="draft"),
     *             @OA\Property(
     *                 property="platforms",
     *                 type="array",
     *                 description="Array of platform IDs to publish this post on",
     *                 @OA\Items(type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Post created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Post")
     *     ),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function store(Request $request)
    {
        $data = $this->validateData($request);
    
        $this->handleImageUpload($request, $data);
    
        $platforms = $this->getPlatformsByIds($data['platforms']);
    
        $validationError = $this->validateContentForPlatforms($platforms, $data['content']);
        if ($validationError) {
            return response()->json(['message' => $validationError], 422);
        }
    
        $data['user_id'] = $request->user()->id;

        $post = $this->postRepository->create($data);
    
        $this->syncPlatforms($post, $data['platforms'], $data['status']);
    
        ActivityLogger::log('Created a new post', $post->title, $post->content);
    
        return response()->json($post->load('platforms'), 201);
    }

    /**
     * @OA\Get(
     *     path="/api/posts/{id}",
     *     summary="Get a single post by ID",
     *     tags={"Posts"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Post ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post details",
     *         @OA\JsonContent(ref="#/components/schemas/Post")
     *     ),
     *     @OA\Response(response=404, description="Post not found"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function show($id, Request $request)
    {
        $post = $this->postRepository->findByIdAndUser($id, $request->user()->id);
    
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }
    
        return response()->json($post);
    }

    /**
     * @OA\Put(
     *     path="/api/posts/{id}",
     *     summary="Update an existing post",
     *     tags={"Posts"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Post ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", example="Updated post title"),
     *             @OA\Property(property="content", type="string", example="Updated content"),
     *             @OA\Property(property="image_url", type="string", format="url", nullable=true, example="https://example.com/image2.jpg"),
     *             @OA\Property(property="scheduled_at", type="string", format="date-time", nullable=true, example="2025-05-25T10:00:00Z"),
     *             @OA\Property(property="status", type="string", enum={"draft", "scheduled", "published"}, example="draft"),
     *             @OA\Property(
     *                 property="platforms",
     *                 type="array",
     *                 @OA\Items(type="integer"),
     *                 description="Array of platform IDs"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Post")
     *     ),
     *     @OA\Response(response=404, description="Post not found"),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function update(Request $request, $id)
    {
        $post = Post::where('user_id', $request->user()->id)->findOrFail($id);
    
        $data = $this->validateData($request);
    
        $this->handleImageUpload($request, $data);
    
        $platforms = $this->getPlatformsByIds($data['platforms']);
    
        $validationError = $this->validateContentForPlatforms($platforms, $data['content']);
        if ($validationError) {
            return response()->json(['message' => $validationError], 422);
        }
    
        $data['user_id'] = $request->user()->id;
        $post = $this->postRepository->update($post ,$data);
   
        $this->syncPlatforms($post, $data['platforms'], $data['status']);

        ActivityLogger::log('Update Post ', $post->title, $post->content);
    
        return response()->json($post->load('platforms'));
    }
    

    /**
     * @OA\Delete(
     *     path="/api/posts/{id}",
     *     summary="Delete a post",
     *     tags={"Posts"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Post ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Post deleted successfully"
     *     ),
     *     @OA\Response(response=404, description="Post not found"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function destroy($id, Request $request)
    {
        $post = $this->postRepository->findByIdAndUser($id, $request->user()->id);
    
        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }
    
        $this->postRepository->delete($post);
    
        return response()->json(null, 204);
    }

    /**
     * @OA\Post(
     *     path="/api/posts/{id}/publish",
     *     summary="Publish a scheduled post immediately",
     *     tags={"Posts"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Post ID to publish",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post published successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Post")
     *     ),
     *     @OA\Response(response=404, description="Post not found"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function publish($id, Request $request)
    {
        $post = $this->postRepository->findByIdAndUser($id, $request->user()->id);
    
        if (!$post || $post->status !== 'scheduled') {
            return response()->json(['message' => 'Post not found or not scheduled'], 404);
        }
    
        $post = $this->postRepository->publish($post);
    
        return response()->json($post->load('platforms'));
    }



    protected function validateData(Request $request): array
{
    return $request->validate([
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'status' => ['required', Rule::in(['draft', 'scheduled', 'published'])],
        'scheduled_at' => 'nullable|date|required_if:status,scheduled',
        'platforms' => 'required|array|min:1',
        'platforms.*' => 'exists:platforms,id',
    ]);
}

protected function handleImageUpload(Request $request, array &$data): void
{
    if ($request->hasFile('image')) {
        $path = $request->file('image')->store('images', 'public');
        $data['image_url'] = asset('storage/' . $path);
    }
}

protected function getPlatformsByIds(array $platformIds)
{
    return Platform::whereIn('id', $platformIds)->get();
}

protected function validateContentForPlatforms($platforms, string $content): ?string
{
    foreach ($platforms as $platform) {
        $validator = PlatformContentValidatorFactory::make($platform->type);
        if ($validator && !$validator->validate($content)) {
            return $validator->getErrorMessage();
        }
    }
    return null;
}

protected function syncPlatforms(Post $post, array $platformIds, string $postStatus): void
{
    $status = in_array($postStatus, ['scheduled', 'draft']) ? 'pending' : 'published';
    $pivotData = [];
    foreach ($platformIds as $platformId) {
        $pivotData[$platformId] = ['status' => $status];
    }
    $post->platforms()->sync($pivotData);
}
}