<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Theravada\TheravadaController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\Api\ApiTaskController;
use App\Http\Controllers\Api\ApiProjectController;
use App\Http\Controllers\Api\ApiSprintController;
use App\Http\Controllers\Api\AnalyticsEventController;
use App\Http\Controllers\Api\ApiAgentRunController;
use App\Http\Controllers\Api\TaskHubMcpController;
use App\Http\Controllers\Api\ApiProjectDocumentController;
use App\Http\Controllers\Api\ApiProjectReleaseController;
use App\Http\Controllers\GithubAuthController;
use App\Http\Controllers\DesktopPairingController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminAnalyticsController;
use App\Http\Controllers\Admin\AdminProjectController;
use App\Http\Controllers\Admin\AdminSkillController;
use App\Http\Controllers\Admin\AdminExperienceController;
use App\Http\Controllers\Admin\AdminArticleController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\AdminContactController;
use App\Http\Controllers\SeoController;

$baseDomain = config('app.base_domain', 'macatung.dev');

// 1. Tasks Productivity Subdomain Routes (tasks.macatung.dev)
Route::domain('tasks.' . $baseDomain)->group(function () {
    Route::get('/', [TaskController::class, 'index'])->name('tasks.domain.index');
    Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('tasks.domain.sitemap');
    Route::get('/robots.txt', [SeoController::class, 'robots'])->name('tasks.domain.robots');
});

// 2. Tasks Path-based Route (Available on main domain /tasks & local testing)
Route::prefix('tasks')->name('tasks.')->group(function () {
    Route::get('/', [TaskController::class, 'index'])->name('index');
});

// 3. Tasks REST API Endpoints (For Web UI & Desktop Mascot Sync)
Route::prefix('api/tasks')->group(function () {
    Route::get('/', [ApiTaskController::class, 'index']);
    Route::post('/', [ApiTaskController::class, 'store']);
    Route::post('/ai-preview', [ApiTaskController::class, 'aiPreview']);
    Route::post('/ai-generate', [ApiTaskController::class, 'aiGenerate']);
    Route::get('/report-settings', [ApiTaskController::class, 'getReportSettings']);
    Route::post('/report-settings', [ApiTaskController::class, 'saveReportSettings']);
    Route::post('/send-report-now', [ApiTaskController::class, 'sendReportNow']);
    Route::get('/daily-dispatch', [ApiTaskController::class, 'dailyDispatch']);
    Route::get('/daily-review', [ApiTaskController::class, 'dailyReview']);
    Route::get('/next-action', [ApiTaskController::class, 'nextAction']);
    Route::get('/ai-settings', [ApiTaskController::class, 'getAiSettings']);
    Route::post('/ai-settings', [ApiTaskController::class, 'saveAiSettings']);
    Route::get('/agent-runs', [ApiAgentRunController::class, 'index']);
    Route::post('/agent-runs', [ApiAgentRunController::class, 'store']);
    Route::get('/agent-runs/{agentRun}', [ApiAgentRunController::class, 'show']);
    Route::patch('/agent-runs/{agentRun}', [ApiAgentRunController::class, 'update']);
    Route::post('/agent-runs/{agentRun}/events', [ApiAgentRunController::class, 'event']);
    Route::post('/agent-runs/{agentRun}/evidence', [ApiAgentRunController::class, 'evidence']);
    Route::post('/agent-runs/{agentRun}/handoff', [ApiAgentRunController::class, 'handoff']);
    Route::get('/context-pack', [ApiAgentRunController::class, 'context']);
    Route::post('/work-items/{task}/approve', [ApiAgentRunController::class, 'approve']);
    Route::post('/work-items/{task}/reject', [ApiAgentRunController::class, 'reject']);
    Route::post('/github/webhook', [ApiAgentRunController::class, 'githubWebhook']);
    Route::post('/mcp', [TaskHubMcpController::class, 'handle']);
});

// GitHub OAuth identity and repository authorization
Route::get('/auth/github', [GithubAuthController::class, 'redirect'])->name('auth.github');
Route::get('/auth/github/callback', [GithubAuthController::class, 'callback'])->name('auth.github.callback');
Route::post('/auth/github/logout', [GithubAuthController::class, 'logout'])->name('auth.github.logout');

