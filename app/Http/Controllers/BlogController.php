<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use App\Models\Article;
use App\Models\SiteSetting;
use Carbon\Carbon;

class BlogController extends Controller
{
    /**
     * Display a listing of published articles.
     */
    public function index(): Response
    {
        $articles = Article::where('is_published', true)->orderBy('published_at', 'desc')->get()->map(function ($article) {
            return [
                'id' => $article->id,
                'title' => $article->title,
                'slug' => $article->slug,
                'excerpt' => $article->excerpt,
                'reading_time_min' => $article->reading_time_min ?? 5,
                'published_at' => $article->published_at ? Carbon::parse($article->published_at)->format('d/m/Y') : '',
            ];
        });

        $settings = SiteSetting::all()->pluck('value', 'key')->toArray();

        return Inertia::render('Blog/Index', [
            'articles' => $articles,
            'settings' => $settings,
        ]);
    }

    /**
     * Display a single article.
     */
    public function show(string $slug): Response
    {
        $article = Article::with('pairedArticle')
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $pairedArticle = null;
        if ($article->pairedArticle && $article->pairedArticle->is_published) {
            $paired = $article->pairedArticle;
            $pairedArticle = [
                'id' => $paired->id,
                'title' => $paired->title,
                'pali_title' => $paired->pali_title,
                'slug' => $paired->slug,
                'excerpt' => $paired->excerpt,
                'site_domain' => $paired->site_domain,
                'reading_time_min' => $paired->reading_time_min ?? 5,
                'url' => '/theravada/kinh/' . $paired->slug,
                'subdomain_url' => 'https://theravada.' . config('app.base_domain', 'macatung.dev') . '/kinh/' . $paired->slug,
            ];
        }

        $settings = SiteSetting::all()->pluck('value', 'key')->toArray();

        return Inertia::render('Blog/Show', [
            'article' => [
                'id' => $article->id,
                'title' => $article->title,
                'slug' => $article->slug,
                'excerpt' => $article->excerpt,
                'content' => $article->content,
                'cover_image' => $article->cover_image ?? null,
                'tags' => $article->tags ?? [],
                'reading_time_min' => $article->reading_time_min ?? 5,
                'published_at' => $article->published_at ? Carbon::parse($article->published_at)->format('d/m/Y') : '',
            ],
            'paired_article' => $pairedArticle,
            'settings' => $settings,
        ]);
    }
}
