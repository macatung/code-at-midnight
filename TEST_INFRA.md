# E2E Test Infra: macatung.dev Full-Stack Migration

## Test Philosophy
- **Requirement-Driven & Opaque-Box**: Derived strictly from `ORIGINAL_REQUEST.md` and user-facing specifications in `PROJECT.md`, without coupling to internal private state.
- **Progressive Testability**: Verification mechanisms do not require features more complex than what is being tested. Earliest tiers provide rapid pass/fail signals.
- **Interface-Compatible**: Uses official entry points (HTTP routes, Inertia JSON payloads, TypeScript interfaces, DOM events, Web Audio contracts).
- **Zero Flakiness**: Deterministic mocks for timers, Web Audio AudioContext, HTML5 Canvas 2D context, and Inertia visits.

---

## Feature Inventory (25 Features)

| # | Feature Key | Feature Name | Description | Source |
|---|-------------|--------------|-------------|--------|
| 1 | `F01_FOUNDATION` | Laravel + Inertia Foundation | Laravel 11/12 bootstrap, Inertia root Blade template, HandleInertiaRequests middleware, route routing | `PROJECT.md § Feature 1` |
| 2 | `F02_VITE_BUILD` | Frontend Build & Vite Config | Vite bundle build, Vue 3 Composition API, TailwindCSS styling, Lucide icons, Canvas Confetti | `PROJECT.md § Feature 2` |
| 3 | `F03_TYPES_DATA` | Types & Static Data Layer | TypeScript models (`portfolio.ts`), static datasets for projects, skills, experience, talismans | `PROJECT.md § Feature 3` |
| 4 | `F04_SOUND_ENGINE` | Web Audio Sound Synthesizer | Procedural `SoundEngine` (`soundEffects.ts`): `playHop()`, `playTalisman()`, `playClick()`, `playTerminalKey()`, `playSuccess()` | `PROJECT.md § Feature 4` |
| 5 | `F05_SOUND_TOGGLE` | Sound Preference & Mute Toggle | Global mute/unmute state in Navbar, audio indicator animations, and `localStorage` persistence | `PROJECT.md § Feature 5` |
| 6 | `F06_MASCOT_PHYSICS` | Interactive Jiangshi Mascot | `MacatungMascot.vue` SVG anatomy, 450ms hop physics, squash-stretch, touch/tap handler, speech quotes | `PROJECT.md § Feature 6` |
| 7 | `F07_MASCOT_MOODS` | Mascot 4 Mood States | `normal`, `caffeine`, `sleepy (4 AM)`, `rage (deploy)` with eye SVGs, talisman text, glow, and pitch shifts | `PROJECT.md § Feature 7` |
| 8 | `F08_HOP_LEDGER` | Persistent Hop Ledger | Hop counter with `localStorage` persistence, milestone celebrations (confetti + fanfare on multiples of 10) | `PROJECT.md § Feature 8` |
| 9 | `F09_TALISMAN_CANVAS`| Talisman & Firefly Particles | `TalismanCanvas.vue` 2D Canvas loop with tech rune paper talismans, fireflies, embers, mouse repulsion, screen wrapping | `PROJECT.md § Feature 9` |
| 10 | `F10_TERMINAL_REPL` | Midnight Terminal CLI | `MidnightTerminal.vue` interactive REPL shell (`macatung-cli`) with history buffer, expand/collapse, copy logs | `PROJECT.md § Feature 10` |
| 11 | `F11_TERMINAL_CMDS` | Full Terminal Command Suite | 11 commands: `help`, `whoami`/`bio`, `projects`/`ls`, `skills`, `hop`, `coffee`, `talisman`, `slogan`, `summon`, `sudo rm -rf bugs`, `clear` | `PROJECT.md § Feature 11` |
| 12 | `F12_TALISMAN_FORGE`| Developer Talisman Forge | `TalismanGenerator.vue` with 6 preset spells, custom name & wish inputs, 4 color palettes, live preview | `PROJECT.md § Feature 12` |
| 13 | `F13_KHAI_QUANG` | Khai Quang Blessing Seal | Blessing ritual animation, rotating seal badge (`✓ ĐÃ KHAI QUANG`), talisman arpeggio chime, confetti burst | `PROJECT.md § Feature 13` |
| 14 | `F14_ASCII_EXPORT` | ASCII Talisman Exporter | Formatted ASCII talisman badge generation and clipboard export | `PROJECT.md § Feature 14` |
| 15 | `F15_GRIMOIRE_GRID` | Grimoire Project Showcase | `ProjectsSection.vue` with category filters (`all`, `fullstack`, `creative`, `ai-web3`, `tools`), 6 projects, metrics, tech tags | `PROJECT.md § Feature 15` |
| 16 | `F16_PROJECT_MODAL` | Project Modal Dialog | `ProjectModal.vue` with architecture highlights, midnight lore, Escape key & backdrop dismiss, body scroll lock | `PROJECT.md § Feature 16` |
| 17 | `F17_MIDNIGHT_CLOCK`| Midnight Clock & Live Status | `MidnightClock.vue` real-time digital clock (`HH:mm:ss`), Midnight Mode vs Daylight Prep badge, caffeine calculator, latency ping | `PROJECT.md § Feature 17` |
| 18 | `F18_ABOUT_MANIFESTO`| About & Developer Manifesto | `AboutSection.vue` 4 stats cards, 3-tab manifesto panel (Triết Lý 00:00 AM, Day vs Night, Khắc Bùa Chất Lượng) | `PROJECT.md § Feature 18` |
| 19 | `F19_SKILLS_ARSENAL`| Skills & Tech Rune Arsenal | `SkillsSection.vue` 4 categories, 18 skills with animated proficiency bars (82-100%), runes, verification pledge | `PROJECT.md § Feature 19` |
| 20 | `F20_EXPERIENCE_LORE`| Experience & Chronicles | `ExperienceSection.vue` chronological timeline with achievement bullets, tech stack pills, Midnight Quest Lore | `PROJECT.md § Feature 20` |
| 21 | `F21_NAV_HERO_FOOTER`| Hero, Navbar & Footer | `HeroSection.vue`, `Navbar.vue` with mobile drawer, `Footer.vue` with Hop-to-Top and Easter eggs | `PROJECT.md § Feature 21` |
| 22 | `F22_RESPONSIVE_ANTI`| Responsive & Anti-Collision | Responsive layouts for 360px-480px (mobile), 768px-1024px (tablet), 1440px+ (desktop), minimum 44x44px tap targets | `PROJECT.md § Feature 22` |
| 23 | `F23_DB_SCHEMA` | SQLite Database & Schema | SQLite database setup (`database/database.sqlite`) and `contact_submissions` table migration | `PROJECT.md § Feature 23` |
| 24 | `F24_BACKEND_CTRL` | Contact Controller & Validation | `ContactController@store` with `ContactRequest` validation rules, Eloquent `ContactSubmission` model | `PROJECT.md § Feature 24` |
| 25 | `F25_SUMMON_ALTAR` | Summoning Altar Inertia Form | `ContactSection.vue` with `@inertiajs/vue3` `useForm`, validation error feedback, sound feedback, confetti, and Inertia flash response | `PROJECT.md § Feature 25` |

