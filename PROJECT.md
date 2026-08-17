# Project: macatung.dev Portfolio Full-Stack Migration

## Architecture
- **Backend Framework**: Laravel 11/12 running on PHP 8.2+ (`C:\laragon\bin\php\php-8.2.30-nts-Win32-vs16-x64\php.exe`)
- **Frontend Framework**: Vue 3 (Composition API `<script setup lang="ts">`) with Inertia.js (`@inertiajs/vue3`)
- **Build Tooling**: Vite (`@vitejs/plugin-vue`, `laravel-vite-plugin`), TypeScript, PostCSS
- **Styling & Design System**: TailwindCSS v3 (`#06080d` obsidian carbon palette, neon mint `#00f5a0`, talisman gold `#ffd166`, cinnabar red `#ff0054`, phantom purple `#9d4edd`), glassmorphic panels, custom keyframes
- **Database Layer**: SQLite (`database/database.sqlite`) via PHP PDO SQLite driver
- **Interactive Engines**: Zero-dependency Web Audio API procedural sound synthesizer, HTML5 2D Canvas particles loop, interactive Terminal REPL, Talisman Forge with Khai Quang seal animation

---

## Feature Inventory

Every feature discovered during Survey is inventoried and mapped to its respective milestone:

| # | Feature | Description | Milestone | Source |
|---|---------|-------------|-----------|--------|
| 1 | Laravel + Inertia Foundation | Laravel 11/12 structure, Composer setup with PHP 8.2+, Inertia root Blade template, HandleInertiaRequests middleware | M1 | Survey (Env) |
| 2 | Frontend Build & Vite Config | Vite with `@vitejs/plugin-vue`, `laravel-vite-plugin`, TailwindCSS, `lucide-vue-next`, `canvas-confetti` | M1 | Survey (Env) |
| 3 | Types & Static Data Layer | TypeScript interfaces (`portfolio.ts`) and data files (`projectsData.ts`, `skillsData.ts`, `experienceData.ts`, `talismanData.ts`) | M1 | Survey (Assets) |
| 4 | Web Audio Sound Synthesizer | Procedural `SoundEngine` (`soundEffects.ts`): `playHop()`, `playTalisman()`, `playClick()`, `playTerminalKey()`, `playSuccess()` | M2 | Survey (Assets/Spec) |
| 5 | Sound Preference & Toggle | Global mute/unmute toggle in Navbar with pulse indicator and `localStorage` persistence | M2 | Survey (Assets/Spec) |
| 6 | Interactive Jiangshi Mascot | `MacatungMascot.vue` SVG anatomy, 450ms hop physics, squash-stretch, touch/tap handler, speech bubble quotes | M2 | Survey (Assets/Spec) |
| 7 | Mascot 4 Mood States | `normal`, `caffeine`, `sleepy (4 AM)`, `rage (deploy)` with eye SVGs, talisman text, glow, and audio pitch shifts | M2 | Survey (Assets/Spec) |
| 8 | Persistent Hop Ledger | Hop counter with `localStorage` persistence, milestone celebrations (confetti + fanfare on multiples of 10) | M2 | Survey (Assets/Spec) |
| 9 | Talisman & Firefly Particles | `TalismanCanvas.vue` 2D Canvas loop with tech rune paper talismans, fireflies, embers, mouse repulsion, screen wrapping | M2 | Survey (Assets/Spec) |
| 10 | Midnight Terminal CLI | `MidnightTerminal.vue` interactive REPL shell (`macatung-cli`) with history buffer, expand/collapse, copy logs | M2 | Survey (Assets/Spec) |
| 11 | Full Terminal Command Suite | 11 commands: `help`, `whoami`/`bio`, `projects`/`ls`, `skills`, `hop`, `coffee`, `talisman`, `slogan`, `summon`, `sudo rm -rf bugs`, `clear` | M2 | Survey (Assets/Spec) |
| 12 | Developer Talisman Forge | `TalismanGenerator.vue` with 6 preset spells, custom name & wish inputs, 4 color palettes, live preview | M2 | Survey (Assets/Spec) |
| 13 | Khai Quang Blessing Seal | Blessing ritual animation, rotating seal badge (`✓ ĐÃ KHAI QUANG`), talisman arpeggio chime, and confetti burst | M2 | Survey (Assets/Spec) |
| 14 | ASCII Talisman Exporter | Formatted ASCII talisman badge generation and clipboard export | M2 | Survey (Assets/Spec) |
| 15 | Grimoire Project Showcase Grid | `ProjectsSection.vue` with category filters (`all`, `fullstack`, `creative`, `ai-web3`, `tools`), 6 projects, metrics, tech tags | M2 | Survey (Assets/Spec) |
| 16 | Project Modal Dialog | `ProjectModal.vue` with architecture highlights, midnight lore, Escape key & backdrop dismiss, body scroll lock | M2 | Survey (Assets/Spec) |
| 17 | Midnight Clock & Live Status | `MidnightClock.vue` real-time digital clock (`HH:mm:ss`), Midnight Mode vs Daylight Prep badge, caffeine calculator, latency ping | M2 | Survey (Assets/Spec) |
| 18 | About & Developer Manifesto | `AboutSection.vue` 4 stats cards, 3-tab manifesto panel (Triết Lý 00:00 AM, Day vs Night, Khắc Bùa Chất Lượng) | M2 | Survey (Assets/Spec) |
| 19 | Skills & Tech Rune Arsenal | `SkillsSection.vue` 4 categories, 18 skills with animated proficiency bars (82-100%), runes, verification pledge | M2 | Survey (Assets/Spec) |
| 20 | Experience & Midnight Chronicles | `ExperienceSection.vue` chronological timeline with achievement bullets, tech stack pills, Midnight Quest Lore | M2 | Survey (Assets/Spec) |
| 21 | Hero, Navbar & Footer | `HeroSection.vue`, `Navbar.vue` with mobile drawer, `Footer.vue` with Hop-to-Top and Easter eggs | M2 | Survey (Assets/Spec) |
| 22 | Responsive Layout & Anti-Collision | Seamless responsive layouts for 360px-480px (mobile), 768px-1024px (tablet), 1440px+ (desktop), minimum 44x44px tap targets | M2 | Survey (Spec) |
| 23 | SQLite Database & Contact Schema | SQLite database setup (`database/database.sqlite`) and `contact_submissions` table migration | M3 | Survey (Env/Spec) |
| 24 | Laravel Contact Controller & Validation | `ContactController@store` with `ContactRequest` validation rules, Eloquent `ContactSubmission` model | M3 | Survey (Spec) |
| 25 | Summoning Altar Inertia Form | `ContactSection.vue` with `@inertiajs/vue3` `useForm`, validation error feedback, sound feedback, confetti, and Inertia flash response | M3 | Survey (Spec) |
| 26 | E2E Test Suite (Tiers 1-4) | Comprehensive opaque-box test suite verifying all 25 features across unit, integration, and E2E tiers | E2E Track / M4 | Survey (All) |
| 27 | Adversarial Hardening (Tier 5) | White-box edge cases, stress testing, audio context handling, memory leak auditing | M4 | Survey (Spec) |

