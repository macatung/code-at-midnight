# Empirical Challenge Report — Milestone 1: Foundation & Backend Setup

**Agent**: Challenger 1 (`challenger_m1_1`)  
**Milestone**: `m1_foundation_backend_setup`  
**Timestamp**: 2026-08-17T14:09:30Z  
**Verdict**: **PASS (CERTIFIED ROBUST)**

---

## 1. Executive Summary

Milestone 1 establishes the full-stack Laravel 11/12 + Inertia.js (Vue 3) monolith foundation with SQLite persistence, TailwindCSS dark theme tokens, TypeScript interfaces, and asset bundling.

All empirical challenges were executed directly against the PHP 8.2 runtime (`C:\laragon\bin\php\php-8.2.30-nts-Win32-vs16-x64\php.exe`) and Node/Vite build system.

| Challenge Dimension | Tests Run | Result | Key Evidence |
|---------------------|-----------|--------|--------------|
| **1. Backend Route Handling** | 4 | **PASS** | `GET /` (200), `GET /up` (200), `GET /404` (404), `POST /` (405) |
| **2. Inertia Protocol & Payloads** | 5 | **PASS** | HTML mount with `data-page`, `X-Inertia` JSON, 409 on version mismatch, Partial reload |
| **3. Session & Flash Hydration** | 2 | **PASS** | Lazy evaluation of `success`, `error`, `reference_id` from session |
| **4. Auth Sanitization in Props** | 1 | **PASS** | `auth.user` provides `id`, `name`, `email`; excludes `password`, `remember_token` |
| **5. SQLite Schema & Migrations** | 4 | **PASS** | `migrate:status`, `migrate:rollback`, `migrate:fresh`, CRUD on `sessions`/`cache`/`users` |
| **6. Asset Build & TypeScript** | 2 | **PASS** | `npm run build` (0 err, 7.2s), `npx tsc --noEmit` (0 err) |
| **7. Automated PHPUnit Suite** | 12 tests | **PASS** | 12 passed (65 assertions) in 0.75s |

---

## 2. Adversarial Test Scenarios & Empirical Results

### Scenario A: Inertia Protocol & Asset Cache Busting (Version Mismatch)
- **Assumption Challenged**: Client requests with outdated asset versions must not receive stale props or bypass asset synchronization.
- **Attack / Stress Vector**: Sent GET `/` with `X-Inertia: true` and `X-Inertia-Version: outdated-asset-hash-999`.
- **Observed Behavior**: Returned HTTP `409 Conflict` with header `X-Inertia-Location: http://localhost:8000/`.
- **Verdict**: **PASS** — Inertia asset versioning properly triggers full client-side reload on stale manifest.

### Scenario B: Flash Message Lazy Evaluation & Prop Injection
- **Assumption Challenged**: Session flash values (`success`, `error`, `reference_id`) must only evaluate when requested and accurately inject into Inertia prop bag.
- **Attack / Stress Vector**: Injected unicode messages with emojis and quest IDs into session (`withSession(['success' => 'Tín hiệu đã được truyền đi qua màn đêm! ☕✨', 'error' => 'Phép thuật thất bại: Lỗi kết nối.', 'reference_id' => 'SUMMON-ALPHA-777'])`).
- **Observed Behavior**:
  - `props.flash.success`: `"Tín hiệu đã được truyền đi qua màn đêm! ☕✨"`
  - `props.flash.error`: `"Phép thuật thất bại: Lỗi kết nối."`
  - `props.flash.reference_id`: `"SUMMON-ALPHA-777"`
- **Verdict**: **PASS** — Lazy closures in `HandleInertiaRequests` evaluate and serialize correctly.

### Scenario C: Authenticated User Information Leakage
- **Assumption Challenged**: Shared auth props must never leak hashed passwords, remember tokens, or internal model fields to the client.
- **Attack / Stress Vector**: Authenticated a user instance (`actingAs($user)`) with password hash and requested Inertia props.
- **Observed Behavior**:
  - `props.auth.user`: `{"id": 42, "name": "Midnight Sorcerer", "email": "sorcerer@macatung.dev"}`
  - `password` key: **ABSENT**
  - `remember_token` key: **ABSENT**
- **Verdict**: **PASS** — Sanitized array mapping prevents accidental credential leakage.

### Scenario D: SQLite Schema Freshness, Rollback, & Table Integrity
- **Assumption Challenged**: Migrations must roll back cleanly and recreate all tables without foreign key deadlock or schema divergence in SQLite.
- **Attack / Stress Vector**:
  1. Ran `artisan migrate:rollback` -> 2 migration files rolled back in 58ms.
  2. Ran `artisan migrate:fresh` -> Tables dropped and recreated in 102ms.
  3. Queried SQLite schemas for `users`, `password_reset_tokens`, `sessions`, `cache`, `cache_locks`.
  4. Executed direct write/read inserts into `users`, `sessions`, `cache`.
- **Observed Behavior**: All 6 tables correctly formed with indexes (`sessions.last_activity`, `users.email`). All write/read operations succeeded with zero locks.
- **Verdict**: **PASS**.

### Scenario E: Full HTML Root Template & Blade @vite Directive
- **Assumption Challenged**: Blade root template `resources/views/app.blade.php` must render `@inertiaHead`, `@inertia`, Google fonts, and production Vite assets.
- **Attack / Stress Vector**: Standard HTTP GET `/` request without Inertia headers.
- **Observed Behavior**:
  - `<div id="app" data-page="...">` present.
  - `<title inertia>Macatung Portfolio</title>` present.
  - Preloaded Google Fonts (`JetBrains Mono`, `Plus Jakarta Sans`, `Space Grotesk`, `Syne`) present.
  - Production asset `<link rel="stylesheet" href=".../build/assets/app-*.css">` and `<script type="module" src=".../build/assets/app-*.js">` correctly resolved from `manifest.json`.
- **Verdict**: **PASS**.

---

## 3. Automated Test Execution Summary

Command executed:
```powershell
& "C:\laragon\bin\php\php-8.2.30-nts-Win32-vs16-x64\php.exe" artisan test
```

Output:
```text
   PASS  Tests\Feature\FoundationChallengeTest
  ✓ standard http request renders inertia root template                                                          0.24s  
  ✓ inertia header request returns pure json payload                                                             0.01s  
  ✓ inertia asset version mismatch returns 409 conflict                                                          0.01s  
  ✓ inertia evaluates and shares flash session data                                                              0.01s  
  ✓ inertia fluent assertion with flash session                                                                  0.01s  
  ✓ inertia shares authenticated user safely                                                                     0.01s  
  ✓ inertia partial reload support                                                                               0.01s  
  ✓ health check endpoint                                                                                        0.01s  
  ✓ unregistered route returns 404                                                                               0.01s  
  ✓ invalid http method on home route                                                                            0.24s  

   PASS  Tests\Feature\PageRenderTest
  ✓ home page renders successfully                                                                               0.02s  
  ✓ inertia shares global props                                                                                  0.01s  

  Tests:    12 passed (65 assertions)
  Duration: 0.75s
```

TypeScript Check:
```powershell
npx.cmd tsc --noEmit
# Exit code 0, 0 errors
```

Vite Production Build:
```powershell
npm.cmd run build
# Exit code 0, 762 modules transformed, built in 7.20s
```

---

## 4. Final Verdict

Milestone 1: Foundation & Backend Setup is **FULLY VERIFIED AND APPROVED**.
No regressions, security leaks, or architectural defects were found in the foundation layer.
The workspace is ready for Milestone 2 (Frontend Components Porting & Responsive Polish).
