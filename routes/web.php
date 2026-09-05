<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Theravada\TheravadaController;
use App\Http\Controllers\Api\AnalyticsEventController;
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

// 1. Theravāda Subdomain Routes (e.g. theravada.macatung.dev / theravada.localhost)
Route::domain('theravada.' . $baseDomain)->group(function () {
    Route::get('/', [TheravadaController::class, 'index'])->name('theravada.domain.index');
    Route::get('/kinh/{slug}', [TheravadaController::class, 'show'])->name('theravada.domain.show');
    Route::get('/bai-viet/{slug}', [TheravadaController::class, 'show']);
    Route::get('/danh-muc/{category}', [TheravadaController::class, 'category'])->name('theravada.domain.category');
    Route::get('/tu-dien-pali', [TheravadaController::class, 'glossary'])->name('theravada.domain.glossary');
    Route::get('/hoc-pali', [TheravadaController::class, 'paliLearning'])->name('theravada.domain.pali-learning');
    Route::get('/hoc-tieng-pali', [TheravadaController::class, 'paliLearning']);
    Route::get('/pali-learning', [TheravadaController::class, 'paliLearning']);
    Route::get('/hoc-pali/{slug}', [TheravadaController::class, 'paliLessonShow'])->name('theravada.domain.pali-lesson.show');
    Route::get('/hoc-tieng-pali/{slug}', [TheravadaController::class, 'paliLessonShow']);
    Route::get('/pali-learning/{slug}', [TheravadaController::class, 'paliLessonShow']);
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
    Route::get('/hoc-pali', [TheravadaController::class, 'paliLearning'])->name('pali-learning');
    Route::get('/hoc-tieng-pali', [TheravadaController::class, 'paliLearning']);
    Route::get('/pali-learning', [TheravadaController::class, 'paliLearning']);
    Route::get('/hoc-pali/{slug}', [TheravadaController::class, 'paliLessonShow'])->name('pali-lesson.show');
    Route::get('/hoc-tieng-pali/{slug}', [TheravadaController::class, 'paliLessonShow']);
    Route::get('/pali-learning/{slug}', [TheravadaController::class, 'paliLessonShow']);
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
