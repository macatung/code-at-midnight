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
use App\Http\Controllers\Admin\AdminTheravadaVideoController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\AdminContactController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\Decode\DecodeController;
use App\Http\Controllers\Cashback\CashbackController;
use App\Http\Controllers\Cashback\CashbackAuthController;
use App\Http\Controllers\Admin\AdminCashbackController;

$baseDomain = config('app.base_domain', 'macatung.dev');

// 1. Theravāda Subdomain Routes (e.g. theravada.macatung.dev / theravada.localhost)
Route::domain('theravada.' . $baseDomain)->group(function () {
    Route::get('/', [TheravadaController::class, 'index'])->name('theravada.domain.index');
    Route::get('/kinh/{slug}', [TheravadaController::class, 'show'])->name('theravada.domain.show');
    Route::get('/bai-viet/{slug}', [TheravadaController::class, 'show']);
    Route::get('/phap-thoai/{slug}', [TheravadaController::class, 'show'])->name('theravada.domain.phap-thoai');
    Route::get('/danh-muc/{category}', [TheravadaController::class, 'category'])->name('theravada.domain.category');
    Route::get('/tu-dien-pali', [TheravadaController::class, 'glossary'])->name('theravada.domain.glossary');
    Route::get('/hoc-pali', [TheravadaController::class, 'paliLearning'])->name('theravada.domain.pali-learning');
    Route::get('/hoc-tieng-pali', [TheravadaController::class, 'paliLearning']);
    Route::get('/pali-learning', [TheravadaController::class, 'paliLearning']);
    Route::get('/hoc-pali/{slug}', [TheravadaController::class, 'paliLessonShow'])->name('theravada.domain.pali-lesson.show');
    Route::get('/hoc-tieng-pali/{slug}', [TheravadaController::class, 'paliLessonShow']);
    Route::get('/pali-learning/{slug}', [TheravadaController::class, 'paliLessonShow']);
    Route::get('/ung-dung-tu-hoc', [TheravadaController::class, 'apps'])->name('theravada.domain.apps');
    Route::get('/feed.json', [TheravadaController::class, 'feedJson'])->name('theravada.domain.feed');
    Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('theravada.domain.sitemap');
    Route::get('/robots.txt', [SeoController::class, 'robots'])->name('theravada.domain.robots');
    Route::get('/favicon.ico', function () {
        return response()->file(public_path('brand/theravada/favicon-theravada.ico'), [
            'Content-Type' => 'image/x-icon',
            'Cache-Control' => 'public, max-age=604800, immutable',
        ]);
    })->name('theravada.domain.favicon');

    // Subdomain Admin Auth & Video CMS Routes
    Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('theravada.admin.login');
    Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('theravada.admin.login.submit');
    Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('theravada.admin.logout');

    Route::prefix('admin')->name('theravada.admin.')->middleware('admin.auth')->group(function () {
        Route::get('/videos', [AdminTheravadaVideoController::class, 'index'])->name('videos.index');
        Route::get('/theravada/videos', [AdminTheravadaVideoController::class, 'index'])->name('videos.theravada.index');
        Route::get('/videos/{article}', [AdminTheravadaVideoController::class, 'show'])->name('videos.show');
        Route::get('/theravada/videos/{article}', [AdminTheravadaVideoController::class, 'show'])->name('videos.theravada.show');
        Route::post('/videos/{article}/trigger', [AdminTheravadaVideoController::class, 'triggerPipeline'])->name('videos.trigger');
        Route::post('/theravada/videos/{article}/trigger', [AdminTheravadaVideoController::class, 'triggerPipeline'])->name('videos.theravada.trigger');
        Route::patch('/videos/{article}/publish', [AdminTheravadaVideoController::class, 'publish'])->name('videos.publish');
        Route::patch('/theravada/videos/{article}/publish', [AdminTheravadaVideoController::class, 'publish'])->name('videos.theravada.publish');
        Route::put('/videos/{article}', [AdminTheravadaVideoController::class, 'update'])->name('videos.update');
        Route::put('/theravada/videos/{article}', [AdminTheravadaVideoController::class, 'update'])->name('videos.theravada.update');
    });
});

// 2. Theravāda Path-based Routes (Available on main domain /theravada/* & local testing)
Route::prefix('theravada')->name('theravada.')->group(function () {
    Route::get('/', [TheravadaController::class, 'index'])->name('index');
    Route::get('/kinh/{slug}', [TheravadaController::class, 'show'])->name('show');
    Route::get('/bai-viet/{slug}', [TheravadaController::class, 'show']);
    Route::get('/phap-thoai/{slug}', [TheravadaController::class, 'show'])->name('phap-thoai');
    Route::get('/danh-muc/{category}', [TheravadaController::class, 'category'])->name('category');
    Route::get('/tu-dien-pali', [TheravadaController::class, 'glossary'])->name('glossary');
    Route::get('/hoc-pali', [TheravadaController::class, 'paliLearning'])->name('pali-learning');
    Route::get('/hoc-tieng-pali', [TheravadaController::class, 'paliLearning']);
    Route::get('/pali-learning', [TheravadaController::class, 'paliLearning']);
    Route::get('/hoc-pali/{slug}', [TheravadaController::class, 'paliLessonShow'])->name('pali-lesson.show');
    Route::get('/hoc-tieng-pali/{slug}', [TheravadaController::class, 'paliLessonShow']);
    Route::get('/ung-dung-tu-hoc', [TheravadaController::class, 'apps'])->name('apps');
    Route::get('/phap-bao', [TheravadaController::class, 'apps']);
    Route::get('/feed.json', [TheravadaController::class, 'feedJson'])->name('feed');
    Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
});

