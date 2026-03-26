<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;

class PostController extends Controller
{
    use ApiResponse;

    protected PostService $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    public function index(): JsonResponse
    {
        $posts = $this->postService->getAllPosts();
        return $this->success(PostResource::collection($posts), 'Posts retrieved successfully');
    }

    public function show(Post $post): JsonResponse
    {
        $post = $this->postService->getPost($post);
        return $this->success(new PostResource($post), 'Post retrieved successfully');
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
        ]);

        $post = $this->postService->createPost($validated);

        return $this->success(new PostResource($post), 'Post created successfully', 201);
    }

    public function update(Request $request, Post $post): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
            'version' => 'required|integer',
        ]);

        $post = $this->postService->updatePost($post, $validated);

        return $this->success(new PostResource($post), 'Post updated successfully');
    }

    public function destroy(Request $request, Post $post): JsonResponse
    {
        $version = $request->input('version');
        
        $this->postService->deletePost($post, $version);

        return $this->success(null, 'Post deleted successfully');
    }
}