---

## 4-Tier Test Architecture & Coverage Matrix

### Tier 1: Feature Coverage (>=5 test cases per feature, Total >= 125 cases)
Equivalence class representative inputs for every feature in isolation:
- `T1_F01`: 5 cases (Root view Blade renders, Inertia page component prop, HandleInertiaRequests shared data, asset paths, 200 HTTP status)
- `T1_F02`: 5 cases (Vite config plugins, Vue SFC compile, TailwindCSS class utility resolution, Lucide icon imports, canvas-confetti import)
- `T1_F03`: 5 cases (Projects array validity, Skills array validity, Experience timeline array validity, Talisman presets array validity, TypeScript interface conformance)
- `T1_F04`: 5 cases (`playHop` frequency sweep, `playTalisman` chord triad, `playClick` transient pop, `playTerminalKey` noise burst, `playSuccess` arpeggio)
- `T1_F05`: 5 cases (Initial mute state read, toggleMute flipping state, localStorage write, localStorage sync on reload, mute suppression of audio playback)
- `T1_F06`: 5 cases (Mascot SVG rendering, 450ms hop duration trigger, squash-stretch CSS class application, touch tap event handler, speech quote rotation)
- `T1_F07`: 5 cases (Mood normal render, Mood caffeine eye glow, Mood sleepy closed eyes, Mood rage red aura, mood sound pitch shift multiplier)
- `T1_F08`: 5 cases (Hop counter increments on click, localStorage persistence of count, Milestone 10 triggers confetti, Milestone 50 trigger, non-milestone silent hop)
- `T1_F09`: 5 cases (Canvas initialization, particle array generation, Talisman rune rendering, Firefly glow rendering, screen edge coordinate wrap)
- `T1_F10`: 5 cases (Terminal REPL container render, prompt display `macatung:~$`, command input change, command history array append, copy logs action)
- `T1_F11`: 11 cases (Execute `help`, `whoami`, `projects`, `skills`, `hop`, `coffee`, `talisman`, `slogan`, `summon`, `sudo rm -rf bugs`, `clear`)
- `T1_F12`: 5 cases (Preset spell selection, developer name input binding, custom wish input binding, color palette theme selection, real-time preview card update)
- `T1_F13`: 5 cases (Khai Quang blessing button click, 800ms seal stamp animation state, rotating badge display, talisman audio trigger, confetti burst dispatch)
- `T1_F14`: 5 cases (ASCII talisman string generator format, custom name injection into ASCII, wish injection into ASCII, border runes integrity, clipboard copy action)
- `T1_F15`: 5 cases (Grimoire grid 6 projects render, Category filter 'all', Category filter 'fullstack', Category filter 'creative', Category filter 'tools')
- `T1_F16`: 5 cases (Modal open on project card click, Modal content matching selected project, Modal close on backdrop click, Modal close on ESC key, Body scroll lock `overflow-hidden`)
- `T1_F17`: 5 cases (Midnight clock digital string format `HH:mm:ss`, Daylight vs Midnight Mode state, Caffeine level computation, Latency ping simulation, live tick interval)
- `T1_F18`: 5 cases (About stats 4 cards render, Manifesto tab 1 switch, Manifesto tab 2 switch, Manifesto tab 3 switch, Developer origin bio content)
- `T1_F19`: 5 cases (Skills 4 categories render, 18 skill items render, Proficiency percentage bar width, Tech rune symbol render, Pledge manifesto card)
- `T1_F20`: 5 cases (Experience timeline render, chronological sorting, achievement bullet points, tech stack tags render, Quest lore narratives)
- `T1_F21`: 5 cases (Hero headline render, Navbar links render, Mobile drawer toggle, Footer copyright & links, Hop-to-Top button scroll trigger)
- `T1_F22`: 5 cases (Viewport 360px layout check, Viewport 390px layout check, Viewport 768px layout check, Viewport 1440px layout check, minimum 44x44px interactive tap target sizes)
- `T1_F23`: 5 cases (SQLite database connection, `contact_submissions` table schema, migration up execution, columns structure check, migration rollback/down)
- `T1_F24`: 5 cases (`ContactController@store` valid payload 200/302, validation failure 422, DB record creation, reference ID generation, Inertia flash payload)
- `T1_F25`: 5 cases (Inertia `useForm` initialization, field binding, client submit event dispatch, validation error display, success banner & reset)

