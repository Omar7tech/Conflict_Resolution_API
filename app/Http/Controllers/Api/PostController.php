<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ConflictDetectedException;
use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        return response()->json(Post::all());
    }

    public function show($id)
    {
        $post = Post::findOrFail($id);
        return response()->json($post);
    }

    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        // Conflict detection
        if ($request->version != $post->version) {
            $diff = $this->calculateDiff($post, $request->only(['title', 'content']));
            throw new ConflictDetectedException($diff);
        }

        // Update fields
        $post->fill($request->only(['title', 'content']));
        $post->version++;
        $post->save();

        return response()->json($post);
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
