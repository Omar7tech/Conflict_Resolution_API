<?php

namespace App\Services;

use App\Exceptions\ConflictDetectedException;
use App\Models\Post;

class PostService
{
    /**
     * Get all posts.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllPosts()
    {
        return Post::all();
    }

    /**
     * Get a single post.
     *
     * @param Post $post
     * @return Post
     */
    public function getPost(Post $post): Post
    {
        return $post;
    }

    /**
     * Create a new post with version 1.
     *
     * @param array $data
     * @return Post
     */
    public function createPost(array $data): Post
    {
        return Post::create([
            'title' => $data['title'],
            'content' => $data['content'],
            'version' => 1,
        ]);
    }

    /**
     * Update a post with optimistic concurrency control.
     *
     * @param Post $post
     * @param array $data
     * @return Post
     * @throws ConflictDetectedException
     */
    public function updatePost(Post $post, array $data): Post
    {
        $this->detectConflict($post, $data['version'], $data);

        $post->update([
            'title' => $data['title'],
            'content' => $data['content'],
            'version' => $post->version + 1,
        ]);

        return $post;
    }

    /**
     * Delete a post with optional version check.
     *
     * @param Post $post
     * @param int|null $version
     * @return void
     * @throws ConflictDetectedException
     */
    public function deletePost(Post $post, ?int $version = null): void
    {
        if ($version !== null) {
            $this->detectConflict($post, $version, []);
        }

        $post->delete();
    }

    /**
     * Detect version conflict and throw exception if conflict exists.
     *
     * @param Post $post
     * @param int $incomingVersion
     * @param array $data
     * @throws ConflictDetectedException
     */
    private function detectConflict(Post $post, int $incomingVersion, array $data): void
    {
        if ($incomingVersion !== $post->version) {
            $diff = $this->calculateDiff($post, $data);

            throw new ConflictDetectedException(
                $post->version,
                $incomingVersion,
                $diff
            );
        }
    }

    /**
     * Calculate the difference between current and incoming data.
     *
     * @param Post $post
     * @param array $incoming
     * @return array
     */
    private function calculateDiff(Post $post, array $incoming): array
    {
        $diff = [];
        $fieldsToCheck = ['title', 'content'];

        foreach ($fieldsToCheck as $field) {
            if (isset($incoming[$field]) && $post->$field !== $incoming[$field]) {
                $diff[$field] = [
                    'current' => $post->$field,
                    'incoming' => $incoming[$field],
                ];
            }
        }

        return $diff;
    }
}