### Tier 2: Boundary & Corner Cases (>=5 test cases per feature, Total >= 125 cases)
Testing at extremes, limits, invalid inputs, edge states, and error handling:
- `T2_F01` - `T2_F25`: >=5 boundary cases each (empty inputs, max length bounds, rapid click spamming, rapid audio context resume, zero latency, midnight 00:00:00 wrap, 1000+ hops overflow, special characters in terminal/talisman, long email strings, SQL injection characters in form, viewport resize spam, audio context unlock on first user gesture, etc.)

### Tier 3: Cross-Feature Combinations (Pairwise Interaction, Total >= 25 cases)
Testing integration points where features share state, audio, DOM, or events:
1. `T3_01`: Mascot Hop (`F06`) + Hop Ledger (`F08`) + Audio Synth (`F04`)
2. `T3_02`: Mascot Mood Change (`F07`) + Audio Synth Pitch Shift (`F04`) + Mascot Speech (`F06`)
3. `T3_03`: Hop Ledger Milestone 10 (`F08`) + Mascot Hop (`F06`) + Confetti Burst (`F02`) + Fanfare Sound (`F04`)
4. `T3_04`: Talisman Forge Preset Selection (`F12`) + Khai Quang Blessing Seal (`F13`) + Talisman Audio (`F04`)
5. `T3_05`: Talisman Forge Custom Wish (`F12`) + ASCII Exporter (`F14`) + Clipboard Copy (`F14`)
6. `T3_06`: Terminal Command `hop` (`F11`) + Mascot Hop Trigger (`F06`) + Hop Ledger Increment (`F08`)
7. `T3_07`: Terminal Command `coffee` (`F11`) + Mascot Mood Change to `caffeine` (`F07`) + Sound Pitch Shift (`F04`)
8. `T3_08`: Terminal Command `talisman` (`F11`) + Talisman Forge Random Preset (`F12`) + Output Log (`F10`)
9. `T3_09`: Terminal Command `projects` (`F11`) + Grimoire Project Data (`F03`/`F15`)
10. `T3_10`: Terminal Command `summon` (`F11`) + Scroll to Summoning Altar (`F25`)
11. `T3_11`: Terminal Command `sudo rm -rf bugs` (`F11`) + Terminal Clear + Mascot Mood `rage` (`F07`)
12. `T3_12`: Grimoire Category Filter (`F15`) + Project Card Click (`F15`) + Project Modal Open (`F16`)
13. `T3_13`: Project Modal Open (`F16`) + Body Scroll Lock (`F16`) + Modal ESC Dismiss (`F16`) + Body Scroll Restore
14. `T3_14`: Sound Mute Toggle in Navbar (`F05`) + Mascot Hop Audio Playback Check (`F04`)
15. `T3_15`: Sound Mute Toggle in Navbar (`F05`) + Khai Quang Seal Audio Playback Check (`F04`)
16. `T3_16`: Sound Mute Toggle in Navbar (`F05`) + Terminal Keystroke Audio Playback Check (`F04`)
17. `T3_17`: Midnight Clock 00:00-05:00 Time (`F17`) + Mascot Mood Auto-Select `sleepy` or `midnight` (`F07`)
18. `T3_18`: Midnight Clock Caffeine Calculator (`F17`) + Terminal `coffee` Command (`F11`)
19. `T3_19`: Summoning Altar Form Submit (`F25`) + Backend Controller (`F24`) + Database Persistence (`F23`)
20. `T3_20`: Summoning Altar Form Validation Error (`F24`/`F25`) + UI Error Highlights + No Database Insert (`F23`)
21. `T3_21`: Summoning Altar Success Flash (`F24`/`F25`) + Success Sound (`F04`) + Confetti Burst (`F02`) + Form Reset
22. `T3_22`: Mobile Viewport 390px (`F22`) + Navbar Mobile Drawer Toggle (`F21`) + Section Navigation
23. `T3_23`: Mobile Viewport 360px (`F22`) + Terminal Expand/Collapse (`F10`) + Tap Target Bounds
24. `T3_24`: Talisman Canvas Particle Mouse Repulsion (`F09`) + Canvas Resize Handler (`F22`)
25. `T3_25`: Footer Hop-to-Top Button (`F21`) + Mascot Hop Trigger (`F06`) + Window Scroll to Top