// 3. Decode Subdomain Routes (e.g. decode.macatung.dev / decode.localhost)
Route::domain('decode.' . $baseDomain)->group(function () {
    Route::get('/', [DecodeController::class, 'index'])->name('decode.domain.index');
    Route::get('/tap/{slug}', [DecodeController::class, 'show'])->name('decode.domain.show');
    Route::get('/tap-1-visa-100k', [DecodeController::class, 'episode1'])->name('decode.domain.ep1');
    Route::get('/tap-01-quet-the-visa-100k-2-giay-du-hanh', [DecodeController::class, 'episode1'])->name('decode.domain.episode1.alias');
    Route::get('/sitemap.xml', [DecodeController::class, 'sitemap'])->name('decode.domain.sitemap');
    Route::get('/robots.txt', [DecodeController::class, 'robots'])->name('decode.domain.robots');
    Route::get('/favicon.ico', function () {
        return response()->file(public_path('brand/decode/favicon-decode.ico'), [
            'Content-Type' => 'image/x-icon',
            'Cache-Control' => 'public, max-age=604800, immutable',
        ]);
    })->name('decode.domain.favicon');
});

// 4. Decode Path-based Routes (Available on main domain /decode/* & local dev)
Route::prefix('decode')->name('decode.')->group(function () {
    Route::get('/', [DecodeController::class, 'index'])->name('index');
    Route::get('/tap/{slug}', [DecodeController::class, 'show'])->name('show');
    Route::get('/tap-1-visa-100k', [DecodeController::class, 'episode1'])->name('ep1');
    Route::get('/tap-01-quet-the-visa-100k-2-giay-du-hanh', [DecodeController::class, 'episode1'])->name('episode1.alias');
    Route::get('/sitemap.xml', [DecodeController::class, 'sitemap'])->name('sitemap');
});

// 5. Shopee Cashback Subdomain Routes (e.g. hoantien.macatung.dev / hoantien.localhost)
$prodSubdomain = 'hoantien.' . $baseDomain;
$localSubdomain = 'hoantien.localhost';

// Local development subdomain group (registered first so canonical names belong to production)
if ($localSubdomain !== $prodSubdomain) {
    Route::domain($localSubdomain)->group(function () {
        Route::get('/', [CashbackController::class, 'index']);
        Route::post('/generate-link', [CashbackController::class, 'generateLink']);
        Route::post('/withdraw', [CashbackController::class, 'withdraw']);
        Route::post('/webhook', [CashbackController::class, 'webhook']);
        Route::post('/sync', [CashbackController::class, 'sync']);
        Route::post('/auth/register', [CashbackAuthController::class, 'register']);
        Route::post('/auth/login', [CashbackAuthController::class, 'login']);
        Route::post('/auth/logout', [CashbackAuthController::class, 'logout']);
        Route::post('/profile/bank', [CashbackAuthController::class, 'updateBankProfile']);
        // Subdomain compatibility with /hoantien prefix
        Route::get('/hoantien', [CashbackController::class, 'index']);
        Route::post('/hoantien/generate-link', [CashbackController::class, 'generateLink']);
        Route::post('/hoantien/withdraw', [CashbackController::class, 'withdraw']);
        Route::post('/hoantien/webhook', [CashbackController::class, 'webhook']);
        Route::post('/hoantien/sync', [CashbackController::class, 'sync']);
        Route::post('/hoantien/auth/register', [CashbackAuthController::class, 'register']);
        Route::post('/hoantien/auth/login', [CashbackAuthController::class, 'login']);
        Route::post('/hoantien/auth/logout', [CashbackAuthController::class, 'logout']);
        Route::post('/hoantien/profile/bank', [CashbackAuthController::class, 'updateBankProfile']);
    });
}

