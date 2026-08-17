# Changes Log — Milestone 1: Foundation & Backend Setup

**Agent**: Worker 1 (`worker_m1_1`)  
**Date**: 2026-08-17  
**Milestone**: `m1_foundation_backend_setup`  

---

## 1. Backend Scaffolding & Infrastructure

1. **`composer.json`**:
   - Defined Laravel 11 (`laravel/framework: ^11.0`), Inertia Laravel (`inertiajs/inertia-laravel: ^1.3`), `guzzlehttp/guzzle: ^7.8`, `laravel/tinker: ^2.9`.
   - Defined Dev dependencies: `phpunit/phpunit: ^10.5`, `mockery/mockery: ^1.6`, `nunomaduro/collision: ^8.0`, `fakerphp/faker: ^1.23`.
   - Configured PSR-4 autoloading for `App\\` -> `app/`, `Database\\Factories\\` -> `database/factories/`, `Database\\Seeders\\` -> `database/seeders/`, `Tests\\` -> `tests/`.
   - Ran `composer install` via PHP 8.2 (`C:\laragon\bin\php\php-8.2.30-nts-Win32-vs16-x64\php.exe`).

2. **Core Laravel Runtime Files**:
   - `artisan`: CLI entrypoint loading `bootstrap/app.php` and handling ArgvInput.
   - `public/index.php`: Web request entrypoint capturing Request and handling via `bootstrap/app.php`.
   - `bootstrap/app.php`: Laravel 11 builder registering web/console routes, health endpoint `/up`, and `HandleInertiaRequests` middleware.
   - `bootstrap/providers.php` & `app/Providers/AppServiceProvider.php`: Service provider registry.
   - `bootstrap/cache/.gitignore`: Cache directory setup.

3. **Application Configuration (`config/`)**:
   - `config/app.php`: Application name, timezone (`Asia/Ho_Chi_Minh`), locale, encryption cipher.
   - `config/database.php`: SQLite default connection pointing to `database/database.sqlite`.
   - `config/session.php`: Session driver (`file`), cookie settings, 120min lifetime.
   - `config/logging.php`: Stack logging with single and daily handlers.
   - `config/filesystems.php`: Local and public disk configurations.
   - `config/cache.php`: File and array cache stores.

4. **Environment & Database**:
   - `.env` & `.env.example`: Configured with `APP_NAME="Macatung Portfolio"`, `DB_CONNECTION=sqlite`, `DB_DATABASE=d:/Work/macatung/database/database.sqlite`.
   - `database/database.sqlite`: Initialized SQLite database.
   - Ran `artisan key:generate`: Generated cryptographic `APP_KEY`.
   - `database/migrations/0001_01_01_000000_create_users_table.php`: Migrations for `users`, `password_reset_tokens`, `sessions`.
   - `database/migrations/0001_01_01_000001_create_cache_table.php`: Migrations for `cache`, `cache_locks`.
   - Ran `artisan migrate`: Successfully created tables in SQLite database.

5. **Inertia Bridge & Routing**:
   - `app/Http/Middleware/HandleInertiaRequests.php`: Inherits `Inertia\Middleware`, sets root view `'app'`, and shares `appName`, `flash` (`success`, `error`, `reference_id`), and `auth`.
   - `app/Http/Controllers/Controller.php`: Abstract base controller.
   - `app/Http/Controllers/HomeController.php`: Renders Inertia `Home` component with title prop.
   - `routes/web.php`: Registered route `GET /` -> `HomeController@index` named `home`.
   - `routes/console.php`: Artisan console commands.
   - `resources/views/app.blade.php`: Root Blade view containing `@inertiaHead`, `@inertia`, Google Fonts (JetBrains Mono, Plus Jakarta Sans, Space Grotesk, Syne, Cinzel Decorative), and `@vite(['resources/css/app.css', 'resources/js/app.ts'])`.

---

## 2. Frontend Scaffolding & Design System

