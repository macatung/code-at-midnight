<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ContentSyncController extends Controller
{
    /**
     * Synchronize video media artifacts, scripts, and SEO metadata from n8n / GCE worker.
     *
     * POST /api/v1/content-sync
     */
    public function sync(Request $request): JsonResponse
    {
        // 1. Authenticate via Bearer Token or X-Content-Sync-Token header
        $configuredSecret = config('services.cms.sync_secret')
            ?? env('CMS_CONTENT_SYNC_SECRET', 'macatung_cms_sync_token_secret_2026');

        $bearerToken = $request->bearerToken();
        $headerToken = $request->header('X-Content-Sync-Token');
        $providedToken = $bearerToken ?? $headerToken;

        if (empty($providedToken) || !hash_equals((string)$configuredSecret, (string)$providedToken)) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized',
                'message' => 'Invalid or missing Bearer token authentication secret.',
            ], 401);
        }

        // 2. Validate incoming payload structure
        $validator = Validator::make($request->all(), [
            'job_id' => 'nullable|string|max:128',
            'article_id' => 'nullable|integer',
            'slug' => 'required_without:article_id|nullable|string|max:255',
            'status' => 'required|string|in:draft,processing,completed,published,failed',
            'error_message' => 'nullable|string',
            'media' => 'nullable|array',
            'scripts' => 'nullable|array',
            'seo_metadata' => 'nullable|array',
            'timings' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'messages' => $validator->errors(),
            ], 422);
        }

        // 3. Locate the target article by article_id or slug
        $article = null;
        if ($request->filled('article_id')) {
            $article = Article::find($request->input('article_id'));
        }

        if (!$article && $request->filled('slug')) {
            $article = Article::where('slug', $request->input('slug'))->first();
        }

        if (!$article) {
            return response()->json([
                'success' => false,
                'error' => 'Article not found',
                'message' => 'No article found matching the specified article_id or slug.',
            ], 404);
        }

        // 4. Map payload to Article fields and persist in atomic transaction
        $status = $request->input('status', 'completed');
        $media = $request->input('media', []);
        $scripts = $request->input('scripts', []);
        $seo = $request->input('seo_metadata', []);
        $timings = $request->input('timings', []);

        $updateData = [
            'video_status' => $status,
        ];

        if ($request->filled('job_id')) {
            $updateData['pipeline_task_id'] = $request->input('job_id');
        }

        if ($request->filled('error_message')) {
            $updateData['pipeline_error'] = $request->input('error_message');
        }

        // Media fields
        if (!empty($media['video_16x9']['cdn_url'])) {
            $updateData['video_long_url'] = $media['video_16x9']['cdn_url'];
        } elseif ($request->filled('video_long_url')) {
            $updateData['video_long_url'] = $request->input('video_long_url');
        }

        if (!empty($media['video_9x16']['cdn_url'])) {
            $updateData['video_short_url'] = $media['video_9x16']['cdn_url'];
        } elseif ($request->filled('video_short_url')) {
            $updateData['video_short_url'] = $request->input('video_short_url');
        }

        if (isset($media['video_16x9']['duration_seconds'])) {
            $updateData['video_long_duration'] = (string)$media['video_16x9']['duration_seconds'];
        } elseif ($request->filled('video_long_duration')) {
            $updateData['video_long_duration'] = (string)$request->input('video_long_duration');
        }

        if (isset($media['video_9x16']['duration_seconds'])) {
            $updateData['video_short_duration'] = (string)$media['video_9x16']['duration_seconds'];
        } elseif ($request->filled('video_short_duration')) {
            $updateData['video_short_duration'] = (string)$request->input('video_short_duration');
        }

        if (!empty($media['thumbnails']['thumb_16x9_url'])) {
            $updateData['thumbnail_long_url'] = $media['thumbnails']['thumb_16x9_url'];
        } elseif ($request->filled('thumbnail_long_url')) {
            $updateData['thumbnail_long_url'] = $request->input('thumbnail_long_url');
        }

        if (!empty($media['thumbnails']['thumb_9x16_url'])) {
            $updateData['thumbnail_short_url'] = $media['thumbnails']['thumb_9x16_url'];
        } elseif ($request->filled('thumbnail_short_url')) {
            $updateData['thumbnail_short_url'] = $request->input('thumbnail_short_url');
        }

        // Scripts fields
        if (!empty($scripts['longform']['content_markdown'])) {
            $updateData['script_long'] = $scripts['longform']['content_markdown'];
        } elseif ($request->filled('script_long')) {
            $updateData['script_long'] = $request->input('script_long');
        }

        if (!empty($scripts['reel_short']['content_text'])) {
            $updateData['script_short'] = $scripts['reel_short']['content_text'];
        } elseif ($request->filled('script_short')) {
            $updateData['script_short'] = $request->input('script_short');
        }

        // SEO metadata
        if (!empty($seo['brand_title']) || !empty($seo['youtube_title'])) {
            $updateData['seo_title'] = $seo['brand_title'] ?? $seo['youtube_title'];
        } elseif ($request->filled('seo_title')) {
            $updateData['seo_title'] = $request->input('seo_title');
        }

        if (!empty($seo['youtube_description'])) {
            $updateData['seo_description'] = $seo['youtube_description'];
        } elseif ($request->filled('seo_description')) {
            $updateData['seo_description'] = $request->input('seo_description');
        }

        if (!empty($seo['captions'])) {
            if (is_array($seo['captions'])) {
                $caption = $seo['captions']['facebook']
                    ?? $seo['captions']['tiktok']
                    ?? $seo['captions']['reels']
                    ?? reset($seo['captions']);
                $updateData['social_caption'] = (string)$caption;
            } else {
                $updateData['social_caption'] = (string)$seo['captions'];
            }
        } elseif ($request->filled('social_caption')) {
            $updateData['social_caption'] = $request->input('social_caption');
        }

        if (!empty($seo['hashtags']) && is_array($seo['hashtags'])) {
            $updateData['hashtags'] = $seo['hashtags'];
        } elseif ($request->filled('hashtags')) {
            $rawTags = $request->input('hashtags');
            $updateData['hashtags'] = is_array($rawTags) ? $rawTags : json_decode((string)$rawTags, true);
        }

        // Timing fields
        if (!empty($timings['triggered_at'])) {
            try {
                $updateData['pipeline_started_at'] = Carbon::parse($timings['triggered_at']);
            } catch (\Throwable) {
                // Ignore parse errors
            }
        }

        if (!empty($timings['completed_at'])) {
            try {
                $updateData['pipeline_completed_at'] = Carbon::parse($timings['completed_at']);
            } catch (\Throwable) {
                // Ignore parse errors
            }
        } elseif ($status === 'completed' && empty($article->pipeline_completed_at)) {
            $updateData['pipeline_completed_at'] = now();
        }

        DB::transaction(function () use ($article, $updateData) {
            $article->update($updateData);
        });

        return response()->json([
            'success' => true,
            'message' => 'Content synchronized successfully',
            'article_id' => $article->id,
            'slug' => $article->slug,
            'video_status' => $article->video_status,
            'updated_at' => $article->updated_at?->toISOString() ?? now()->toISOString(),
        ], 200);
    }
}