### Tier 4: Real-World Application Scenarios (>=12 End-to-End User Workflows)
Complete, multi-step end-to-end user journeys simulating real visitor interactions:
1. `T4_01_VISITOR_FIRST_IMPRESSION`: User loads home page → verifies obsidian aesthetic, hero typography, live midnight clock, canvas particles floating.
2. `T4_02_MASCOT_PLAYFUL_JOURNEY`: User interacts with Jiangshi mascot → taps 10 times → watches hop physics → observes quote changes → triggers 10th hop milestone celebration with confetti and chime.
3. `T4_03_MASCOT_MOOD_CYCLE`: User explores all mascot moods (normal → caffeine → sleepy → rage) via mascot controls and terminal commands, checking eye SVGs, glow colors, and audio pitch variations.
4. `T4_04_TERMINAL_POWER_USER`: Developer opens terminal CLI → expands shell → runs `help`, `whoami`, `skills`, `projects`, `hop`, `coffee`, `talisman`, `slogan` → copies output logs to clipboard → runs `clear`.
5. `T4_05_TALISMAN_FORGING_RITUAL`: Developer crafts a custom bug-free talisman → chooses "Bùa Fix Bug" preset → customizes name "LeadDev" and wish "Deploy êm đềm" → triggers Khai Quang blessing seal → hears talisman arpeggio → copies ASCII talisman badge.
6. `T4_06_PROJECT_GRIMOIRE_EXPLORATION`: Recruiter browses portfolio projects → filters by 'fullstack' and 'ai-web3' → opens project modal for "Ma Cà Tưng Portfolio" → reads architecture highlights and midnight lore → closes via ESC key.
7. `T4_07_DEVELOPER_MANIFESTO_DEEP_DIVE`: Visitor reviews About & Philosophy → examines 4 stats cards → reads through all 3 manifesto tabs (Triết Lý 00:00 AM, Day vs Night, Khắc Bùa Chất Lượng) → verifies typography readability and smooth tabs.
8. `T4_08_SKILLS_AND_EXPERIENCE_INSPECTION`: Hiring manager inspects skills arsenal (18 skills across 4 categories) with animated proficiency levels → inspects chronological experience timeline and Midnight Quest lore narratives.
9. `T4_09_SUMMONING_ALTAR_CONTACT_FLOW`: Client fills Summoning Altar contact form with name, email, project type "Creative UI/UX & Web Audio", coffee offering "Cà phê muối", and detailed message → submits form → receives Inertia flash confirmation with reference ID and celebratory confetti.
10. `T4_10_SUMMONING_ALTAR_ERROR_RECOVERY`: Client submits invalid/empty form → receives validation errors for required fields → fixes each error with valid data → re-submits successfully and verifies database state.
11. `T4_11_MOBILE_RESPONSIVE_WALKTHROUGH`: Mobile user on 390px iPhone viewport → navigates via mobile drawer menu → taps mascot with touch events → uses terminal and talisman generator without clipping or text overlap → verifies all buttons have >=44px tap targets.
12. `T4_12_ACCESSIBILITY_AND_AUDIO_CONTROL`: Sound-sensitive visitor uses global mute button in Navbar → verifies sound engine is muted across mascot hops, seal blessings, and terminal keystrokes → re-enables sound and verifies audio restores cleanly.

