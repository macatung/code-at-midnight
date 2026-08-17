# Detailed Analysis: Milestone 1 Foundation & Backend Setup

**Date**: 2026-08-17  
**Explorer**: Explorer 1 (`.agents/explorer_m1_1/`)  
**Scope**: Workspace inspection, runtime verification, and Laravel 11 + Inertia.js (Vue 3) architecture blueprint  

---

## 1. Workspace Inventory & Current State

### 1.1 Existing Files at `d:/Work/macatung/`
The workspace currently houses a standalone React 19 + TypeScript single-page application built with Vite:

- **Root Configs**:
  - `package.json`: Configured for React 19 (`react`, `react-dom`, `@vitejs/plugin-react`, `lucide-react`, `tailwindcss`, `clsx`, `tailwind-merge`, `canvas-confetti`).
  - `vite.config.ts`: Configured solely with `@vitejs/plugin-react`.
  - `tailwind.config.js`: Contains custom theme tokens (`midnight`, `talisman`, `phantom`), custom font families (`Plus Jakarta Sans`, `Space Grotesk`, `JetBrains Mono`, `Cinzel Decorative`), keyframe animations (`hop`, `float`, `pulseGlow`, `flutter`, `shimmer`), and custom glow shadows. Content paths currently target `./src/**/*.{js,ts,jsx,tsx}` and `./index.html`.
  - `tsconfig.json`, `tsconfig.app.json`, `tsconfig.node.json`.
  - `index.html`: Contains Google Font imports and `<div id="root"></div>`.
- **Existing Source (`src/`)**:
  - `src/types/portfolio.ts`: Core data interfaces (`Project`, `SkillCategory`, `ExperienceItem`, `DeveloperStat`, `TalismanPreset`).
  - `src/data/`: Static data files (`projectsData.ts`, `skillsData.ts`, `experienceData.ts`, `talismanData.ts`).
  - `src/audio/soundEffects.ts`: Pure Web Audio API synthesizer (`SoundEngine` class) with zero external dependencies.
  - `src/components/`: 11 component directories (`about/`, `contact/`, `experience/`, `hero/`, `layout/`, `mascot/`, `projects/`, `skills/`, `talisman/`, `terminal/`, `ui/`).
  - `src/assets/`: `hero.png`, `react.svg`, `vite.svg`.

### 1.2 Missing Laravel Backend & Inertia Infrastructure
None of the Laravel core files currently exist in the root directory:
- ❌ `composer.json`
- ❌ `artisan`
- ❌ `app/` (`Http/Controllers/HomeController.php`, `Http/Middleware/HandleInertiaRequests.php`)
- ❌ `bootstrap/` (`app.php`, `providers.php`)
- ❌ `config/` (`app.php`, `database.php`, `session.php`, `cache.php`)
- ❌ `database/` (`database.sqlite`, `migrations/`)
- ❌ `public/index.php`
- ❌ `resources/views/app.blade.php`
- ❌ `resources/js/app.ts`, `resources/js/Pages/Home.vue`
- ❌ `resources/css/app.css`
- ❌ `routes/web.php`

---

## 2. Runtime & Environment Verification

### 2.1 PHP Binary & Modules
- **Explicit PHP Path**: `C:\laragon\bin\php\php-8.2.30-nts-Win32-vs16-x64\php.exe`
- **PHP Version**: `PHP 8.2.30 (cli) (built: Dec 16 2025 17:41:11) (NTS Visual C++ 2019 x64)`
- **PHP Modules Verified**:
  - `pdo_sqlite` & `sqlite3` — Active ✅
  - `curl`, `openssl`, `mbstring`, `fileinfo`, `intl`, `gd`, `zip`, `dom`, `json`, `tokenizer` — Active ✅
  - Meets all prerequisites for Laravel 11/12 and SQLite database engine.

### 2.2 Critical Environment Gotcha: System PATH vs Explicit PHP
- **Finding**: Executing `php` or `composer` directly in the terminal invokes the default system PATH PHP, which is **PHP 5.6.26** (`C:\laragon\bin\php\php-5.6.26-Win32-VC11-x64\php.exe`).
- **Error if run naked**: `Composer 2.3.0 dropped support for PHP <7.2.5 and you are running 5.6.26... Aborting.`
- **Mandatory Invocation Rule**: All PHP, Composer, and Artisan commands MUST explicitly reference the PHP 8.2 binary:
  ```powershell
  # Composer command invocation
  & "C:\laragon\bin\php\php-8.2.30-nts-Win32-vs16-x64\php.exe" "C:\laragon\bin\composer\composer.phar" <args>

  # Artisan command invocation
  & "C:\laragon\bin\php\php-8.2.30-nts-Win32-vs16-x64\php.exe" artisan <args>
  ```
