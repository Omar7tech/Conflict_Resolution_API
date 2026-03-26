<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ConflictDetectedException;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
class PostController extends Controller
{
    use ApiResponse;
    public function index(): JsonResponse
    {
        return $this->success(PostResource::collection(Post::all()), 'Posts retrieved successfully');
    }

    public function show(Post $post): JsonResponse
    {
        return $this->success(new PostResource($post), 'Post retrieved successfully');
    }

    public function update(Request $request, Post $post): JsonResponse
    {
        $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
            'version' => 'required|integer',
        ]);
        if ($request->version != $post->version) {
            $diff = $this->calculateDiff($post, $request->only(['title', 'content']));
            throw new ConflictDetectedException(
                $post->version,
                $request->version,
                $diff
            );
        }
        $post->update([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'version' => $post->version + 1,
        ]);

        return $this->success(new PostResource($post), 'Post updated successfully');
    }

    private function calculateDiff(Post $post, array $incoming): array
    {
        $diff = [];
        foreach ($incoming as $key => $value) {
            if ($post->$key !== $value) {
                $diff[$key] = [
                    'current' => $post->$key,
                    'incoming' => $value
                ];
            }
        }
        return $diff;
    }
}