1. **`package.json`**:
   - Replaced React dependencies with Vue 3 stack: `vue: ^3.5.0`, `@inertiajs/vue3: ^2.0.0`, `@vitejs/plugin-vue: ^5.2.1`, `laravel-vite-plugin: ^1.2.0`, `tailwindcss: ^3.4.17`, `postcss: ^8.4.49`, `autoprefixer: ^10.4.20`, `lucide-vue-next: ^0.469.0`, `canvas-confetti: ^1.9.4`, `@types/canvas-confetti: ^1.9.0`, `typescript: ^5.7.2`.
   - Ran `npm.cmd install` successfully.

2. **Build Configuration**:
   - `vite.config.ts`: Configured `laravel-vite-plugin` with entrypoints `resources/css/app.css` and `resources/js/app.ts`, `@vitejs/plugin-vue`, and alias `@` -> `./resources/js`.
   - `tailwind.config.js`: Target content paths `./resources/views/**/*.blade.php` and `./resources/js/**/*.{vue,ts,js,jsx,tsx}`, custom palettes (`midnight`, `talisman`, `phantom`), font families (`Plus Jakarta Sans`, `Space Grotesk`, `JetBrains Mono`, `Cinzel Decorative`), keyframes & animations (`hop`, `hop-fast`, `float`, `float-slow`, `pulseGlow`, `flutter`, `shimmer`), and custom drop shadow glows.
   - `postcss.config.js`: Tailwind & Autoprefixer plugins.
   - `tsconfig.json`: Strict TypeScript configuration targeting `resources/js/**/*`.
   - `resources/js/types/shims-vue.d.ts` & `resources/js/types/vite-env.d.ts`: Vue SFC and Vite client typing.

3. **Frontend Assets & Architecture**:
   - `resources/css/app.css`: Tailwind directives, dark midnight scrollbar, `.glass-panel`, `.glass-panel-glow`, `.glass-panel-talisman`, `.text-glow-mint`, `.text-glow-talisman`, `.talisman-paper`, `.bg-grid-pattern`, `.hover-card-glow`.
   - `resources/js/app.ts`: Client Inertia app setup using `createInertiaApp` and `resolvePageComponent`.
   - `resources/js/types/portfolio.ts`: Complete TypeScript interfaces (`Project`, `SkillCategory`, `SkillItem`, `ExperienceItem`, `DeveloperStat`, `TalismanPreset`, `ContactFormData`, `FlashMessages`, `AuthProps`, `PageProps`, `ISoundEngine`, `MascotProps`, `MascotEmits`).
   - `resources/js/data/`:
     - `projectsData.ts`: 6 full projects across all categories.
     - `skillsData.ts`: 4 categories with 18 skills and proficiency levels.
     - `experienceData.ts`: 4 career timeline milestones and 4 developer stats.
     - `talismanData.ts`: 6 talisman presets with spells and colors.
   - `resources/js/audio/soundEffects.ts`: Web Audio API procedural synthesizer (`SoundEngine` class) implementing `ISoundEngine` (`playHop`, `playTalisman`, `playClick`, `playTerminalKey`, `playSuccess`).
   - `resources/js/Pages/Home.vue`: Initial Inertia Home view with Vue 3 `<script setup lang="ts">`, ambient midnight glows, interactive hop counter, stats grid, and sound trigger.

---

## 3. Test Suites & Verification

1. **`tests/TestCase.php` & `tests/Feature/PageRenderTest.php`**:
   - `test_home_page_renders_successfully`: Verifies status 200 and Inertia component `Home`.
   - `test_inertia_shares_global_props`: Verifies shared `appName` and `flash` bag.
   - `phpunit.xml`: Configured in-memory SQLite and testing environment variables.
   - Result: `2 passed (17 assertions) in 0.70s`.

2. **Frontend Build Verification**:
   - Ran `npm.cmd run build`: Built cleanly in 3.04s (`public/build/manifest.json`, `public/build/assets/app-Cmhl7ULg.css`, `public/build/assets/Home-hY0M6zvg.js`, `public/build/assets/app-Co79ylnM.js`).

3. **Artisan Routes & Migrations**:
   - `artisan route:list`: Confirms `GET /` route exists.
   - `artisan migrate:status`: Confirms all SQLite tables migrated.