- **Composer Binary Path**: `C:\laragon\bin\composer\composer.phar` (version 2.4.1, connectivity to packagist/github verified OK).

### 2.3 Node.js & NPM Verification
- **Node.js**: `v24.14.0`
- **NPM**: `11.9.0`
- **PowerShell Execution Policy Gotcha**: Direct invocation of `npm` executes `npm.ps1`, which is blocked by Windows PowerShell script execution policy.
- **Mandatory Invocation Rule**: Use `npm.cmd` or `npx.cmd` in all commands:
  ```powershell
  npm.cmd install
  npm.cmd run build
  npx.cmd vite build
  ```

---

## 3. Laravel 11/12 + Inertia.js (Vue 3) Dependency Matrix

### 3.1 Backend: `composer.json`
```json
{
  "name": "macatung/portfolio",
  "type": "project",
  "description": "macatung.dev — Full-stack portfolio application powered by Laravel 11 & Inertia.js (Vue 3)",
  "keywords": ["laravel", "inertia", "vue3", "portfolio", "macatung"],
  "license": "MIT",
  "require": {
    "php": "^8.2",
    "guzzlehttp/guzzle": "^7.8",
    "inertiajs/inertia-laravel": "^1.3",
    "laravel/framework": "^11.0",
    "laravel/tinker": "^2.9"
  },
  "require-dev": {
    "fakerphp/faker": "^1.23",
    "laravel/pint": "^1.13",
    "mockery/mockery": "^1.6",
    "nunomaduro/collision": "^8.0",
    "phpunit/phpunit": "^10.5"
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
    ]
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

### 3.2 Frontend: `package.json`
```json
{
  "name": "macatung",
  "private": true,
  "version": "1.0.0",
  "type": "module",
  "scripts": {
    "dev": "vite",
    "build": "vite build",
    "preview": "vite preview"
  },
  "dependencies": {
    "@inertiajs/vue3": "^2.0.0",
    "canvas-confetti": "^1.9.4",
    "clsx": "^2.1.1",
    "lucide-vue-next": "^0.469.0",
    "tailwind-merge": "^3.0.0",
    "vue": "^3.5.0"
  },
  "devDependencies": {
    "@types/canvas-confetti": "^1.9.0",
    "@types/node": "^24.13.3",
    "@vitejs/plugin-vue": "^5.2.0",
    "autoprefixer": "^10.5.4",
    "laravel-vite-plugin": "^1.2.0",
    "postcss": "^8.5.26",
    "tailwindcss": "^3.4.17",
    "typescript": "~6.0.2",
    "vite": "^8.2.0"
  }
}
```

---

## 4. Scaffolding & Architecture Blueprint

### 4.1 Laravel Bootstrap & Config (`bootstrap/app.php`)
In Laravel 11, application routing and middleware are configured concisely in `bootstrap/app.php`:
```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\HandleInertiaRequests;

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

### 4.2 Inertia Middleware (`app/Http/Middleware/HandleInertiaRequests.php`)
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
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'reference_id' => fn () => $request->session()->get('reference_id'),
            ],
        ]);
    }
}
```

### 4.3 Root Blade Template (`resources/views/app.blade.php`)
```html
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title inertia>{{ config('app.name', 'macatung.dev — Code at midnight') }}</title>
    <meta name="description" content="Portfolio of macatung.dev — Full-Stack Night-Crawler & Creative Engineer crafting supernatural web applications under the midnight moon.">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='24' fill='%23070b14'/><circle cx='50' cy='52' r='28' fill='%231a233d' stroke='%2300f5a0' stroke-width='2'/><path d='M35 24 C35 16 65 16 65 24 L72 32 L28 32 Z' fill='%2311182c' stroke='%23ffd166' stroke-width='2'/><rect x='42' y='28' width='16' height='32' rx='3' fill='%23ffd166'/><circle cx='50' cy='36' r='3' fill='%23e63946'/><line x1='46' y1='44' x2='54' y2='44' stroke='%23e63946' stroke-width='1.5'/><line x1='46' y1='50' x2='54' y2='50' stroke='%23e63946' stroke-width='1.5'/><circle cx='40' cy='52' r='4' fill='%2300f5d4'/><circle cx='60' cy='52' r='4' fill='%2300f5d4'/><circle cx='40' cy='52' r='1.5' fill='%23ffffff'/><circle cx='60' cy='52' r='1.5' fill='%23ffffff'/><ellipse cx='34' cy='60' rx='3' ry='2' fill='%23ff0054' opacity='0.6'/><ellipse cx='66' cy='60' rx='3' ry='2' fill='%23ff0054' opacity='0.6'/><path d='M47 62 Q50 65 53 62' stroke='%23ffffff' stroke-width='2' fill='none' stroke-linecap='round'/></svg>" />

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel+Decorative:wght@700&family=JetBrains+Mono:ital,wght@0,300;0,400;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&family=Syne:wght@700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.ts'])
    @inertiaHead
