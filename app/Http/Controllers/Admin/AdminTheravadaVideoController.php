<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleShort;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use Inertia\Response;

class AdminTheravadaVideoController extends Controller
{
    /**
     * Display a listing of Theravāda video productions and articles.
     *
     * GET /admin/theravada/videos
     */
    public function index(Request $request): Response
    {
        $status = $request->query('status', 'all');
        $playlist = $request->query('playlist', 'all');
        $search = trim((string) $request->query('search', ''));

        $query = Article::query()->where('site_domain', 'theravada');

        if ($status !== 'all' && in_array($status, ['draft', 'processing', 'completed', 'published', 'failed'], true)) {
            $query->where('video_status', $status);
        }

        if ($playlist !== 'all') {
            if ($playlist === 'unassigned') {
                $query->whereNull('playlist');
            } else {
                $query->where('playlist', $playlist);
            }
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('pali_title', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        // Playlist-specific ordering vs Global ordering
        if ($playlist !== 'all' && $playlist !== 'unassigned') {
            $articles = $query
                ->orderByRaw("CASE WHEN episode_number IS NOT NULL THEN 0 ELSE 1 END ASC")
                ->orderBy('episode_number', 'asc')
                ->orderBy('id', 'asc')
                ->paginate(12)
                ->withQueryString();
        } else {
            $articles = $query
                ->orderByRaw("CASE WHEN video_long_url IS NOT NULL OR video_status IN ('completed', 'published', 'processing') THEN 0 ELSE 1 END ASC")
                ->orderBy('id', 'desc')
                ->paginate(12)
                ->withQueryString();
        }

        // Compute tab counts
        $baseTheravada = Article::where('site_domain', 'theravada');
        $statusCounts = [
            'all' => (clone $baseTheravada)->count(),
            'draft' => (clone $baseTheravada)->where('video_status', 'draft')->count(),
            'processing' => (clone $baseTheravada)->where('video_status', 'processing')->count(),
            'completed' => (clone $baseTheravada)->where('video_status', 'completed')->count(),
            'published' => (clone $baseTheravada)->where('video_status', 'published')->count(),
        ];

        $playlistTabs = [
            ['key' => 'all', 'label' => 'Tất cả Playlist', 'count' => (clone $baseTheravada)->count()],
            ['key' => 'phat-phap-ung-dung', 'label' => 'Phật Pháp Ứng Dụng', 'count' => (clone $baseTheravada)->where('playlist', 'phat-phap-ung-dung')->count()],
            ['key' => 'tam-an-van-su-an', 'label' => 'Tâm An Vạn Sự An', 'count' => (clone $baseTheravada)->where('playlist', 'tam-an-van-su-an')->count()],
            ['key' => 'unassigned', 'label' => 'Chưa phân loại', 'count' => (clone $baseTheravada)->whereNull('playlist')->count()],
        ];

        $playlistsMetadata = [
            'phat-phap-ung-dung' => [
                'slug' => 'phat-phap-ung-dung',
                'title' => 'Phật Pháp Ứng Dụng',
                'subtitle' => 'Khoa Học Thần Kinh & Đời Thực Cho Người Trẻ',
                'description' => 'Giải mã các hiện tượng tâm lý, áp lực cuộc sống, nghiện dopamine và căn bệnh trì hoãn qua lăng kính khoa học kết hợp cốt tủy Phật giáo nguyên thủy với giọng văn dí dỏm, gần gũi.',
                'icon' => 'Brain',
                'total_episodes' => (clone $baseTheravada)->where('playlist', 'phat-phap-ung-dung')->count(),
                'completed_episodes' => (clone $baseTheravada)->where('playlist', 'phat-phap-ung-dung')->whereIn('video_status', ['completed', 'published'])->count(),
                'published_episodes' => (clone $baseTheravada)->where('playlist', 'phat-phap-ung-dung')->where('video_status', 'published')->count(),
            ],
            'tam-an-van-su-an' => [
                'slug' => 'tam-an-van-su-an',
                'title' => 'Tâm An Vạn Sự An',
                'subtitle' => 'Pháp Âm Tỉnh Thức & Hành Trình Chữa Lành Vô Ngã',
                'description' => 'Chuỗi pháp thoại thiền quán, giải tỏa lo âu, tháo ngòi nổ bản ngã và nghệ thuật sống an nhiên tự tại giữa tám ngọn gió đời.',
                'icon' => 'Sparkles',
                'total_episodes' => (clone $baseTheravada)->where('playlist', 'tam-an-van-su-an')->count(),
                'completed_episodes' => (clone $baseTheravada)->where('playlist', 'tam-an-van-su-an')->whereIn('video_status', ['completed', 'published'])->count(),
                'published_episodes' => (clone $baseTheravada)->where('playlist', 'tam-an-van-su-an')->where('video_status', 'published')->count(),
            ],
        ];

        return Inertia::render('Admin/Theravada/Videos/Index', [
            'articles' => $articles,
            'filters' => [
                'status' => $status,
                'playlist' => $playlist,
                'search' => $search,
            ],
            'statusCounts' => $statusCounts,
            'playlistTabs' => $playlistTabs,
            'playlistsMetadata' => $playlistsMetadata,
        ]);
    }

    /**
     * Display video details, players, thumbnails, scripts, and 1-click copy suite.
     *
     * GET /admin/theravada/videos/{article}
     */
    public function show(Article $article): Response
    {
        $prevEpisode = null;
        $nextEpisode = null;

        if ($article->playlist) {
            if ($article->episode_number !== null) {
                $prevEpisode = Article::where('playlist', $article->playlist)
                    ->where('episode_number', '<', $article->episode_number)
                    ->orderBy('episode_number', 'desc')
                    ->first(['id', 'title', 'slug', 'episode_number', 'video_status', 'thumbnail_long_url']);

                $nextEpisode = Article::where('playlist', $article->playlist)
                    ->where('episode_number', '>', $article->episode_number)
                    ->orderBy('episode_number', 'asc')
                    ->first(['id', 'title', 'slug', 'episode_number', 'video_status', 'thumbnail_long_url']);
            } else {
                $prevEpisode = Article::where('playlist', $article->playlist)
                    ->where('id', '<', $article->id)
                    ->orderBy('id', 'desc')
                    ->first(['id', 'title', 'slug', 'episode_number', 'video_status', 'thumbnail_long_url']);

                $nextEpisode = Article::where('playlist', $article->playlist)
                    ->where('id', '>', $article->id)
                    ->orderBy('id', 'asc')
                    ->first(['id', 'title', 'slug', 'episode_number', 'video_status', 'thumbnail_long_url']);
            }
        }

        $article->load('shorts');

        return Inertia::render('Admin/Theravada/Videos/Show', [
            'article' => $article,
            'prevEpisode' => $prevEpisode,
            'nextEpisode' => $nextEpisode,
        ]);

    }

    /**
     * Trigger n8n on-demand video generation pipeline for an article.
     *
     * POST /admin/theravada/videos/{article}/trigger
     */
    public function triggerPipeline(Article $article, Request $request): RedirectResponse
    {
        $article->update([
            'video_status' => 'processing',
            'pipeline_started_at' => now(),
            'pipeline_error' => null,
        ]);

        $webhookUrl = config('services.n8n.video_webhook_url')
            ?? env('N8N_VIDEO_WEBHOOK_URL', 'https://workflow.macatung.dev/webhook/theravada-video-generator');
        $webhookSecret = config('services.n8n.webhook_secret')
            ?? env('N8N_WEBHOOK_SECRET', 'nendoi_video_pipeline_secret_2026');

        $jobId = 'job_' . now()->format('Ymd_His') . '_' . $article->id;

        $payload = [
            'source' => 'cms_admin',
            'article_id' => $article->id,
            'slug' => $article->slug,
            'title' => $article->title,
            'article_url' => url('/phap-thoai/' . $article->slug),
            'category' => $article->category ?? 'phap-thoai',
            'raw_content' => $article->content,
            'options' => [
                'voice_code' => $request->input('voice_code', 'hn_male_phuthang_stor_24k-stl'),
                'speed_rate' => (float) $request->input('speed_rate', 0.75),
                'bgm_preset' => $request->input('bgm_preset', 'meditation_432hz'),
                'ducking_db' => -22.0,
                'generate_formats' => ['16:9', '9:16'],
                'callback_url' => route('api.v1.content-sync'),
                'mode' => env('APP_ENV') === 'production' ? 'production' : 'demo',
            ],
        ];

        try {
            $response = Http::timeout(4)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $webhookSecret,
                    'X-Request-Source' => 'cms_admin',
                ])
                ->post($webhookUrl, $payload);

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data['job_id'])) {
                    $jobId = $data['job_id'];
                }
            }
        } catch (\Throwable $e) {
            // Graceful handling for offline n8n / local test environments
        }

        $article->update([
            'pipeline_task_id' => $jobId,
        ]);

        return redirect()->back()->with('success', "Đã kích hoạt tiến trình tạo Video thành công! Mã tác vụ: {$jobId}");
    }

    /**
     * Toggle video publishing status between published and completed.
     *
     * PATCH /admin/theravada/videos/{article}/publish
     */
    public function publish(Article $article): RedirectResponse
    {
        $newStatus = ($article->video_status === 'published') ? 'completed' : 'published';

        $article->update([
            'video_status' => $newStatus,
            'is_published' => true,
        ]);

        $message = ($newStatus === 'published')
            ? 'Video đã được xuất bản công khai!'
            : 'Video đã được gỡ về trạng thái hoàn thành (chưa xuất bản).';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Update video metadata, YouTube link, status, and SEO content.
     *
     * PUT /admin/theravada/videos/{article}
     */
    public function update(Article $article, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'youtube_url' => ['nullable', 'string', 'max:500'],
            'playlist' => ['nullable', 'string', 'max:100'],
            'episode_number' => ['nullable', 'integer', 'min:1'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string'],
            'social_caption' => ['nullable', 'string'],
            'hashtags' => ['nullable'],
            'video_status' => ['nullable', 'string', 'in:draft,processing,completed,published,failed'],
        ]);

        if (isset($validated['hashtags']) && is_string($validated['hashtags'])) {
            $raw = trim($validated['hashtags']);
            if (str_contains($raw, ',')) {
                $validated['hashtags'] = array_values(array_filter(array_map('trim', explode(',', $raw))));
            } elseif (str_contains($raw, '#')) {
                preg_match_all('/#?([a-zA-Z0-9_\x{00C0}-\x{024F}\x{1E00}-\x{1EFF}]+)/u', $raw, $matches);
                $validated['hashtags'] = $matches[1] ?? [];
            } else {
                $validated['hashtags'] = array_values(array_filter(explode(' ', $raw)));
            }
        }

        $article->update($validated);

        return redirect()->back()->with('success', 'Đã cập nhật thông tin video và metadata thành công!');
    }

    /**
     * Create or update an ArticleShort item in the Multi-Shorts ecosystem.
     *
     * POST /admin/theravada/videos/{article}/shorts
     */
    public function saveShort(Article $article, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id' => ['nullable', 'integer'],
            'title' => ['required', 'string', 'max:255'],
            'focus_hook' => ['nullable', 'string', 'max:255'],
            'visual_style' => ['nullable', 'string', 'max:100'],
            'video_url' => ['required', 'string', 'max:500'],
            'thumbnail_url' => ['nullable', 'string', 'max:500'],
            'duration' => ['nullable', 'string', 'max:50'],
            'script' => ['nullable', 'string'],
            'display_text' => ['nullable', 'string'],
            'spoken_text' => ['nullable', 'string'],
            'youtube_shorts_url' => ['nullable', 'string', 'max:500'],
            'tiktok_url' => ['nullable', 'string', 'max:500'],
            'reels_url' => ['nullable', 'string', 'max:500'],
            'order_index' => ['nullable', 'integer'],
            'status' => ['nullable', 'string', 'max:50'],
        ]);

        if (!empty($validated['id'])) {
            $short = $article->shorts()->findOrFail($validated['id']);
            $short->update($validated);
        } else {
            $validated['order_index'] = $validated['order_index'] ?? $article->shorts()->count();
            $short = $article->shorts()->create($validated);
        }

        // Backward compatibility: If this is the primary short (order_index == 0), sync to article
        if (($validated['order_index'] ?? 0) === 0) {
            $article->update([
                'video_short_url' => $validated['video_url'],
                'thumbnail_short_url' => $validated['thumbnail_url'] ?? $article->thumbnail_short_url,
                'video_short_duration' => $validated['duration'] ?? $article->video_short_duration,
            ]);
        }

        return redirect()->back()->with('success', 'Đã lưu thông tin Video Short thành công!');
    }

    /**
     * Delete an ArticleShort item.
     *
     * DELETE /admin/theravada/videos/{article}/shorts/{short}
     */
    public function deleteShort(Article $article, ArticleShort $short): RedirectResponse
    {
        if ($short->article_id !== $article->id) {
            abort(403);
        }

        $short->delete();

        // If another short remains, sync the first one back to article
        $firstShort = $article->shorts()->orderBy('order_index')->first();
        if ($firstShort) {
            $article->update([
                'video_short_url' => $firstShort->video_url,
                'thumbnail_short_url' => $firstShort->thumbnail_url,
                'video_short_duration' => $firstShort->duration,
            ]);
        }

        return redirect()->back()->with('success', 'Đã xóa Video Short thành công!');
    }
}

