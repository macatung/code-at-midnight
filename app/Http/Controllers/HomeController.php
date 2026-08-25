<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Skill;
use App\Models\Experience;
use App\Models\SiteSetting;
use App\Models\PageView;
use App\Models\AnalyticsEvent;
use App\Models\ContactSubmission;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    /**
     * Display the Task Companion product introduction.
     */
    public function index(): Response
    {
        $settings = SiteSetting::all()->pluck('value', 'key')->toArray();

        return Inertia::render('Home', [
            'title' => $settings['site_title'] ?? 'MacaTung — Building AI Agents & Business Systems',
            'settings' => $settings,
        ]);
    }

    /**
     * Dedicated Projects Grimoire page.
     */
    public function projects(): Response
    {
        $projects = Project::ordered()->get();
        $settings = SiteSetting::all()->pluck('value', 'key')->toArray();

        return Inertia::render('Projects/Index', [
            'projects' => $projects,
            'settings' => $settings,
        ]);
    }

    /**
     * Desktop Task Companion product and download page.
     */
    public function desktop(): Response
    {
        return Inertia::render('Desktop/Index');
    }

    /**
     * Dedicated Skills & Profile Lore page.
     */
    public function about(): Response
    {
        $skills = Skill::ordered()->get();
        $experiences = Experience::ordered()->get();
        $settings = SiteSetting::all()->pluck('value', 'key')->toArray();

        $stats = [
            'total_pageviews' => PageView::count(),
            'unique_visitors' => PageView::distinct('session_id')->count('session_id'),
            'total_inquiries' => ContactSubmission::count(),
            'total_hops' => AnalyticsEvent::where('event_type', 'hop_mascot')->count(),
            'total_projects' => Project::count(),
        ];

        return Inertia::render('About/Index', [
            'skills' => $skills,
            'experiences' => $experiences,
            'stats' => $stats,
            'settings' => $settings,
        ]);
    }

    /**
     * Dedicated Contact & Summoning Altar page.
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
}
