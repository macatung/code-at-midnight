# Codebase & Asset Survey Report: macatung.dev Full-Stack Migration

## 1. Observation

A comprehensive inspection of the existing codebase at `d:/Work/macatung` was conducted. Below are the verified observations, file paths, component structures, logic details, and environment specifics.

### 1.1 Project Structure & Inventory

```
d:/Work/macatung/
├── ORIGINAL_REQUEST.md            # Full migration requirements and acceptance criteria
├── README.md                      # Feature descriptions and quick-start docs
├── package.json                   # Current React 19 dependencies & scripts
├── tailwind.config.js             # Tailwind theme (midnight palette, custom animations)
├── postcss.config.js              # PostCSS config (tailwindcss, autoprefixer)
├── index.html                     # HTML shell, fonts (Plus Jakarta Sans, Space Grotesk, Syne, JetBrains Mono)
├── public/
│   ├── favicon.svg                # Mascot SVG favicon
│   └── icons.svg                  # SVG symbol sprite
└── src/
    ├── App.tsx                    # Main layout and section orchestrator
    ├── index.css                  # Custom CSS (glass panels, scrollbar, text glow, talisman styling)
    ├── main.tsx                   # React entrypoint
    ├── types/
    │   └── portfolio.ts           # TypeScript interfaces (Project, SkillCategory, ExperienceItem, DeveloperStat, TalismanPreset)
    ├── audio/
    │   └── soundEffects.ts        # Zero-dependency Web Audio API procedural synthesizer (SoundEngine singleton)
    ├── data/
    │   ├── experienceData.ts      # 4 career positions + 4 developer metrics stats
    │   ├── projectsData.ts        # 6 full-stack, creative, AI & systems projects
    │   ├── skillsData.ts          # 4 skill categories with 18 technical skills
    │   └── talismanData.ts        # 6 preset talisman spells with code snippets
    └── components/
        ├── hero/HeroSection.tsx
        ├── about/AboutSection.tsx
        ├── projects/
        │   ├── ProjectsSection.tsx
        │   └── ProjectModal.tsx
        ├── skills/SkillsSection.tsx
        ├── experience/ExperienceSection.tsx
        ├── terminal/MidnightTerminal.tsx
        ├── talisman/TalismanGenerator.tsx
        ├── contact/ContactSection.tsx
        ├── mascot/
        │   ├── MacatungMascot.tsx
        │   ├── TalismanCanvas.tsx
        │   └── MidnightClock.tsx
        ├── layout/
        │   ├── Navbar.tsx
        │   ├── Footer.tsx
        │   └── SoundToggle.tsx
        └── ui/Icons.tsx
```

---

### 1.2 Component Logic & Asset Breakdown

#### A. Interactive Mascot (`MacatungMascot.tsx` -> `MacatungMascot.vue`)
- **SVG Anatomy**:
  - Jiangshi Hat: Brim ellipse (`rx=52, ry=14`), crown path, golden jewel (`cx=120, cy=50, r=6`), cyber antenna with jade tip (`#00f5a0`).
  - Gaming Headphones: Headband arc (`M68 95 C68 62, 172 62, 172 95`) with ear cups (`rect x=64, y=80` and `x=164, y=80`).
  - Head & Face: Radial ghost skin gradient (`#d8f3dc` -> `#b7e4c7` -> `#95d5b2`), blushing cheeks (`#ff4d6d`, opacity 0.35), mouth with white vampire fangs.
  - Interactive Eyes:
    - `normal`: Glowing cyan `#00f5d4` with white pupils.
    - `caffeine`: Golden yellow `#ffd166`.
    - `sleepy`: Arc paths (`#8b5cf6`).
    - `rage`: Angled triangular polygons (`#ff4d6d`).
  - Forehead Talisman: Yellow gradient paper (`x=105, y=55, w=30, h=62`), red code seal `<circle>` with `</>`, tech line decorations, dynamic mood text (`0 BUG`, `COFFEE`, `4:00 AM`, `DEPLOY`), circuit runes.
  - Robe & Arms: Outstretched Jiangshi arms with animated flutter (`animate-talisman-flutter`), robotic claw gloves, dark hoodie gradient (`#111724` -> `#060910`), chest rune hexagon with `{ }`.
  - Feet & Shadow: Hopping feet ellipses (`#0f172a`, stroke `#00f5a0`) and dynamic ground blur shadow that contracts and fades during hops.
