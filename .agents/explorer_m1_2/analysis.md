# Frontend Asset Setup & Type Analysis Report
**Explorer 2 — Milestone 1: Foundation & Backend Setup**
**Timestamp:** 2026-08-17T13:58:00+07:00

---

## 1. Executive Summary

This report delivers a comprehensive investigation of the frontend asset setup, design token system, TypeScript type contracts, and static datasets required to migrate `macatung.dev` from its React standalone prototype to a full-stack **Laravel + Inertia.js (Vue 3 Composition API `<script setup lang="ts">`) + Tailwind CSS + TypeScript + Vite** application.

---

## 2. Frontend Stack & Build Tooling Requirements

### 2.1 Package Dependencies Matrix (`package.json`)
The existing `package.json` contains React 19 dependencies (`react`, `react-dom`, `@vitejs/plugin-react`, `lucide-react`). For Milestone 1, the frontend stack must transition to Vue 3 + Inertia:

| Package | Role | Required Version / Target |
|---|---|---|
| `vue` | Vue 3 Reactive Runtime & SFC Engine | `^3.5.0` |
| `@inertiajs/vue3` | Inertia.js Vue 3 monolith bridge adapter | `^2.0.0` or `^1.2.0` |
| `lucide-vue-next` | Vue 3 icon set replacing `lucide-react` | `^0.475.0` |
| `canvas-confetti` | Milestone celebration particle bursts | `^1.9.4` |
| `clsx`, `tailwind-merge` | Class utility helpers | `^2.1.1`, `^3.6.0` |
| `laravel-vite-plugin` | Laravel Blade integration for Vite | `^1.2.0` |
| `@vitejs/plugin-vue` | Vite Vue 3 Single File Component plugin | `^5.2.0` |
| `tailwindcss`, `postcss`, `autoprefixer` | Utility CSS compiler engine | `^3.4.17`, `^8.5.2`, `^10.5.4` |
| `typescript`, `@types/node` | Strict type checking | `~5.x` / `~6.x`, `^24.13.0` |
| `@types/canvas-confetti` | TypeScript definitions for canvas-confetti | `^1.9.0` |

### 2.2 Vite Configuration (`vite.config.ts`)
The Vite configuration must bundle `resources/css/app.css` and `resources/js/app.ts`, register the Vue plugin with asset transform options, and expose `@` path aliases:

```ts
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
      '@': path.resolve(__dirname, 'resources/js'),
    },
  },
});
```

### 2.3 PostCSS Configuration (`postcss.config.js`)
```js
export default {
  plugins: {
    tailwindcss: {},
    autoprefixer: {},
  },
};
```

### 2.4 TypeScript Configuration (`tsconfig.json`)
```json
{
  "compilerOptions": {
    "target": "ESNext",
    "useDefineForClassFields": true,
    "module": "ESNext",
    "moduleResolution": "Bundler",
    "strict": true,
    "jsx": "preserve",
    "sourceMap": true,
    "resolveJsonModule": true,
    "isolatedModules": true,
    "esModuleInterop": true,
    "lib": ["ESNext", "DOM", "DOM.Iterable"],
    "skipLibCheck": true,
    "paths": {
      "@/*": ["./resources/js/*"]
    },
    "types": ["vite/client"]
  },
  "include": ["resources/js/**/*.ts", "resources/js/**/*.d.ts", "resources/js/**/*.vue"]
}
```

### 2.5 Inertia Root Blade View (`resources/views/app.blade.php`)
```html
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title inertia>{{ config('app.name', 'macatung.dev') }} — Code at midnight</title>
    <meta name="description" content="Portfolio of macatung.dev — Full-Stack Night-Crawler & Creative Engineer crafting supernatural web applications under the midnight moon.">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel+Decorative:wght@700&family=JetBrains+Mono:ital,wght@0,300;0,400;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&family=Syne:wght@700;800&display=swap" rel="stylesheet">

    @routes
    @vite(['resources/css/app.css', 'resources/js/app.ts'])
    @inertiaHead
</head>
<body class="bg-midnight-950 text-slate-100 font-sans antialiased selection:bg-phantom-mint selection:text-midnight-950 overflow-x-hidden">
    @inertia
</body>
</html>
```