// Keep dynamic task routes after all named API endpoints.
Route::prefix('api/tasks')->group(function () {
    Route::patch('/{id}', [ApiTaskController::class, 'update']);
    Route::delete('/{id}', [ApiTaskController::class, 'destroy']);
});

// Desktop Agent Workspace device pairing and one-time MCP grant.
Route::post('/api/desktop/pairing/start', [DesktopPairingController::class, 'start']);
Route::get('/api/desktop/pairing/{pairingId}/status', [DesktopPairingController::class, 'status']);
Route::get('/desktop/pairing/{pairingId}/approve', [DesktopPairingController::class, 'approveForm']);
Route::post('/desktop/pairing/{pairingId}/approve', [DesktopPairingController::class, 'approve']);
Route::post('/desktop/pairing/{pairingId}/deny', [DesktopPairingController::class, 'deny']);

// 3.1 Projects REST API Endpoints (For Projects Management)
Route::prefix('api/projects')->group(function () {
    Route::get('/', [ApiProjectController::class, 'index']);
    Route::post('/', [ApiProjectController::class, 'store']);
    Route::get('/github/repositories', [ApiProjectController::class, 'githubRepositories'])->middleware('auth');
    Route::post('/from-github', [ApiProjectController::class, 'storeFromGithub'])->middleware('auth');
    Route::get('/{project}/github', [ApiProjectController::class, 'githubStatus']);
    Route::post('/{project}/github/connect', [ApiProjectController::class, 'connectGithub'])->middleware('auth');
    Route::post('/{project}/github/sync', [ApiProjectController::class, 'syncGithub'])->middleware('auth');
    Route::patch('/{id}', [ApiProjectController::class, 'update']);
    Route::delete('/{id}', [ApiProjectController::class, 'destroy']);
    Route::get('/{project}/documents', [ApiProjectDocumentController::class, 'index']);
    Route::post('/{project}/documents', [ApiProjectDocumentController::class, 'store']);
    Route::post('/{project}/documents/import-manifest', [ApiProjectDocumentController::class, 'importManifest']);
    Route::get('/{project}/releases', [ApiProjectReleaseController::class, 'index']);
    Route::post('/{project}/releases', [ApiProjectReleaseController::class, 'store']);
});
Route::get('/api/project-documents/manifest-template', [ApiProjectDocumentController::class, 'manifestTemplate']);
Route::patch('/api/project-documents/{document}', [ApiProjectDocumentController::class, 'update']);
Route::delete('/api/project-documents/{document}', [ApiProjectDocumentController::class, 'destroy']);
Route::patch('/api/project-releases/{release}', [ApiProjectReleaseController::class, 'update']);
Route::post('/api/tasks/{task}/documents', [ApiProjectDocumentController::class, 'attach']);
Route::delete('/api/tasks/{task}/documents/{document}', [ApiProjectDocumentController::class, 'detach']);

// 3.2 Sprints REST API Endpoints (For Scrum Sprint Management)
Route::prefix('api/sprints')->group(function () {
    Route::get('/', [ApiSprintController::class, 'index']);
    Route::post('/', [ApiSprintController::class, 'store']);
    Route::patch('/{sprint}', [ApiSprintController::class, 'update']);
    Route::delete('/{sprint}', [ApiSprintController::class, 'destroy']);
    Route::post('/{sprint}/start', [ApiSprintController::class, 'start']);
    Route::post('/{sprint}/complete', [ApiSprintController::class, 'complete']);
    Route::post('/move-tasks', [ApiSprintController::class, 'moveTasks']);
});

// 4. Theravāda Subdomain Routes (e.g. theravada.macatung.dev / theravada.localhost)
Route::domain('theravada.' . $baseDomain)->group(function () {
    Route::get('/', [TheravadaController::class, 'index'])->name('theravada.domain.index');
    Route::get('/kinh/{slug}', [TheravadaController::class, 'show'])->name('theravada.domain.show');
    Route::get('/bai-viet/{slug}', [TheravadaController::class, 'show']);
    Route::get('/danh-muc/{category}', [TheravadaController::class, 'category'])->name('theravada.domain.category');
    Route::get('/tu-dien-pali', [TheravadaController::class, 'glossary'])->name('theravada.domain.glossary');
    Route::get('/ung-dung-tu-hoc', [TheravadaController::class, 'apps'])->name('theravada.domain.apps');
    Route::get('/phap-bao', [TheravadaController::class, 'apps']);
    Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('theravada.domain.sitemap');
    Route::get('/robots.txt', [SeoController::class, 'robots'])->name('theravada.domain.robots');
});