- **Physics & State Management**:
  - `hopCount`: Persisted to `localStorage` key `'macatung_hop_counter'`.
  - `isJumping`: 450ms hop state (`-translate-y-12 scale-y-110` with easing).
  - `mood`: 4 states (`normal`, `caffeine`, `sleepy`, `rage`), each updating eye graphics, audio pitch, speech bubble messages, and ambient halo color.
  - Milestone Confetti: Triggers `canvas-confetti` and `sound.playSuccess()` on multiples of 10 hops.
  - Speech Bubble: Dynamic random quotes in Vietnamese/English celebrating midnight developer culture.

#### B. Floating Talisman & Firefly Particles Canvas (`TalismanCanvas.tsx` -> `TalismanCanvas.vue`)
- **Canvas Engine**:
  - Fullscreen fixed element (`fixed inset-0 pointer-events-none z-0`, opacity 0.6).
  - Particle count: `Math.min(24, Math.floor(window.innerWidth / 50))`.
  - Particle types:
    1. `talisman`: Yellow rectangular paper (`#eed060`, red border `#b91c1c`, red `<circle>` stamp at top, tech runes: `'0 BUG'`, `'</>'`, `'⚡'`, `'DEV'`, `'☕'`, `'HOP'`, `'12AM'`), rotation physics, vertical drift.
    2. `firefly` / `ember`: Radial gradient glowing orbs (`#00f5a0`, `#00d2ff`, `#ff4d6d`), upward drift, sinusoidal pulsing opacity.
  - Interactive mouse repulsion: When cursor is within 100px of a particle, applies inverse distance repulsion velocity.
  - Seamless screen wrapping on all 4 canvas boundaries.

#### C. Web Audio Procedural Synthesizer (`soundEffects.ts`)
- **Zero HTTP Asset Synthesizer**:
  - Uses native `AudioContext` / `webkitAudioContext`.
  - Auto-resumes suspended audio contexts on user interaction.
  - `localStorage` key `'macatung_sound_muted'`.
  - Synthesizer Methods:
    - `playHop(intensity)`: Sine oscillator, base 220Hz + (intensity * 40), exponential ramp to 2.8x base in 0.12s, decays to 0.8x in 0.25s.
    - `playTalisman()`: Arpeggiated mystic chime using triangle oscillators on D5 (587.33Hz), A5 (880Hz), D6 (1174.66Hz), A6 (1760Hz) staggered by 50ms.
    - `playClick()`: Sine wave 800Hz ramping to 300Hz over 40ms.
    - `playTerminalKey()`: Triangle oscillator with randomized frequency 420–500Hz for 30ms (mechanical switch sound).
    - `playSuccess()`: Major chord fanfare (C5 523.25Hz, E5 659.25Hz, G5 783.99Hz, C6 1046.50Hz) staggered by 80ms over 600ms decay.

#### D. Midnight Terminal CLI (`MidnightTerminal.tsx` -> `MidnightTerminal.vue`)
- **REPL Interface**:
  - Shell header: `macatung@midnight-sanctuary: ~ (zsh)`.
  - History buffer with Up/Down arrow navigation.
  - Output copy to clipboard with feedback icon.
  - Window expand/collapse toggle.
  - Supported Command Suite:
    - `help`: Lists all midnight commands.
    - `whoami` / `bio`: Displays identity, domain, focus, fuel stats.
    - `projects` / `ls` / `ls projects`: Lists Grimoire artifacts.
    - `skills`: Tech stack matrix.
    - `hop`: Triggers audio synthesis and increments hop ledger.
    - `coffee`: ASCII art Vietnamese Robusta coffee mug.
    - `talisman`: ASCII art Zero-Bug code talisman.
    - `slogan`: Echoes "Code at midnight" motto.
    - `summon` / `contact`: Direct transmission channels.
    - `sudo rm -rf bugs` / `rm -rf bugs`: Confetti explosion and bug purging protocol.
    - `clear`: Clears the screen buffer.
  - Quick-click chips for single-tap command execution.

#### E. Talisman Forge / Generator (`TalismanGenerator.tsx` -> `TalismanGenerator.vue`)
- **Interactive Generator**:
  - Preset spells: `BÙA CODE 0 BUG`, `BÙA DEPLOY THỨ 6`, `BÙA TĂNG LƯƠNG X2`, `BÙA 0 CONFLICT`, `BÙA FIX PROD NỬA ĐÊM`, `BÙA CLEAN ARCHITECTURE`.
  - Inputs: Custom owner name, optional custom wish.
  - Color palettes: Yellow (Classic), Crimson (Blood Moon), Cyan (Jade Mint), Purple (Cyber).
  - "Khai Quang & Thỉnh Bùa": Synthesizes talisman chime, triggers colored confetti, stamps rotated seal badge (`✓ ĐÃ KHAI QUANG - ZERO_BUG_GUARANTEED`).
  - "Copy Mã Bùa": Generates and copies ASCII boxed talisman card to clipboard.