### 2.6 Inertia Vue App Entrypoint (`resources/js/app.ts`)
```ts
import '../css/app.css';

import { createApp, h, DefineComponent } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

const appName = import.meta.env.VITE_APP_NAME || 'macatung.dev';

createInertiaApp({
  title: (title) => title ? `${title} — ${appName}` : `${appName} — Code at midnight`,
  resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob<DefineComponent>('./Pages/**/*.vue')),
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .mount(el);
  },
  progress: {
    color: '#00f5a0',
  },
});
```

---

## 3. Tailwind Color Palette & Design Tokens

### 3.1 Extended Theme Tokens (`tailwind.config.js`)
The `tailwind.config.js` content paths must target Blade views and Vue components:
`content: ["./resources/views/**/*.blade.php", "./resources/js/**/*.{vue,js,ts,jsx,tsx}"]`.

#### Color Palette Mappings
1. **Midnight Palette (`midnight`)**: Deep obsidian night aesthetic
   - `950`: `#04070d` (or `#06080d` / `#0B0F17` for obsidian carbon backdrop)
   - `900`: `#070b14`
   - `850`: `#0c1220`
   - `800`: `#11182c`
   - `700`: `#1a233d`
   - `600`: `#253254`
   - `500`: `#394b7a`
2. **Talisman Palette (`talisman`)**: Mystic yellow, gold & sealing wax
   - `yellow`: `#ffd166`
   - `gold`: `#f59e0b` (alias: `#e5a93c`)
   - `paper`: `#ffea79`
   - `cinnabar`: `#e63946`
   - `seal`: `#ef233c`
3. **Phantom Luminescence (`phantom`)**: Cyberpunk runes & neon accents
   - `cyan`: `#00f5d4`
   - `mint`: `#00f5a0`
   - `emerald`: `#10b981`
   - `blue`: `#00bbf9`
   - `purple`: `#9d4edd`
   - `arcana`: `#8b5cf6`
   - `lavender`: `#c77dff`
   - `neon`: `#7000ff`
   - `blood`: `#ff0054`

#### Typography Tokens
- `sans`: `['"Plus Jakarta Sans"', 'system-ui', 'sans-serif']`
- `display`: `['"Space Grotesk"', '"Syne"', 'sans-serif']`
- `mono`: `['"JetBrains Mono"', '"Fira Code"', 'monospace']`
- `rune`: `['"Cinzel Decorative"', 'serif']`

#### Keyframe Animations & Shadows
- **Animations**:
  - `hop`: `hop 1s cubic-bezier(0.28, 0.84, 0.42, 1) infinite` (squash-stretch jump)
  - `hop-fast`: `hop 0.6s cubic-bezier(0.28, 0.84, 0.42, 1) infinite`
  - `float` / `float-slow`: `4s` / `7s` gentle hover
  - `pulse-glow`: `2.5s ease-in-out infinite` drop-shadow glow
  - `talisman-flutter`: `3s ease-in-out infinite` wind rotation & skew
  - `shimmer`: `2.5s linear infinite`
- **Box Glow Shadows**:
  - `glow-cyan`: `0 0 25px -5px rgba(0, 245, 212, 0.4)`
  - `glow-mint`: `0 0 30px -5px rgba(0, 245, 160, 0.45)`
  - `glow-talisman`: `0 0 35px -5px rgba(255, 209, 102, 0.5)`
  - `glow-purple`: `0 0 30px -5px rgba(157, 78, 221, 0.45)`
  - `glow-blood`: `0 0 30px -5px rgba(255, 0, 84, 0.45)`