// Canonical production subdomain group
Route::domain($prodSubdomain)->group(function () {
    Route::get('/', [CashbackController::class, 'index'])->name('cashback.domain.index');
    Route::post('/generate-link', [CashbackController::class, 'generateLink'])->name('cashback.domain.generate-link');
    Route::post('/withdraw', [CashbackController::class, 'withdraw'])->name('cashback.domain.withdraw');
    Route::post('/webhook', [CashbackController::class, 'webhook'])->name('cashback.domain.webhook');
    Route::post('/sync', [CashbackController::class, 'sync'])->name('cashback.domain.sync');
    Route::post('/auth/register', [CashbackAuthController::class, 'register'])->name('cashback.domain.auth.register');
    Route::post('/auth/login', [CashbackAuthController::class, 'login'])->name('cashback.domain.auth.login');
    Route::post('/auth/logout', [CashbackAuthController::class, 'logout'])->name('cashback.domain.auth.logout');
    Route::post('/profile/bank', [CashbackAuthController::class, 'updateBankProfile'])->name('cashback.domain.profile.bank');
    // Subdomain compatibility with /hoantien prefix
    Route::get('/hoantien', [CashbackController::class, 'index']);
    Route::post('/hoantien/generate-link', [CashbackController::class, 'generateLink']);
    Route::post('/hoantien/withdraw', [CashbackController::class, 'withdraw']);
    Route::post('/hoantien/webhook', [CashbackController::class, 'webhook']);
    Route::post('/hoantien/sync', [CashbackController::class, 'sync']);
    Route::post('/hoantien/auth/register', [CashbackAuthController::class, 'register']);
    Route::post('/hoantien/auth/login', [CashbackAuthController::class, 'login']);
    Route::post('/hoantien/auth/logout', [CashbackAuthController::class, 'logout']);
    Route::post('/hoantien/profile/bank', [CashbackAuthController::class, 'updateBankProfile']);
});

// 6. Shopee Cashback Path-based Fallback Routes (Available on main domain /hoantien/* & local dev)
Route::prefix('hoantien')->name('cashback.')->group(function () {
    Route::get('/', [CashbackController::class, 'index'])->name('index');
    Route::post('/generate-link', [CashbackController::class, 'generateLink'])->name('generate-link');
    Route::post('/withdraw', [CashbackController::class, 'withdraw'])->name('withdraw');
    Route::post('/webhook', [CashbackController::class, 'webhook'])->name('webhook');
    Route::post('/sync', [CashbackController::class, 'sync'])->name('sync');
    Route::post('/auth/register', [CashbackAuthController::class, 'register'])->name('auth.register');
    Route::post('/auth/login', [CashbackAuthController::class, 'login'])->name('auth.login');
    Route::post('/auth/logout', [CashbackAuthController::class, 'logout'])->name('auth.logout');
    Route::post('/profile/bank', [CashbackAuthController::class, 'updateBankProfile'])->name('profile.bank');
});

// 7. Global SEO & Asset Endpoints
Route::get('/favicon.ico', function (\Illuminate\Http\Request $request) {
    if (str_starts_with($request->getHost(), 'theravada.')) {
        return response()->file(public_path('brand/theravada/favicon-theravada.ico'), [
            'Content-Type' => 'image/x-icon',
            'Cache-Control' => 'public, max-age=604800, immutable',
        ]);
    }
    if (str_starts_with($request->getHost(), 'decode.')) {
        return response()->file(public_path('brand/decode/favicon-decode.ico'), [
            'Content-Type' => 'image/x-icon',
            'Cache-Control' => 'public, max-age=604800, immutable',
        ]);
    }
    if (file_exists(public_path('favicon.ico'))) {
        return response()->file(public_path('favicon.ico'), [
            'Content-Type' => 'image/x-icon',
            'Cache-Control' => 'public, max-age=604800, immutable',
        ]);
    }
    return response()->file(public_path('favicon.svg'), [
        'Content-Type' => 'image/svg+xml',
        'Cache-Control' => 'public, max-age=604800, immutable',
    ]);
})->name('favicon');
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
Route::get('/tools', [HomeController::class, 'game'])->name('tools.index');
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

    // Video & Pháp Thoại CMS (Milestone 3 / R3)
    Route::get('/theravada/videos', [AdminTheravadaVideoController::class, 'index'])->name('theravada.videos.index');
    Route::get('/theravada/videos/{article}', [AdminTheravadaVideoController::class, 'show'])->name('theravada.videos.show');
    Route::post('/theravada/videos/{article}/trigger', [AdminTheravadaVideoController::class, 'triggerPipeline'])->name('theravada.videos.trigger');
    Route::patch('/theravada/videos/{article}/publish', [AdminTheravadaVideoController::class, 'publish'])->name('theravada.videos.publish');
    Route::put('/theravada/videos/{article}', [AdminTheravadaVideoController::class, 'update'])->name('theravada.videos.update');

    // Site Settings & Profile CMS
    Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [AdminSettingController::class, 'update'])->name('settings.update');

    // Summoning Inquiries Inbox
    Route::get('/contacts', [AdminContactController::class, 'index'])->name('contacts.index');
    Route::delete('/contacts/{contact}', [AdminContactController::class, 'destroy'])->name('contacts.destroy');

    // Shopee Cashback & Withdrawal Management CMS
    Route::get('/cashback', [AdminCashbackController::class, 'index'])->name('cashback.index');
    Route::post('/cashback/withdrawals/{withdrawal}/approve', [AdminCashbackController::class, 'approve'])->name('cashback.withdrawals.approve');
    Route::post('/cashback/withdrawals/{withdrawal}/reject', [AdminCashbackController::class, 'reject'])->name('cashback.withdrawals.reject');
    Route::post('/cashback/sync', [AdminCashbackController::class, 'sync'])->name('cashback.sync');
});
