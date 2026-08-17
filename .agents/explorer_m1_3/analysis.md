# Analysis: Laravel Backend Architecture & Scaffolding Blueprint (Milestone 1)

**Agent**: Explorer 3 (`explorer_m1_3`)  
**Target Milestone**: Milestone 1 (Foundation & Backend Setup)  
**Date**: 2026-08-17  
**Status**: COMPLETE  

---

## 1. Executive Summary

Milestone 1 requires establishing the full-stack foundation for the `macatung.dev` portfolio migration. The existing workspace was an un-monolithed standalone React/Vite project. This investigation details the exact backend architecture, configuration, routing, middleware, database initialization, and root Blade view requirements for Laravel 11/12 with Inertia.js (Vue 3), running under PHP 8.2.30 (`C:\laragon\bin\php\php-8.2.30-nts-Win32-vs16-x64\php.exe`) with SQLite.

---

## 2. Environment Verification

| Component | Path / Spec | Status | Capabilities Verified |
|-----------|-------------|--------|----------------------|
| **PHP CLI** | `C:\laragon\bin\php\php-8.2.30-nts-Win32-vs16-x64\php.exe` | **Verified** | PHP 8.2.30 NTS x64 |
| **PDO SQLite Driver** | Built-in PHP module `pdo_sqlite`, `sqlite3` | **Verified** | PDO SQLite 3.x available |
| **OpenSSL / cURL / Zip** | Built-in PHP modules | **Verified** | Required for Composer HTTPS downloads |
| **Composer CLI** | `C:\laragon\bin\composer\composer.phar` (v2.4.1) | **Verified** | Packagist HTTPS connectivity OK |
| **Node / NPM** | Node.js v20+ / npm v10+ | **Verified** | Ready for Vite build |

---

## 3. Laravel Backend Scaffolding Specification

### 3.1. `composer.json` Definition
The project requires Laravel 11 framework components along with `inertiajs/inertia-laravel`:

```json
{
    "name": "macatung/portfolio",
    "type": "project",
    "description": "macatung.dev Portfolio Full-Stack Migration",
    "keywords": ["laravel", "inertia", "vue3", "portfolio", "macatung"],
    "license": "MIT",
    "require": {
        "php": "^8.2",
        "inertiajs/inertia-laravel": "^1.3|^2.0",
        "laravel/framework": "^11.0",
        "laravel/tinker": "^2.9"
    },
    "require-dev": {
        "fakerphp/faker": "^1.23",
        "laravel/pint": "^1.13",
        "laravel/sail": "^1.26",
        "mockery/mockery": "^1.6",
        "nunomaduro/collision": "^8.1",
        "phpunit/phpunit": "^10.5|^11.0"
    },
    "autoload": {
        "psr-4": {
            "App\\": "app/",
            "Database\\Factories\\": "database/factories/",
            "Database\\Seeders\\": "database/seeders/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "Tests\\": "tests/"
        }
    },
    "scripts": {
        "post-autoload-dump": [
            "Illuminate\\Foundation\\ComposerScripts::postAutoloadDump",
            "@php artisan package:discover --ansi"
        ],
        "post-update-cmd": [
            "@php artisan vendor:publish --tag=laravel-assets --ansi --force"
        ],
        "post-root-package-install": [
            "@php -r \"file_exists('.env') || copy('.env.example', '.env');\""
        ],
        "post-create-project-cmd": [
            "@php artisan key:generate --ansi",
            "@php -r \"file_exists('database/database.sqlite') || touch('database/database.sqlite');\"",
            "@php artisan migrate --graceful --ansi"
        ]
    },
    "extra": {
        "laravel": {
            "dont-discover": []
        }
    },
    "config": {
        "optimize-autoloader": true,
        "preferred-install": "dist",
        "sort-packages": true,
        "allow-plugins": {
            "pestphp/pest-plugin": true,
            "php-http/discovery": true
        }
    },
    "minimum-stability": "stable",
    "prefer-stable": true
}
```

---

### 3.2. Entry Points & Bootstrap

#### `artisan` (Application CLI Entrypoint)
```php
#!/usr/bin/env php
<?php

use Symfony\Component\Console\Input\ArgvInput;

define('LARAVEL_START', microtime(true));

// Register the Composer autoloader...
require __DIR__.'/vendor/autoload.php';

// Bootstrap Laravel and handle the command...
(require_once __DIR__.'/bootstrap/app.php')
    ->handleCommand(new ArgvInput);
```