// 2. Theravāda Path-based Routes (Available on main domain /theravada/* & local testing)
Route::prefix('theravada')->name('theravada.')->group(function () {
    Route::get('/', [TheravadaController::class, 'index'])->name('index');
    Route::get('/kinh/{slug}', [TheravadaController::class, 'show'])->name('show');
    Route::get('/bai-viet/{slug}', [TheravadaController::class, 'show']);
    Route::get('/danh-muc/{category}', [TheravadaController::class, 'category'])->name('category');
    Route::get('/tu-dien-pali', [TheravadaController::class, 'glossary'])->name('glossary');
    Route::get('/ung-dung-tu-hoc', [TheravadaController::class, 'apps'])->name('apps');
    Route::get('/phap-bao', [TheravadaController::class, 'apps']);
    Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
});

// 3. Global SEO Endpoints
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');

// 4. Public Multi-Page Portfolio Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/projects', [HomeController::class, 'projects'])->name('projects.index');
Route::get('/desktop', [HomeController::class, 'desktop'])->name('desktop.index');
Route::get('/about', [HomeController::class, 'about'])->name('about.index');
Route::get('/skills', [HomeController::class, 'about'])->name('skills.index');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
Route::get('/game', [HomeController::class, 'game'])->name('game.index');
Route::get('/talisman', [HomeController::class, 'talisman'])->name('talisman.index');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact.index');

Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
Route::post('/summon', [ContactController::class, 'store'])->name('contact.summon');

// Client Beacon Interaction Analytics API
Route::post('/api/analytics/event', [AnalyticsEventController::class, 'store'])->name('api.analytics.event');

// Admin Authentication Shield
Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

// Protected Admin CMS Routes
Route::prefix('admin')->name('admin.')->middleware('admin.auth')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [AdminDashboardController::class, 'index']);

    // Traffic & Interaction Analytics
    Route::get('/analytics', [AdminAnalyticsController::class, 'index'])->name('analytics.index');

    // Projects CMS
    Route::get('/projects', [AdminProjectController::class, 'index'])->name('projects.index');
    Route::post('/projects', [AdminProjectController::class, 'store'])->name('projects.store');
    Route::put('/projects/{project}', [AdminProjectController::class, 'update'])->name('projects.update');
    Route::patch('/projects/{project}/toggle-featured', [AdminProjectController::class, 'toggleFeatured'])->name('projects.toggle-featured');
    Route::delete('/projects/{project}', [AdminProjectController::class, 'destroy'])->name('projects.destroy');

    // Skills & Arsenal CMS
    Route::get('/skills', [AdminSkillController::class, 'index'])->name('skills.index');
    Route::post('/skills', [AdminSkillController::class, 'store'])->name('skills.store');
    Route::put('/skills/{skill}', [AdminSkillController::class, 'update'])->name('skills.update');
    Route::delete('/skills/{skill}', [AdminSkillController::class, 'destroy'])->name('skills.destroy');

    // Career Chronicles CMS
    Route::get('/experiences', [AdminExperienceController::class, 'index'])->name('experiences.index');
    Route::post('/experiences', [AdminExperienceController::class, 'store'])->name('experiences.store');
    Route::put('/experiences/{experience}', [AdminExperienceController::class, 'update'])->name('experiences.update');
    Route::delete('/experiences/{experience}', [AdminExperienceController::class, 'destroy'])->name('experiences.destroy');

    // Midnight Tech Notes / Articles CMS
    Route::get('/articles', [AdminArticleController::class, 'index'])->name('articles.index');
    Route::post('/articles', [AdminArticleController::class, 'store'])->name('articles.store');
    Route::put('/articles/{article}', [AdminArticleController::class, 'update'])->name('articles.update');
    Route::delete('/articles/{article}', [AdminArticleController::class, 'destroy'])->name('articles.destroy');

    // Site Settings & Profile CMS
    Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [AdminSettingController::class, 'update'])->name('settings.update');

    // Summoning Inquiries Inbox
    Route::get('/contacts', [AdminContactController::class, 'index'])->name('contacts.index');
    Route::delete('/contacts/{contact}', [AdminContactController::class, 'destroy'])->name('contacts.destroy');
});