</head>
<body class="bg-midnight-950 text-slate-100 font-sans antialiased selection:bg-phantom-mint selection:text-midnight-950 overflow-x-hidden">
    @inertia
</body>
</html>
```

### 4.4 Vite & Tailwind Configs
- **`vite.config.ts`**:
  ```typescript
  import { defineConfig } from 'vite';
  import laravel from 'laravel-vite-plugin';
  import vue from '@vitejs/plugin-vue';
  import path from 'path';

  export default defineConfig({
    plugins: [
      laravel({
        input: ['resources/css/app.css', 'resources/js/app.ts'],
        refresh: true,
      }),
      vue({
        template: {
          transformAssetUrls: {
            base: null,
            includeAbsolute: false,
          },
        },
      }),
    ],
    resolve: {
      alias: {
        '@': path.resolve(__dirname, './resources/js'),
      },
    },
  });
  ```
- **`tailwind.config.js`**:
  Update `content` to:
  ```javascript
  content: [
    './resources/views/**/*.blade.php',
    './resources/js/**/*.{vue,ts,js}',
  ],
  ```

### 4.5 Frontend Client Entrypoint (`resources/js/app.ts`)
```typescript
import '../css/app.css';
import { createApp, h, DefineComponent } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'macatung.dev';

createInertiaApp({
  title: (title) => `${title} — ${appName}`,
  resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob<DefineComponent>('./Pages/**/*.vue')),
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .mount(el);
  },
  progress: {
    color: '#00f5a0',
    showSpinner: true,
  },
});
```

### 4.6 Controller & Routes
- **`routes/web.php`**:
  ```php
  <?php

  use Illuminate\Support\Facades\Route;
  use App\Http\Controllers\HomeController;

  Route::get('/', [HomeController::class, 'index'])->name('home');
  ```
- **`app/Http/Controllers/HomeController.php`**:
  ```php
  <?php

  namespace App\Http\Controllers;

  use Inertia\Inertia;
  use Inertia\Response;

  class HomeController extends Controller
  {
      public function index(): Response
      {
          return Inertia::render('Home', [
              'title' => 'Code at midnight',
          ]);
      }
  }
  ```

### 4.7 Database Initialization
- `database/database.sqlite` empty file created.
- `.env` set with `DB_CONNECTION=sqlite`, `DB_DATABASE=d:/Work/macatung/database/database.sqlite` (or relative path `database/database.sqlite`).

---

## 5. Summary of Actions for Milestone 1 Implementation

| Step | Component | Action |
|------|-----------|--------|
| 1 | `composer.json` & Laravel Core | Create `composer.json` and run composer install using PHP 8.2 binary. |
| 2 | Laravel Skeleton | Generate `artisan`, `bootstrap/app.php`, `config/`, `public/index.php`, `.env`. |
| 3 | SQLite DB | Create `database/database.sqlite`. |
| 4 | Inertia Bridge | Create `app/Http/Middleware/HandleInertiaRequests.php`, `resources/views/app.blade.php`, and `app/Http/Controllers/HomeController.php`. |
| 5 | Frontend Build | Update `package.json` for Vue 3 + Inertia, install NPM packages via `npm.cmd`, configure `vite.config.ts`, `tailwind.config.js`, `tsconfig.json`. |
| 6 | Types & Data Migration | Copy and adapt `src/types/portfolio.ts` to `resources/js/types/portfolio.ts`, `src/data/*.ts` to `resources/js/data/*.ts`, and `src/audio/soundEffects.ts` to `resources/js/audio/soundEffects.ts`. |
| 7 | Entrypoint & Home Skeleton | Create `resources/js/app.ts`, `resources/css/app.css`, and initial `resources/js/Pages/Home.vue`. |
| 8 | Build & Verification | Run `npm.cmd run build` and test PHP/artisan execution to verify zero compiler errors. |
