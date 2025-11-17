<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmailVerificationController extends Controller
{
    /**
     * Send verification code
     * POST /api/send-verification-code
     */
    public function sendVerificationCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email address',
                'errors' => $validator->errors(),
            ], 422);
        }

        [$post, $index, $posts] = $this->findPostByEmail($request->email);

        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'No post found with this email',
            ], 404);
        }

        $code = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        $posts[$index]['verification_code'] = $code;
        $posts[$index]['code_expires_at'] = Carbon::now()->addMinutes(15)->toDateTimeString();
        $posts[$index]['email_verified'] = false;
        $posts[$index]['updated_at'] = Carbon::now()->toDateTimeString();

        $this->writePosts($posts);

        return response()->json([
            'success' => true,
            'message' => 'Verification code generated successfully',
            'code' => $code, // Remove this in production!
        ], 200);
    }

    /**
     * Verify email with code
     * POST /api/verify-email
     */
    public function verifyEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'code' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        [$post, $index, $posts] = $this->findPostByEmail($request->email);

        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found',
            ], 404);
        }

        if (($post['verification_code'] ?? null) !== $request->code) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid verification code',
            ], 400);
        }

        $expiresAt = isset($post['code_expires_at']) ? Carbon::parse($post['code_expires_at']) : null;

        if (!$expiresAt || Carbon::now()->greaterThan($expiresAt)) {
            return response()->json([
                'success' => false,
                'message' => 'Verification code has expired',
            ], 400);
        }

        $posts[$index]['email_verified'] = true;
        $posts[$index]['verification_code'] = null;
        $posts[$index]['code_expires_at'] = null;
        $posts[$index]['updated_at'] = Carbon::now()->toDateTimeString();

        $this->writePosts($posts);

        return response()->json([
            'success' => true,
            'message' => 'Email verified successfully',
            'data' => $posts[$index],
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
        if (!file_exists($this->storagePath())) {
            return [];
        }

        $decoded = json_decode(file_get_contents($this->storagePath()), true);

        return is_array($decoded) ? $decoded : [];
    }

    private function writePosts(array $posts): void
    {
        file_put_contents($this->storagePath(), json_encode(array_values($posts), JSON_PRETTY_PRINT));
    }

    private function findPostByEmail(string $email): array
    {
        $posts = $this->readPosts();
        $matchedIndex = null;

        // Prefer the latest unverified post when multiple entries share an email
        foreach (array_reverse($posts, true) as $index => $post) {
            if (($post['email'] ?? null) === $email && empty($post['email_verified'])) {
                $matchedIndex = $index;
                break;
            }
        }

        if ($matchedIndex === null) {
            foreach (array_reverse($posts, true) as $index => $post) {
                if (($post['email'] ?? null) === $email) {
                    $matchedIndex = $index;
                    break;
                }
            }
        }

        if ($matchedIndex === null) {
            return [null, null, $posts];
        }

        return [$posts[$matchedIndex], $matchedIndex, $posts];
    }
}