---

## Milestones

| # | Milestone | Scope | Dependencies | Status |
|---|-----------|-------|-------------|--------|
| **E2E** | `e2e_testing_track` | Requirement-driven Opaque-box Test Suite (Tiers 1-4), Test Harness & `TEST_READY.md` publication | None | DONE |
| **M1** | `m1_foundation_backend_setup` | Laravel 11/12 setup, PHP 8.2+ Composer config, SQLite DB init, Vite Vue 3 tooling, Inertia root template & middleware, TypeScript types & data | None | DONE |
| **M2** | `m2_frontend_components_responsive` | Vue 3 Modular Component Porting (Mascot, Canvas, Audio, Terminal, Talisman, Projects, Clock, About, Skills, Experience, Hero, Nav, Footer) + Responsive & Anti-Collision Polish (360px-1440px) | M1 | DONE |
| **M3** | `m3_backend_altar_integration` | Backend Contact Migration, Eloquent Model, FormRequest Validation, `ContactController@store`, Inertia Flash Response, and `ContactSection.vue` Form Integration | M1, M2 | DONE |
| **M4** | `m4_final_verification_adversarial_hardening` | Phase 1: 100% E2E Test Suite Pass (Tiers 1-4). Phase 2: Adversarial Coverage Hardening (Tier 5) with Challenger and Forensic Audit | M3, E2E | DONE |

