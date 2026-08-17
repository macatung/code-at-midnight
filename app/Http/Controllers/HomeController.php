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
     * Display the portfolio home page with live database records and real analytics stats.
     */
    public function index(): Response
    {
        $projects = Project::ordered()->get();
        $skills = Skill::ordered()->get();
        $experiences = Experience::ordered()->get();
        $settings = SiteSetting::all()->pluck('value', 'key')->toArray();

        // Real computed stats from database
        $totalPageviews = PageView::count();
        $totalUniqueVisitors = PageView::distinct('session_id')->count('session_id');
        $totalInquiries = ContactSubmission::count();
        $totalHops = AnalyticsEvent::where('event_type', 'hop_mascot')->count();
        $totalProjects = Project::count();

        $stats = [
            'total_pageviews' => $totalPageviews,
            'unique_visitors' => $totalUniqueVisitors,
            'total_inquiries' => $totalInquiries,
            'total_hops' => $totalHops,
            'total_projects' => $totalProjects,
        ];

        return Inertia::render('Home', [
            'title' => $settings['site_title'] ?? 'Code at midnight',
            'projects' => $projects,
            'skills' => $skills,
            'experiences' => $experiences,
            'settings' => $settings,
            'stats' => $stats,
        ]);
    }
}