#### `public/index.php` (HTTP Web Entrypoint)
```php
<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());
```

#### `bootstrap/app.php` (Laravel 11 Application Configuration)
```php
<?php

use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            HandleInertiaRequests::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
```

#### `bootstrap/providers.php`
```php
<?php

return [
    App\Providers\AppServiceProvider::class,
];
```

#### `app/Providers/AppServiceProvider.php`
```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
```

---

### 3.3. Middleware: `app/Http/Middleware/HandleInertiaRequests.php`

The middleware extends `Inertia\Middleware`, binds the root view to `'app'`, and injects shared props including flash notifications and user/app state.

```php
<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'appName' => config('app.name', 'macatung.dev'),
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'reference_id' => fn () => $request->session()->get('reference_id'),
            ],
            'auth' => [
                'user' => $request->user(),
            ],
        ]);
    }
}
```

---

### 3.4. Controllers & Routes

#### `app/Http/Controllers/Controller.php`
```php
<?php

namespace App\Http\Controllers;

abstract class Controller
{
    //
}
```

#### `app/Http/Controllers/HomeController.php`
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    /**
     * Display the macatung.dev portfolio home page.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('Home', [
            'status' => 'online',
            'midnightMode' => true,
            'meta' => [
                'title' => 'macatung.dev — Code at midnight',
                'description' => 'Portfolio of macatung.dev — Full-Stack Night-Crawler & Creative Engineer crafting supernatural web applications under the midnight moon.',
            ],
        ]);
    }
}
```

#### `routes/web.php`
```php
<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
```

#### `routes/console.php`
```php
<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
```

---

### 3.5. Root Blade Template: `resources/views/app.blade.php`

Root template with all 4 required Google Fonts (`Plus Jakarta Sans`, `Space Grotesk`, `Syne`, `JetBrains Mono`), dark background styles, and Inertia directives (`@inertiaHead`, `@inertia`, `@vite`).

```blade
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark scroll-smooth">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='24' fill='%23070b14'/><circle cx='50' cy='52' r='28' fill='%231a233d' stroke='%2300f5a0' stroke-width='2'/><path d='M35 24 C35 16 65 16 65 24 L72 32 L28 32 Z' fill='%2311182c' stroke='%23ffd166' stroke-width='2'/><rect x='42' y='28' width='16' height='32' rx='3' fill='%23ffd166'/><circle cx='50' cy='36' r='3' fill='%23e63946'/><line x1='46' y1='44' x2='54' y2='44' stroke='%23e63946' stroke-width='1.5'/><line x1='46' y1='50' x2='54' y2='50' stroke='%23e63946' stroke-width='1.5'/><circle cx='40' cy='52' r='4' fill='%2300f5d4'/><circle cx='60' cy='52' r='4' fill='%2300f5d4'/><circle cx='40' cy='52' r='1.5' fill='%23ffffff'/><circle cx='60' cy='52' r='1.5' fill='%23ffffff'/><ellipse cx='34' cy='60' rx='3' ry='2' fill='%23ff0054' opacity='0.6'/><ellipse cx='66' cy='60' rx='3' ry='2' fill='%23ff0054' opacity='0.6'/><path d='M47 62 Q50 65 53 62' stroke='%23ffffff' stroke-width='2' fill='none' stroke-linecap='round'/></svg>" />
    <title inertia>{{ config('app.name', 'macatung.dev') }} — Code at midnight</title>
    <meta name="description" content="Portfolio of macatung.dev — Full-Stack Night-Crawler & Creative Engineer crafting supernatural web applications under the midnight moon." />

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:ital,wght@0,300;0,400;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&family=Syne:wght@700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.ts'])
    @inertiaHead
  </head>
  <body class="bg-midnight-950 text-slate-100 font-sans antialiased selection:bg-phantom-mint selection:text-midnight-950 overflow-x-hidden">
    @inertia
  </body>
</html>
```

---

### 3.6. Configuration Files

#### `config/app.php`
```php
<?php

return [
    'name' => env('APP_NAME', 'macatung.dev'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'timezone' => env('APP_TIMEZONE', 'Asia/Ho_Chi_Minh'),
    'locale' => env('APP_LOCALE', 'en'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),
    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),
    'cipher' => 'AES-256-CBC',
    'key' => env('APP_KEY'),
    'previous_keys' => [
        ...array_filter(
            explode(',', (string) env('APP_PREVIOUS_KEYS', ''))
        ),
    ],
    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],
];
```

#### `config/database.php`
```php
<?php

return [
    'default' => env('DB_CONNECTION', 'sqlite'),
    'connections' => [
        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DB_URL'),
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
            'busy_timeout' => null,
            'journal_mode' => null,
            'synchronous' => null,
        ],
    ],
    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => true,
    ],
];
```

#### `config/session.php`
```php
<?php

use Illuminate\Support\Str;

return [
    'driver' => env('SESSION_DRIVER', 'database'),
    'lifetime' => (int) env('SESSION_LIFETIME', 120),
    'expire_on_close' => env('SESSION_EXPIRE_ON_CLOSE', false),
    'encrypt' => env('SESSION_ENCRYPT', false),
    'files' => storage_path('framework/sessions'),
    'connection' => env('SESSION_CONNECTION'),
    'table' => env('SESSION_TABLE', 'sessions'),
    'store' => env('SESSION_STORE'),
    'lottery' => [2, 100],
    'cookie' => env(
        'SESSION_COOKIE',
        Str::slug(env('APP_NAME', 'laravel'), '_').'_session'
    ),
    'path' => env('SESSION_PATH', '/'),
    'domain' => env('SESSION_DOMAIN'),
    'secure' => env('SESSION_SECURE_COOKIE'),
    'http_only' => env('SESSION_HTTP_ONLY', true),
    'same_site' => env('SESSION_SAME_SITE', 'lax'),
    'partitioned' => env('SESSION_PARTITIONED_COOKIE', false),
];
```

#### `config/cache.php`, `config/logging.php`, `config/filesystems.php`
Standard Laravel configurations for local file/database storage and single-channel debug logging.

---

### 3.7. SQLite Database & Environment Setup

#### `database/database.sqlite`
- Initialized as an empty file in `database/database.sqlite`.

#### `.env.example` & `.env`
```ini
APP_NAME="macatung.dev"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_TIMEZONE=Asia/Ho_Chi_Minh
APP_URL=http://localhost:8000

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database
CACHE_PREFIX=

VITE_APP_NAME="${APP_NAME}"
```

---

### 3.8. Standard Migrations

#### `database/migrations/0001_01_01_000000_create_users_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
```

#### `database/migrations/0001_01_01_000001_create_cache_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cache');
        Schema::dropIfExists('cache_locks');
    }
};
```

#### `database/migrations/0001_01_01_000002_create_jobs_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('failed_jobs');
    }
};
```

---

### 3.9. Backend Tests & Testing Infrastructure

#### `phpunit.xml`
```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true"
>
    <testsuites>
        <testsuite name="Feature">
            <directory>tests/Feature</directory>
        </testsuite>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
    </testsuites>
    <source>
        <include>
            <directory>app</directory>
        </include>
    </source>
    <php>
        <env name="APP_ENV" value="testing"/>
        <env name="APP_MAINTENANCE_DRIVER" value="file"/>
        <env name="BCRYPT_ROUNDS" value="4"/>
        <env name="CACHE_STORE" value="array"/>
        <env name="DB_CONNECTION" value="sqlite"/>
        <env name="DB_DATABASE" value=":memory:"/>
        <env name="MAIL_MAILER" value="array"/>
        <env name="PULSE_ENABLED" value="false"/>
        <env name="QUEUE_CONNECTION" value="sync"/>
        <env name="SESSION_DRIVER" value="array"/>
        <env name="TELESCOPE_ENABLED" value="false"/>
    </php>
</phpunit>
```

#### `tests/TestCase.php`
```php
<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    //
}
```

#### `tests/Feature/PageRenderTest.php`
```php
<?php

namespace Tests\Feature;

use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PageRenderTest extends TestCase
{
    /**
     * Test home page returns 200 and renders Inertia Home component.
     */
    public function test_home_page_renders_successfully(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Home')
            ->has('status')
            ->has('midnightMode')
            ->has('meta')
        );
    }

    /**
     * Test shared Inertia data contains appName and flash structure.
     */
    public function test_shared_inertia_data_has_expected_keys(): void
    {
        $response = $this->get('/');

        $response->assertInertia(fn (Assert $page) => $page
            ->has('appName')
            ->has('flash')
        );
    }
}
```

---

## 4. Worker Step-by-Step Execution Plan

The Worker should execute the following 7 concrete phases:

1. **Storage & Directory Structure Scaffolding**:
   Create directories:
   `app/Http/Controllers`, `app/Http/Middleware`, `app/Models`, `app/Providers`, `bootstrap`, `config`, `database/factories`, `database/migrations`, `database/seeders`, `public`, `resources/views`, `resources/css`, `resources/js/Pages`, `routes`, `storage/app/public`, `storage/framework/cache/data`, `storage/framework/sessions`, `storage/framework/views`, `storage/logs`, `tests/Feature`, `tests/Unit`.

2. **File Generation**:
   Write `composer.json`, `artisan`, `public/index.php`, `bootstrap/app.php`, `bootstrap/providers.php`, `app/Providers/AppServiceProvider.php`, `app/Http/Controllers/Controller.php`, `app/Http/Controllers/HomeController.php`, `app/Http/Middleware/HandleInertiaRequests.php`, `config/app.php`, `config/database.php`, `config/session.php`, `config/cache.php`, `config/logging.php`, `config/filesystems.php`, `routes/web.php`, `routes/console.php`, `resources/views/app.blade.php`, `database/database.sqlite`, `database/migrations/0001_01_01_000000_create_users_table.php`, `database/migrations/0001_01_01_000001_create_cache_table.php`, `database/migrations/0001_01_01_000002_create_jobs_table.php`, `database/seeders/DatabaseSeeder.php`, `.env.example`, `.env`, `phpunit.xml`, `tests/TestCase.php`, and `tests/Feature/PageRenderTest.php`.

3. **Composer Installation**:
   Execute:
   ```powershell
   & "C:\laragon\bin\php\php-8.2.30-nts-Win32-vs16-x64\php.exe" "C:\laragon\bin\composer\composer.phar" install --no-interaction
   ```

4. **Application Key Generation & SQLite Initialization**:
   Execute:
   ```powershell
   & "C:\laragon\bin\php\php-8.2.30-nts-Win32-vs16-x64\php.exe" artisan key:generate --ansi
   & "C:\laragon\bin\php\php-8.2.30-nts-Win32-vs16-x64\php.exe" artisan migrate --ansi
   ```

5. **Frontend Asset Setup & Build Integration (Coordination with Explorer 1 & 2)**:
   - Configure `package.json` with Vue 3, `@inertiajs/vue3`, `laravel-vite-plugin`, `@vitejs/plugin-vue`, `tailwindcss`, `lucide-vue-next`, `canvas-confetti`.
   - Update `vite.config.ts` and `tailwind.config.js`.
   - Create `resources/js/app.ts`, `resources/css/app.css`, and skeleton `resources/js/Pages/Home.vue`.
   - Run `npm run build` to verify clean compilation.

6. **Backend Verification Tests**:
   Execute:
   ```powershell
   & "C:\laragon\bin\php\php-8.2.30-nts-Win32-vs16-x64\php.exe" artisan test
   ```

---

## 5. Potential Pitfalls & Mitigations

1. **PHP Binary Path Drift**:
   *Pitfall*: Running `php` directly in powershell might invoke whatever is in system PATH (or older PHP).
   *Mitigation*: Explicitly call `C:\laragon\bin\php\php-8.2.30-nts-Win32-vs16-x64\php.exe`.
2. **SQLite Database Missing / Locked**:
   *Pitfall*: Laravel throws database not found if `database/database.sqlite` does not exist prior to migration.
   *Mitigation*: Pre-create `database/database.sqlite` empty file before running `artisan migrate`.
3. **Inertia Root View Mismatch**:
   *Pitfall*: If `HandleInertiaRequests::$rootView` is set to anything other than `'app'`, Laravel looks for a non-existent blade file.
   *Mitigation*: Strict alignment on `protected $rootView = 'app';` matching `resources/views/app.blade.php`.
4. **Vite Manifest Resolution**:
   *Pitfall*: Laravel's `@vite` directive looks in `public/build/manifest.json`.
   *Mitigation*: Ensure `vite.config.ts` outputs to default `public/build` when building.
