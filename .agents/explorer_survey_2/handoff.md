# Environment & Framework Survey Report — macatung.dev Migration

## 1. Observation

Direct observations from inspection of the host environment, runtime binaries, and current codebase at `d:/Work/macatung`:

### 1.1 Runtime Binaries & System Tools

| Tool | Version / Location | Verification Command & Result | Status |
|---|---|---|---|
| **PHP (Active Target)** | **PHP 8.2.30** (NTS x64 Visual C++ 2019)<br>`C:\laragon\bin\php\php-8.2.30-nts-Win32-vs16-x64\php.exe` | `& 'C:\laragon\bin\php\php-8.2.30-nts-Win32-vs16-x64\php.exe' -v`<br>PDO drivers: `mysql, pgsql, sqlite`<br>Extensions enabled: `curl, openssl, mbstring, pdo_sqlite, pdo_mysql, tokenizer, xml, zip, bcmath, fileinfo` | ✅ Ready for Laravel 11/12 |
| **PHP (Alternative 1)** | **PHP 8.3.33**<br>`C:\laragon\bin\php\php-8.3.33-nts-Win32-vs16-x64\php.exe` | `& 'C:\laragon\bin\php\php-8.3.33-nts-Win32-vs16-x64\php.exe' -v` | ✅ Available |
| **PHP (Alternative 2)** | **PHP 8.4.24**<br>`C:\laragon\bin\php\php-8.4.24-nts-Win32-vs17-x64\php.exe` | `& 'C:\laragon\bin\php\php-8.4.24-nts-Win32-vs17-x64\php.exe' -v` | ✅ Available |
| **PHP (System PATH)** | **PHP 5.6.26** (Legacy default)<br>`C:\laragon\bin\php\php-5.6.26-Win32-VC11-x64\php.exe` | `where.exe php` | ⚠️ **Must avoid bare `php` command without PATH override** |
| **Composer** | **Composer 2.4.1**<br>`C:\laragon\bin\composer\composer.phar`<br>`C:\laragon\bin\composer\composer.bat` | `& 'C:\laragon\bin\php\php-8.2.30-nts-Win32-vs16-x64\php.exe' C:\laragon\bin\composer\composer.phar --version`<br>Packagist connectivity: OK | ✅ Ready |
| **Node.js** | **v24.14.0**<br>`C:\Program Files\nodejs\node.exe` | `node -v` -> `v24.14.0` | ✅ Ready |
| **npm** | **11.9.0**<br>`C:\Program Files\nodejs\npm.cmd` | `npm.cmd -v` -> `11.9.0`<br>Note: Use `npm.cmd` in PowerShell to bypass `.ps1` ExecutionPolicy restriction | ✅ Ready |
| **Git** | **2.37.3.windows.1**<br>`C:\laragon\bin\git\cmd\git.exe` | `git --version` | ✅ Ready |

### 1.2 Current Codebase Structure (`d:/Work/macatung`)

- **Current Stack**: Standalone React 19 + TypeScript + Vite SPA.
- **Existing `package.json`**:
  - Dependencies: `canvas-confetti` (1.9.4), `clsx` (2.1.1), `lucide-react` (1.31.0), `react` (19.2.8), `react-dom` (19.2.8), `tailwind-merge` (3.6.0).
  - DevDependencies: `@types/canvas-confetti`, `@types/node`, `@types/react`, `@types/react-dom`, `@vitejs/plugin-react` (6.0.4), `autoprefixer` (10.5.4), `oxlint` (1.75.0), `postcss` (8.5.26), `tailwindcss` (3.4.17), `typescript` (~6.0.2), `vite` (^8.2.0).