### 3.2 Global CSS Utility Classes (`resources/css/app.css`)
```css
@tailwind base;
@tailwind components;
@tailwind utilities;

@layer base {
  :root {
    --accent-glow: rgba(0, 245, 160, 0.25);
    --accent-primary: #00f5a0;
    --accent-secondary: #00d2ff;
  }

  body {
    background-color: #06080d;
    color: #e2e8f0;
    font-feature-settings: "cv02", "cv03", "cv04", "cv11";
  }

  /* Custom Clean Midnight Scrollbar */
  ::-webkit-scrollbar {
    width: 6px;
    height: 6px;
  }

  ::-webkit-scrollbar-track {
    background: #06080d;
  }

  ::-webkit-scrollbar-thumb {
    background: #1e293b;
    border-radius: 3px;
  }

  ::-webkit-scrollbar-thumb:hover {
    background: #00f5a0;
  }
}

/* Minimalist Dark Glass Panels */
.glass-panel {
  background: rgba(10, 14, 23, 0.7);
  backdrop-filter: blur(16px);
  -webkit-backdrop-filter: blur(16px);
  border: 1px solid rgba(255, 255, 255, 0.06);
}

.glass-panel-glow {
  background: rgba(10, 14, 23, 0.85);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border: 1px solid rgba(0, 245, 160, 0.18);
  box-shadow: 0 10px 30px 0 rgba(0, 0, 0, 0.5), inset 0 0 16px rgba(0, 245, 160, 0.02);
}

.glass-panel-talisman {
  background: linear-gradient(135deg, rgba(255, 209, 102, 0.05) 0%, rgba(10, 14, 23, 0.85) 100%);
  backdrop-filter: blur(16px);
  border: 1px solid rgba(255, 209, 102, 0.15);
}

/* Text Glows */
.text-glow-mint {
  text-shadow: 0 0 16px rgba(0, 245, 160, 0.4);
}

.text-glow-talisman {
  text-shadow: 0 0 16px rgba(255, 209, 102, 0.4);
}

/* Talisman Paper Aesthetic */
.talisman-paper {
  background: linear-gradient(180deg, #ffe57f 0%, #fbd561 50%, #f4c430 100%);
  color: #c9182b;
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5);
  position: relative;
  overflow: hidden;
}

/* Minimalist Dark Grid & Dots */
.bg-grid-pattern {
  background-size: 40px 40px;
  background-image: 
    linear-gradient(to right, rgba(255, 255, 255, 0.02) 1px, transparent 1px),
    linear-gradient(to bottom, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
}

/* Hover Card Transitions */
.hover-card-glow {
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

.hover-card-glow:hover {
  transform: translateY(-3px);
  border-color: rgba(0, 245, 160, 0.3);
  box-shadow: 0 12px 28px -8px rgba(0, 0, 0, 0.6);
}
```

---

## 4. TypeScript Interface Contracts (`resources/js/types/portfolio.ts`)

The TypeScript definitions must be located in `resources/js/types/portfolio.ts` with comprehensive coverage of domain models, form models, and UI state:

```ts
export interface Project {
  id: string;
  title: string;
  tagline: string;
  description: string;
  category: 'fullstack' | 'creative' | 'ai-web3' | 'tools';
  coverGradient: string;
  tags: string[];
  techStack: string[];
  metrics: { label: string; value: string }[];
  liveUrl?: string;
  githubUrl?: string;
  featured?: boolean;
  architectureHighlights: string[];
  midnightFact: string;
}

export interface SkillCategory {
  title: string;
  iconName: string;
  badge: string;
  skills: {
    name: string;
    level: number; // 1-100
    rune: string;
    tag: string;
    description: string;
  }[];
}

export interface ExperienceItem {
  id: string;
  period: string;
  role: string;
  company: string;
  location: string;
  type: 'Full-time' | 'Contract' | 'Open Source' | 'Venture';
  summary: string;
  achievements: string[];
  technologies: string[];
  midnightQuest: string;
}

export interface DeveloperStat {
  label: string;
  value: string;
  unit?: string;
  iconName: string;
  description: string;
}

export interface TalismanPreset {
  id: string;
  title: string;
  runeTop: string;
  codeSnippet: string;
  meaning: string;
  colorScheme: 'yellow' | 'crimson' | 'cyan' | 'purple';
}

export interface FlashMessages {
  success?: string;
  error?: string;
  reference_id?: string;
}

export interface ContactFormData {
  name: string;
  email: string;
  project_type: string;
  coffee_offering: string;
  message: string;
}

export interface PageProps {
  flash?: FlashMessages;
  errors?: Record<string, string>;
  stats?: DeveloperStat[];
  [key: string]: unknown;
}

export type MascotMood = 'normal' | 'caffeine' | 'sleepy' | 'rage';

export interface MascotProps {
  size?: 'sm' | 'md' | 'lg' | 'hero';
  showControls?: boolean;
}

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

---

## 5. Portfolio Dataset Migration (`resources/js/data/`)

The 4 primary data sources must be placed under `resources/js/data/`:

### 5.1 `resources/js/data/projectsData.ts`
Contains 6 detailed showcase projects:
1. `midnight-terminal-os`: Nocturne OS (Fullstack, WebAssembly + Next.js + Rust)
2. `phantom-dex-protocol`: Phantom Flow (AI/Web3, Go + Solidity + GraphQL)
3. `jiangshi-ui-engine`: Grimoire UI (Creative, React 18 + Framer Motion + TailwindCSS)
4. `spectral-ai-agents`: Spectral Agents (AI/Web3, Python 3.11 + FastAPI + Tree-Sitter)
5. `hyper-cache-kv`: Kitsune KV (Tools, Rust + Tokio + gRPC + FlatBuffers)
6. `macatung-dev-v1`: macatung.dev The Midnight Grimoire (Creative, Web Audio + Canvas + Vite)

### 5.2 `resources/js/data/skillsData.ts`
Contains 4 categories with 18 skills and proficiency levels (82-100%):
- Category 1: `Frontend Sorcery & UI/UX` (React/Next.js, TypeScript, TailwindCSS, Three.js/Canvas, Web Audio API)
- Category 2: `Backend Alchemy & Systems` (Node.js/Bun/Deno, Go, Python FastAPI, PostgreSQL, Redis)
- Category 3: `Cloud Rituals & DevOps` (Docker & Kubernetes, AWS & Cloudflare, CI/CD, Monitoring)
- Category 4: `Dark Arts, AI & Architecture` (System Architecture, LLM Agents & RAG, Performance Profiling, Midnight Coffee Brewing)

### 5.3 `resources/js/data/experienceData.ts`
Contains 4 career timeline milestones and 4 developer stats:
- Career:
  1. `lead-midnight-architect`: Lead Full-Stack & Creative Systems Architect at Nocturne Labs (2024 — Present)
  2. `senior-fullstack-engineer`: Senior Full-Stack Engineer at Aetheria Cloud Matrix (2022 — 2024)
  3. `creative-developer-contract`: Creative Frontend & UI/UX Specialist at Vortex Interactive Studios (2020 — 2022)
  4. `indie-hacker-origins`: Night Crawler & Indie Software Hacker at The Midnight Lair (2018 — 2020)
- Stats (`developerStats`):
  - Coffees Brewed: `2,840+ Cups`
  - Bugs Exorcised: `4,192 Squashed`
  - Hop Velocity: `60 FPS`
  - Code Shipped at 12AM: `99.8% Deploy`

### 5.4 `resources/js/data/talismanData.ts`
Contains 6 developer talisman presets:
1. `bua-no-bug`: BÙA CODE 0 BUG (`yellow`)
2. `bua-friday-deploy`: BÙA DEPLOY THỨ 6 (`crimson`)
3. `bua-x2-salary`: BÙA TĂNG LƯƠNG X2 (`purple`)
4. `bua-no-conflict`: BÙA 0 CONFLICT (`cyan`)
5. `bua-fix-prod-12am`: BÙA FIX PROD NỬA ĐÊM (`yellow`)
6. `bua-clean-code`: BÙA CLEAN ARCHITECTURE (`cyan`)

### 5.5 Sound Synthesizer Engine (`resources/js/audio/soundEffects.ts`)
Zero external dependency Web Audio API synthesizer implementing `ISoundEngine`:
- Audio Context singleton handling suspend/resume and `localStorage` sound mute state (`macatung_sound_muted`).
- Methods: `playHop(intensity)`, `playTalisman()`, `playClick()`, `playTerminalKey()`, `playSuccess()`.

---

## 6. Implementation Readiness & Guidance for M1 Builders

1. **Clean Scaffolding**: Replace React packages with Vue 3 / Inertia packages in `package.json`.
2. **Path Setup**: Move all frontend code into standard Laravel layout under `resources/js/` and `resources/css/`.
3. **Vite Build**: Ensure `npm run build` compiles both `resources/css/app.css` and `resources/js/app.ts` into `public/build/`.
4. **Icons**: Replace `lucide-react` imports with `lucide-vue-next` (e.g. `Terminal`, `Coffee`, `Volume2`, `VolumeX`, `Sparkles`, `Code2`, `Layers`, `ExternalLink`, etc.).