#### F. Grimoire Project Showcase & Modal (`ProjectsSection.tsx`, `ProjectModal.tsx`)
- **Showcase Grid**:
  - Category filters: `All`, `Full-Stack Systems`, `Creative UI / Web Audio`, `AI & Web3 Protocols`, `Developer Tooling`.
  - 6 comprehensive projects: Nocturne OS, Phantom Flow, Grimoire UI, Spectral Agents, Kitsune KV, macatung.dev v2.0.
  - Cards feature: gradient headers, category tags, 3-column key metrics, tech stack pills, direct demo and GitHub links.
- **Interactive Modal**:
  - Escape key and backdrop click dismissal.
  - Body scroll lock during modal lifecycle.
  - Architecture highlights checklist, full tech stack, and "Midnight Lore" callout card.

#### G. Midnight Clock & Live Status (`MidnightClock.tsx` -> `MidnightClock.vue`)
- **Live Widgets**:
  - Live 24-hour clock (`HH:MM:SS`) with pulsating neon green indicator.
  - Status mode: `Midnight Code Mode` (22:00 - 05:00) vs `Daylight Prep` (06:00 - 21:00).
  - Dynamic caffeine percentage indicator (90–99% at night, 65–85% by day).
  - Simulated latency indicator (12ms).

#### H. About, Experience, Skills & Contact Sections
- **About Section**:
  - 4 stats cards (Coffees Brewed, Bugs Exorcised, Hop Velocity, Code Shipped at 12AM).
  - 3 interactive tabs: Manifesto with `midnight_protocol.ts` code snippet, Day vs Night mode comparison, and Core Engineering Values.
- **Experience Section**:
  - Vertical timeline with pulsing node indicators.
  - 4 timeline cards with achievements, technologies, and "Midnight Quest Lore".
- **Skills Section**:
  - 4 categories with 18 skills, proficiency bars (82% to 100%), runes, and verified guarantee badge.
- **Contact Section / Summoning Altar**:
  - Form fields: Name, Email, Project Type, Coffee Offering, Message.
  - Direct spectral channels (Email, GitHub, Telegram, LinkedIn).
  - Requires backend Laravel endpoint for persistent SQLite/MySQL submission and Inertia flash response.

---

### 1.3 Design System, Fonts & Color Palettes

| Token | Hex / Value | Purpose |
|---|---|---|
| Dark Matte Background | `#06080d` | Primary page & canvas backdrop |
| Midnight 950 | `#04070d` | Deepest panels & cards |
| Midnight 900 | `#070b14` | Sub-panels & terminal chrome |
| Midnight 850 | `#0c1220` | Card borders & dividers |
| Neon Mint / Jade | `#00f5a0` | Primary accent, status pings, highlights |
| Neon Cyan | `#00f5d4` / `#00d2ff` | Secondary glow & interactive links |
| Talisman Gold / Paper | `#ffd166` / `#ffe57f` / `#f59e0b` | Talisman paper, stars, badges |
| Cinnabar / Crimson / Blood | `#e63946` / `#ef233c` / `#ff0054` | Seal stamps, blood moon mode, danger |
| Phantom Purple | `#9d4edd` / `#c77dff` | Mystic accents & 4AM mood |

**Typography**:
- Sans: `"Plus Jakarta Sans", system-ui, sans-serif`
- Display: `"Space Grotesk", "Syne", sans-serif`
- Mono: `"JetBrains Mono", "Fira Code", monospace`
- Rune: `"Cinzel Decorative", serif`

---

### 1.4 Environment & Execution Findings

- **Operating System**: Windows (PowerShell)
- **PHP Version**: Modern PHP 8.2+ is located at `C:\laragon\bin\php\php-8.2.30-nts-Win32-vs16-x64\php.exe`.
- **Composer PATH Note**: The global environment PATH points to an ancient PHP version (5.6). When executing Composer commands, prepending `$env:PATH = "C:\laragon\bin\php\php-8.2.30-nts-Win32-vs16-x64;" + $env:PATH` ensures Composer 2.4.1 runs without error.

---

## 2. Logic Chain