- **Existing UI Components in `src/`**:
  - `src/audio/soundEffects.ts` — Web Audio API procedural synthesizer (zero external sound dependencies).
  - `src/types/portfolio.ts` — TypeScript interfaces for Project, SkillCategory, ExperienceItem, DeveloperStat, TalismanPreset.
  - `src/data/` — `projectsData.ts`, `skillsData.ts`, `experienceData.ts`, `talismanData.ts`.
  - `src/components/mascot/` — `MacatungMascot.tsx`, `MidnightClock.tsx`, `TalismanCanvas.tsx`.
  - `src/components/terminal/` — `MidnightTerminal.tsx`.
  - `src/components/talisman/` — `TalismanGenerator.tsx`.
  - `src/components/projects/` — `ProjectsSection.tsx`, `ProjectModal.tsx`.
  - `src/components/skills/` — `SkillsSection.tsx`.
  - `src/components/experience/` — `ExperienceSection.tsx`.
  - `src/components/about/` — `AboutSection.tsx`.
  - `src/components/hero/` — `HeroSection.tsx`.
  - `src/components/contact/` — `ContactSection.tsx`.
  - `src/components/layout/` — `Navbar.tsx`, `Footer.tsx`, `SoundToggle.tsx`.
  - `src/components/ui/` — `Icons.tsx`.
- **Existing Styling**:
  - `tailwind.config.js` — Custom color palettes (`midnight`, `talisman`, `phantom`), custom keyframe animations (`hop`, `hop-fast`, `float`, `float-slow`, `pulseGlow`, `talisman-flutter`, `shimmer`), custom box shadows.
  - `src/index.css` — Custom glassmorphic panel classes (`glass-panel`, `glass-panel-glow`, `glass-panel-talisman`), talisman paper texture, grid patterns, custom midnight scrollbar.

### 1.3 Database Readiness

- PHP 8.2 PDO extension confirms active SQLite support (`pdo_sqlite`, `sqlite3`).
- Target configuration: `DB_CONNECTION=sqlite`, `DB_DATABASE=database/database.sqlite`.
- SQLite requires zero background services or daemons and executes instantly in local environments.

---

## 2. Logic Chain