---

## Interface Contracts

### 1. Backend Controller ↔ Frontend Inertia Contract
- **Endpoint**: `POST /contact` (named route: `contact.store`)
- **Request Payload (JSON / Form)**:
  ```json
  {
    "name": "string (required, max:255)",
    "email": "string (required, email, max:255)",
    "project_type": "string (required, in: ['Full-Stack Web App', 'Creative UI/UX & Web Audio', 'High-Throughput Microservice', 'AI Agents & Automation', 'Tech Lead / Architecture Consulting', 'Other Quest'])",
    "coffee_offering": "string (required, max:255)",
    "message": "string (required, min:10, max:5000)"
  }
  ```
- **Response**:
  - Success (200 / 302): Redirect back with Inertia flash:
    ```json
    {
      "flash": {
        "success": "Tín hiệu đã được truyền đi qua màn đêm! Ma Cà Tưng sẽ hồi đáp trong thời gian sớm nhất. ☕✨",
        "reference_id": "SUMMON-XXXX"
      }
    }
    ```
  - Validation Failure (422): Inertia standard error bag:
    ```json
    {
      "errors": {
        "name": "The name field is required.",
        "email": "The email field must be a valid email address.",
        "message": "The message must be at least 10 characters."
      }
    }
    ```

### 2. Frontend Sound Engine Interface (`soundEffects.ts`)
```ts
export interface ISoundEngine {
  isMuted(): boolean;
  toggleMute(): boolean;
  playHop(intensity?: number): void;
  playTalisman(): void;
  playClick(): void;
  playTerminalKey(): void;
  playSuccess(): void;
}
```

### 3. Mascot Component Emits & Props (`MacatungMascot.vue`)
```ts
export interface MascotProps {
  size?: 'sm' | 'md' | 'lg' | 'hero';
  showControls?: boolean;
}
export interface MascotEmits {
  (e: 'hop-count-change', count: number): void;
}
```

---

## Code Layout

```
d:/Work/macatung/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── HomeController.php
│   │   │   └── ContactController.php
│   │   ├── Middleware/
│   │   │   └── HandleInertiaRequests.php
│   │   └── Requests/
│   │       └── ContactRequest.php
│   └── Models/
│       └── ContactSubmission.php
├── bootstrap/
│   └── app.php
├── config/
│   ├── app.php
│   └── database.php
├── database/
│   ├── database.sqlite
│   └── migrations/
│       └── 2026_08_17_000001_create_contact_submissions_table.php
├── resources/
│   ├── views/
│   │   └── app.blade.php
│   ├── css/
│   │   └── app.css
│   └── js/
│       ├── app.ts
│       ├── types/
│       │   └── portfolio.ts
│       ├── audio/
│       │   └── soundEffects.ts
│       ├── data/
│       │   ├── experienceData.ts
│       │   ├── projectsData.ts
│       │   ├── skillsData.ts
│       │   └── talismanData.ts
│       ├── Components/
│       │   ├── hero/HeroSection.vue
│       │   ├── about/AboutSection.vue
│       │   ├── projects/
│       │   │   ├── ProjectsSection.vue
│       │   │   └── ProjectModal.vue
│       │   ├── skills/SkillsSection.vue
│       │   ├── experience/ExperienceSection.vue
│       │   ├── terminal/MidnightTerminal.vue
│       │   ├── talisman/TalismanGenerator.vue
│       │   ├── contact/ContactSection.vue
│       │   ├── mascot/
│       │   │   ├── MacatungMascot.vue
│       │   │   ├── TalismanCanvas.vue
│       │   │   └── MidnightClock.vue
│       │   ├── layout/
│       │   │   ├── Navbar.vue
│       │   │   ├── Footer.vue
│       │   │   └── SoundToggle.vue
│       │   └── ui/Icons.vue
│       └── Pages/
│           └── Home.vue
├── routes/
│   └── web.php
├── tests/
│   ├── Feature/
│   │   ├── ContactSubmissionTest.php
│   │   └── PageRenderTest.php
│   └── E2E/
│       └── e2e_suite.test.ts (or test runners)
├── composer.json
├── package.json
├── vite.config.ts
└── tailwind.config.js
```