1. **Architecture Porting Mapping**:
   - The current standalone React 19 SPA (`App.tsx` + components in `src/components/`) will be mapped to a Laravel 11/12 + Inertia.js (Vue 3 `<script setup>`) application structure:
     - `resources/js/app.ts` -> Inertia Vue 3 initialization.
     - `resources/js/Pages/Home.vue` -> Main single-page portfolio view incorporating all ported Vue components.
     - `resources/js/Components/` -> Ported Vue 3 components (`MacatungMascot.vue`, `TalismanCanvas.vue`, `MidnightTerminal.vue`, `TalismanGenerator.vue`, `ProjectsSection.vue`, `ProjectModal.vue`, `MidnightClock.vue`, `AboutSection.vue`, `ExperienceSection.vue`, `SkillsSection.vue`, `ContactSection.vue`, `Navbar.vue`, `Footer.vue`, `SoundToggle.vue`, `Icons.vue`).
     - `resources/js/audio/soundEffects.ts` -> TypeScript Web Audio synthesis engine (zero changes needed to core synthesis logic).
     - `resources/js/data/` -> Preserved data files (`projectsData.ts`, `experienceData.ts`, `skillsData.ts`, `talismanData.ts`).
     - `resources/js/types/` -> Preserved TypeScript types (`portfolio.ts`).
     - `resources/views/app.blade.php` -> Root HTML template with fonts and Inertia directives.

2. **Responsive Fixes & Text Overlap Resolution**:
   - Mobile screens (360px - 480px) and tablet screens (768px - 1024px) need specific CSS adjustments:
     - Use `break-words`, `overflow-hidden`, and responsive font clamp sizes on headings (`text-3xl sm:text-5xl lg:text-7xl`) to avoid text wrapping collisions.
     - Talisman Forge card width must use `w-full max-w-[320px] sm:w-80` to prevent horizontal clipping on 360px devices.
     - Timeline on mobile needs to hide left-placed absolute dates and use inline mobile badges.
     - Interactive Mascot SVG must use `viewBox="0 0 240 280"` and responsive max-width sizing.
     - Touch targets across all buttons, chips, and links must have a minimum size of 44x44px.

3. **Backend Summoning Altar Integration**:
   - Route: `POST /contact` (or `POST /summon`).
   - Controller: `ContactController@store` with validation rules:
     - `name`: `required|string|max:100`
     - `email`: `required|email|max:150`
     - `project_type`: `nullable|string|max:100`
     - `coffee_offering`: `nullable|string|max:100`
     - `message`: `required|string|max:2000`
   - Database: SQLite/MySQL migration creating `contact_inquiries` table with `id`, `name`, `email`, `project_type`, `coffee_offering`, `message`, `ip_address`, `created_at`, `updated_at`.
   - Inertia Form Helper: `useForm` from `@inertiajs/vue3` provides reactive `form.processing`, `form.errors`, and `form.post` with flash message handling without page reloads.

---

## 3. Caveats

1. **Icons Library**: The React project uses `lucide-react`. In the Vue 3 migration, `lucide-vue-next` should be installed to provide equivalent Lucide icons in `<script setup>`.
2. **Confetti Library**: `canvas-confetti` (and `@types/canvas-confetti`) works identically in Vue 3.
3. **Canvas Animation Lifecycle**: `TalismanCanvas.vue` must handle `requestAnimationFrame` lifecycle within `onMounted` and `onUnmounted` to prevent memory leaks when switching routes or Hot Module Reloading.
4. **PHP Environment Execution**: Any Laravel or Composer command must use PHP 8.2+ by setting `$env:PATH = "C:\laragon\bin\php\php-8.2.30-nts-Win32-vs16-x64;" + $env:PATH;`.

---

## 4. Conclusion

- The codebase is clean, well-structured, and rich with unique custom animations, Web Audio synthesis, interactive terminal capabilities, and talisman forging logic.
- All core assets, SVG illustrations, typography tokens, color variables, sound algorithms, and portfolio data have been inventoried and are fully ready for seamless porting to Laravel 11/12 + Inertia.js (Vue 3).
- The identified layout and text collision areas on small viewports have clear remedies to guarantee full mobile and desktop responsiveness.

---

## 5. Verification Method

To verify the codebase assets and preparation for full-stack migration:

1. **Inspect Survey Artifacts**:
   - Check `d:/Work/macatung/.agents/explorer_survey_1/handoff.md` (this report).
   - Check `d:/Work/macatung/.agents/explorer_survey_1/BRIEFING.md`.

2. **Verify PHP 8.2+ & Composer Availability**:
   ```powershell
   $env:PATH = "C:\laragon\bin\php\php-8.2.30-nts-Win32-vs16-x64;" + $env:PATH
   php -v
   composer --version
   ```

3. **Verify Existing React Build**:
   ```powershell
   npm run build
   ```
   (Ensures baseline code compiles with 0 errors).