1. **PHP Execution Isolation**:
   - *Observation*: Default `php` executable in PATH points to PHP 5.6.26, while PHP 8.2.30 / 8.3.33 / 8.4.24 are installed in `C:\laragon\bin\php\`.
   - *Logic*: All Composer commands, Artisan commands, and test runners must explicitly invoke `C:\laragon\bin\php\php-8.2.30-nts-Win32-vs16-x64\php.exe` or execute with the directory prepended to `PATH` in the command session (e.g. `$env:PATH = 'C:\laragon\bin\php\php-8.2.30-nts-Win32-vs16-x64;' + $env:PATH`).

2. **PowerShell Script Policy for npm**:
   - *Observation*: Invoking `npm` directly in PowerShell can trigger `PSSecurityException` regarding `npm.ps1`.
   - *Logic*: Invoking `npm.cmd` directly avoids PowerShell script execution policy checks and executes cleanly.

3. **Laravel + Inertia.js Framework Setup Requirements**:
   - *Observation*: The repository currently lacks Laravel scaffolding (`artisan`, `composer.json`, `app/`, `routes/`, `resources/views/app.blade.php`).
   - *Logic*: The migration requires setting up:
     1. `composer.json` containing:
        - `php: ^8.2`
        - `laravel/framework: ^11.0` (or `^10.0`)
        - `inertiajs/inertia-laravel: ^1.0 || ^2.0`
     2. Laravel foundation structure: `artisan`, `bootstrap/app.php`, `config/`, `routes/web.php`, `app/Http/Controllers/`, `app/Models/`, `database/migrations/`.
     3. Inertia root template `resources/views/app.blade.php` with `@inertiaHead` and `@inertia`.
     4. Inertia middleware `HandleInertiaRequests` sharing flash messages and metadata.
     5. SQLite database migration for contact submissions: `contact_submissions` table (`name`, `email`, `project_type`, `coffee_offering`, `message`, `ip_address`, `timestamps`).

4. **Frontend Vue 3 Package & Tooling Requirements**:
   - *Observation*: The frontend is currently React-based (`@vitejs/plugin-react`, `lucide-react`).
   - *Logic*: The frontend migration requires:
     - Replacing React dependencies with:
       - `vue: ^3.4`
       - `@inertiajs/vue3: ^1.0 || ^2.0`
       - `@vitejs/plugin-vue: ^5.0`
       - `laravel-vite-plugin: ^1.0`
       - `lucide-vue-next: ^0.460` (for Lucide Vue 3 icons)
       - `canvas-confetti: ^1.9` & `@types/canvas-confetti`
     - Updating `vite.config.js` / `vite.config.ts` to load `laravel()` and `vue()` plugins.
     - Porting all 14+ components from React `.tsx` to Vue 3 `<script setup lang="ts">` `.vue` single file components in `resources/js/`.

---

## 3. Caveats

1. **System PATH PHP version**: Do not run bare `composer` or `php` in shells without specifying the full path or setting `$env:PATH`, as it will invoke PHP 5.6.26 and fail immediately.
2. **ExecutionPolicy**: Do not run bare `npm` without `.cmd` extension in standard PowerShell sessions if script execution is restricted.
3. **Database File Creation**: SQLite database file (`database/database.sqlite`) must exist before running `php artisan migrate`. If it does not exist, running `touch database/database.sqlite` (or `New-Item -ItemType File database/database.sqlite`) will initialize it.
4. **Port Availability**: Default Laravel dev server uses port 8000 and Vite uses port 5173. If occupied, port parameters can be specified (`--port 8001`, etc.).

---

## 4. Conclusion

The host machine environment is fully equipped and verified for the complete Laravel + Inertia.js (Vue 3) migration:
- **PHP**: PHP 8.2.30 with all required PDO SQLite / MySQL / OpenSSL / cURL extensions is available at `C:\laragon\bin\php\php-8.2.30-nts-Win32-vs16-x64\php.exe`.
- **Composer**: Composer 2.4.1 is functional and verified connecting to Packagist.
- **Node & npm**: Node v24.14.0 and npm 11.9.0 (`npm.cmd`) are ready.
- **Database**: SQLite file database is optimal for zero-daemon operation.
- **Frontend**: Standard Vue 3 Composition API (`<script setup>`) with `@inertiajs/vue3`, `lucide-vue-next`, `canvas-confetti`, and Tailwind CSS 3 will provide exact parity and improved responsiveness for all 14+ portfolio features.

### Required Setup Sequence for Implementers:
1. **Laravel Scaffolding**: Setup `composer.json` (Laravel 11, Inertia Laravel), install packages via `C:\laragon\bin\php\php-8.2.30-nts-Win32-vs16-x64\php.exe C:\laragon\bin\composer\composer.phar install`.
2. **Database & Backend**: Initialize `database/database.sqlite`, create migration for `contact_submissions`, create `ContactSubmission` model, create `ContactController` with validation and Inertia response, and register `HandleInertiaRequests` middleware.
3. **Frontend Packages**: Update `package.json` with Vue 3, `@inertiajs/vue3`, `@vitejs/plugin-vue`, `laravel-vite-plugin`, `lucide-vue-next`, `canvas-confetti`, and install via `npm.cmd install`.
4. **Vite & Tailwind Config**: Configure `vite.config.js` and `tailwind.config.js` with theme definitions, pointing to `resources/js/app.ts` and `resources/views/app.blade.php`.
5. **Component Migration**: Port all components into `resources/js/Components/` and `resources/js/Pages/Home.vue` in Vue 3 `<script setup>`.
6. **Build Verification**: Run `npm.cmd run build` and `php artisan test` (or route checks) to verify 0 errors.

---

## 5. Verification Method

Independent verification steps to confirm environment and framework readiness:

```powershell
# 1. Verify PHP 8.2.30 binary and SQLite driver
& 'C:\laragon\bin\php\php-8.2.30-nts-Win32-vs16-x64\php.exe' -v
& 'C:\laragon\bin\php\php-8.2.30-nts-Win32-vs16-x64\php.exe' -r "echo 'PDO Drivers: ' . implode(', ', PDO::getAvailableDrivers()) . PHP_EOL;"

# 2. Verify Composer execution with PHP 8.2
& 'C:\laragon\bin\php\php-8.2.30-nts-Win32-vs16-x64\php.exe' 'C:\laragon\bin\composer\composer.phar' --version

# 3. Verify Node and npm
node -v
npm.cmd -v

# 4. Verify Frontend build (once scaffolded)
npm.cmd run build
```