---

## Test Directory Structure & Organization

```
d:/Work/macatung/tests/
├── Unit/
│   ├── AudioSynthTest.test.ts          # F04, F05 Web Audio synthesizer & mute tests
│   ├── MascotPhysicsTest.test.ts       # F06, F07, F08 Mascot anatomy, physics, moods, hop ledger
│   ├── TalismanCanvasTest.test.ts      # F09 Canvas particles, math, mouse repulsion, wrapping
│   ├── TalismanForgeTest.test.ts       # F12, F13, F14 Preset generation, Khai Quang, ASCII export
│   ├── TerminalCliTest.test.ts         # F10, F11 Terminal REPL & 11 commands execution
│   ├── PortfolioDataTest.test.ts       # F03 Static datasets & types validation
│   └── MidnightClockTest.test.ts       # F17 Clock math, modes, caffeine calc, latency
├── Components/
│   ├── GrimoireProjectsTest.test.ts    # F15, F16 Grimoire filter, project cards, modal dialog
│   ├── AboutManifestoTest.test.ts      # F18 About stats, 3 manifesto tabs, typography
│   ├── SkillsArsenalTest.test.ts       # F19 Skills categories, proficiency bars, runes
│   ├── ExperienceLoreTest.test.ts      # F20 Experience timeline, quest lore narratives
│   ├── LayoutNavFooterTest.test.ts     # F21 Navbar, mobile drawer, footer, hop-to-top
│   └── ResponsiveLayoutTest.test.ts    # F22 Viewport checks (360, 390, 768, 1440px), tap targets
├── Integration/
│   ├── CrossFeaturePairwiseTest.test.ts # Tier 3: All 25 Cross-feature interaction test cases
│   └── SummoningAltarInertiaTest.test.ts# F24, F25 Contact form Inertia handling & validation
├── Feature/
│   ├── ContactSubmissionTest.php       # F23, F24 Laravel Contact controller, request validation, DB
│   └── PageRenderTest.php              # F01 Laravel root route, Inertia view, Blade layout
├── E2E/
│   ├── Scenarios_01_to_06.test.ts      # Tier 4: User workflows 1 through 6
│   └── Scenarios_07_to_12.test.ts      # Tier 4: User workflows 7 through 12
├── Harness/
│   ├── test_runner.ts                  # Test runner & reporting harness
│   └── mock_helpers.ts                 # DOM, Web Audio, Canvas, and Inertia test doubles
└── run_all_tests.js                    # Unified CLI test runner executable
```

---

## Test Execution Commands & Pass Semantics
- **Run Full Frontend & E2E Test Suite**: `node tests/run_all_tests.js` or `npx vitest run`
- **Run Backend PHPUnit Tests**: `php artisan test` or `C:\laragon\bin\php\php-8.2.30-nts-Win32-vs16-x64\php.exe artisan test`
- **Pass Criteria**: 100% of test cases pass with exit code 0, 0 failures, 0 skipped, and complete coverage across all 4 tiers.
