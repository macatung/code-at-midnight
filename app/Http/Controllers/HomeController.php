<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use App\Models\PageView;
use App\Models\AnalyticsEvent;
use App\Models\ContactSubmission;
use App\Models\Article;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class HomeController extends Controller
{
    /**
     * Display the Midnight Tech & Fun Hub home page.
     */
    public function index(): Response
    {
        $latestArticles = Article::where('is_published', true)->orderBy('published_at', 'desc')->limit(4)->get();
        $settings = SiteSetting::all()->pluck('value', 'key')->toArray();

        // Real computed stats from database
        $totalPageviews = PageView::count();
        $totalUniqueVisitors = PageView::distinct('session_id')->count('session_id');
        $totalInquiries = ContactSubmission::count();
        $totalHops = AnalyticsEvent::where('event_type', 'hop_mascot')->count();

        $stats = [
            'total_pageviews' => $totalPageviews,
            'unique_visitors' => $totalUniqueVisitors,
            'total_inquiries' => $totalInquiries,
            'total_hops' => $totalHops,
        ];

        return Inertia::render('Home', [
            'title' => $settings['site_title'] ?? 'Code at midnight — Midnight Tech & Fun Sanctuary',
            'latestArticles' => $latestArticles,
            'settings' => $settings,
            'stats' => $stats,
        ]);
    }

    /**
     * Redirect legacy projects url to Knowledge Blog.
     */
    public function projects(): RedirectResponse
    {
        return redirect()->route('blog.index');
    }

    /**
     * Redirect legacy about/skills url to Knowledge Blog.
     */
    public function about(): RedirectResponse
    {
        return redirect()->route('blog.index');
    }

    /**
     * Dedicated Contact & Discussion Altar page.
     */
    public function contact(): Response
    {
        $settings = SiteSetting::all()->pluck('value', 'key')->toArray();

        return Inertia::render('Contact/Index', [
            'settings' => $settings,
        ]);
    }

    /**
     * Dedicated Rune Typer Dev Game Arcade page.
     */
    public function game(): Response
    {
        $settings = SiteSetting::all()->pluck('value', 'key')->toArray();

        return Inertia::render('Game/Index', [
            'settings' => $settings,
        ]);
    }

    /**
     * Dedicated Talisman Forge tool page.
     */
    public function talisman(): Response
    {
        $settings = SiteSetting::all()->pluck('value', 'key')->toArray();

        return Inertia::render('Talisman/Index', [
            'settings' => $settings,
        ]);
    }

    /**
     * Dedicated Yin-Yang Fortune Oracle page.
     */
    public function oracle(): Response
    {
        $settings = SiteSetting::all()->pluck('value', 'key')->toArray();

        return Inertia::render('Oracle/Index', [
            'settings' => $settings,
        ]);
    }
}
