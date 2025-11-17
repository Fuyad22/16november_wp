<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * Get all posts
     * GET /api/posts
     */
    public function index()
    {
        $posts = $this->readPosts();

        // Return newest first to mirror the previous database ordering
        $posts = array_reverse(array_values($posts));

        return response()->json([
            'success' => true,
            'data' => $posts,
        ], 200);
    }

    /**
     * Create a new post
     * POST /api/posts
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'author' => 'required|string|max:100',
            'email' => 'required|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $posts = $this->readPosts();
        $now = Carbon::now()->toDateTimeString();
        $newPost = [
            '_id' => $this->generateId($posts),
            'title' => $request->title,
            'content' => $request->content,
            'author' => $request->author,
            'email' => $request->email,
            'email_verified' => false,
            'verification_code' => null,
            'code_expires_at' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        $posts[] = $newPost;
        $this->writePosts($posts);

        return response()->json([
            'success' => true,
            'message' => 'Post created successfully',
            'data' => $newPost,
        ], 201);
    }

    /**
     * Get a single post
     * GET /api/posts/{id}
     */
    public function show(string $id)
    {
        $post = $this->findPost($id);

        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $post,
        ], 200);
    }

    /**
     * Update a post
     * PUT /api/posts/{id}
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'author' => 'sometimes|required|string|max:100',
            'email' => 'sometimes|required|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        [$post, $index, $posts] = $this->findPostWithIndex($id);

        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found',
            ], 404);
        }

        if ($request->has('title')) {
            $posts[$index]['title'] = $request->title;
        }

        if ($request->has('content')) {
            $posts[$index]['content'] = $request->content;
        }

        if ($request->has('author')) {
            $posts[$index]['author'] = $request->author;
        }

        if ($request->has('email')) {
            if ($request->email !== $posts[$index]['email']) {
                $posts[$index]['email_verified'] = false;
            }
            $posts[$index]['email'] = $request->email;
        }

        if ($request->has('email_verified')) {
            $posts[$index]['email_verified'] = (bool) $request->email_verified;
        }

        $posts[$index]['updated_at'] = Carbon::now()->toDateTimeString();

        $this->writePosts($posts);

        return response()->json([
            'success' => true,
            'message' => 'Post updated successfully',
            'data' => $posts[$index],
        ], 200);
    }

    /**
     * Delete a post
     * DELETE /api/posts/{id}
     */
    public function destroy(string $id)
    {
        [$post, $index, $posts] = $this->findPostWithIndex($id);

        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found',
            ], 404);
        }

        array_splice($posts, $index, 1);
        $this->writePosts($posts);

        return response()->json([
            'success' => true,
            'message' => 'Post deleted successfully',
        ], 200);
    }

    /**
     * Helpers
     */
    private function storagePath(): string
    {
        return storage_path('posts.json');
    }

    private function readPosts(): array
    {
        $path = $this->storagePath();
        if (!file_exists($path)) {
            return [];
        }

        $decoded = json_decode(file_get_contents($path), true);

        return is_array($decoded) ? $decoded : [];
    }

    private function writePosts(array $posts): void
    {
        file_put_contents($this->storagePath(), json_encode(array_values($posts), JSON_PRETTY_PRINT));
    }

    private function findPost(string $id): ?array
    {
        foreach ($this->readPosts() as $post) {
            if ((string) $post['_id'] === (string) $id) {
                return $post;
            }
        }

        return null;
    }

    private function findPostWithIndex(string $id): array
    {
        $posts = $this->readPosts();

        foreach ($posts as $index => $post) {
            if ((string) $post['_id'] === (string) $id) {
                return [$post, $index, $posts];
            }
        }

        return [null, null, $posts];
    }

    private function generateId(array $posts): string
    {
        if (empty($posts)) {
            return '1';
        }

        $max = max(array_map(static fn ($post) => (int) $post['_id'], $posts));

        return (string) ($max + 1);
    }
}
