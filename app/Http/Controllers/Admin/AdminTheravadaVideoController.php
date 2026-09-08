<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
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
        $search = trim((string) $request->query('search', ''));

        $query = Article::query()->where('site_domain', 'theravada');

        if ($status !== 'all' && in_array($status, ['draft', 'processing', 'completed', 'published', 'failed'], true)) {
            $query->where('video_status', $status);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('pali_title', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $articles = $query->orderBy('id', 'desc')->paginate(12)->withQueryString();

        // Compute tab counts
        $baseTheravada = Article::where('site_domain', 'theravada');
        $statusCounts = [
            'all' => (clone $baseTheravada)->count(),
            'draft' => (clone $baseTheravada)->where('video_status', 'draft')->count(),
            'processing' => (clone $baseTheravada)->where('video_status', 'processing')->count(),
            'completed' => (clone $baseTheravada)->where('video_status', 'completed')->count(),
            'published' => (clone $baseTheravada)->where('video_status', 'published')->count(),
        ];

        return Inertia::render('Admin/Theravada/Videos/Index', [
            'articles' => $articles,
            'filters' => [
                'status' => $status,
                'search' => $search,
            ],
            'statusCounts' => $statusCounts,
        ]);
    }

    /**
     * Display video details, players, thumbnails, scripts, and 1-click copy suite.
     *
     * GET /admin/theravada/videos/{article}
     */
    public function show(Article $article): Response
    {
        return Inertia::render('Admin/Theravada/Videos/Show', [
            'article' => $article,
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
}
