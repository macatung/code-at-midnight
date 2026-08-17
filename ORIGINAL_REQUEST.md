# Original User Request

## Initial Request — 2026-08-17T13:52:08+07:00

Convert the macatung.dev portfolio project into a modern Laravel (with Inertia.js & Vue 3) full-stack web application, fix all text overlapping and line-break rendering bugs, and ensure seamless mobile responsiveness across all devices.

Working directory: d:/Work/macatung
Integrity mode: development

Note on environment: Multiple PHP versions are installed in C:\laragon\bin\php (including php-8.2, php-8.3, php-8.4). When executing PHP/Composer commands, use the PHP 8.2+ binary (e.g. C:\laragon\bin\php\php-8.2.30-nts-Win32-vs16-x64\php.exe) or ensure the modern PHP path is used.

## Requirements

### R1. Full-Stack Laravel + Inertia.js (Vue 3) Migration
- Set up a clean Laravel project with Inertia.js (Vue 3 Composition API `<script setup>`), Vite, and TailwindCSS.
- Port all current features into Vue 3 components:
  - Animated Hopping "Ma Cà Tưng" Mascot (`MacatungMascot.vue`) with touch/tap support, squash-and-stretch physics, and mood states.
  - Floating Talisman & Firefly Particles Canvas (`TalismanCanvas.vue`).
  - Web Audio API Sound Effects Synthesizer (`soundEffects.ts`).
  - Interactive Terminal CLI (`MidnightTerminal.vue`) with full command suite (`help`, `coffee`, `hop`, `talisman`, `projects`, etc.).
  - Developer Talisman Forge (`TalismanGenerator.vue`) with real-time preview, copy, and seal stamp animation.
  - The Grimoire Project Showcase & Modal dialogs (`ProjectsSection.vue`, `ProjectModal.vue`).
  - Midnight Clock and live status badge (`MidnightClock.vue`).
  - Origin & Philosophy timeline (`AboutSection.vue`, `ExperienceSection.vue`, `SkillsSection.vue`).

### R2. Responsive UI Overhaul & Bug Fixes
- Eliminate all text collisions, awkward line breaks, and overlapping typography on mobile screens (360px – 480px) and tablet screens (768px – 1024px).
- Ensure hero typography (`Code at midnight.`), section headings, stat cards, and grimoire grids fluidly adapt with appropriate `break-words`, `tracking-tight`, responsive font sizes (`clamp()` or responsive Tailwind classes), and touch-friendly tap targets (minimum 44x44px).
- Maintain the deep matte obsidian / carbon dark aesthetic (`#06080d`), removing visual clutter while preserving neon mint and talisman gold accents.

### R3. Backend Summoning Altar Integration
- Implement a robust Laravel controller and Inertia form submission for the "Summoning Altar" contact form (`/contact` or `/summon`).
- Validate incoming requests (name, email, project type, coffee offering, message), store contact inquiries in a SQLite/MySQL database table, and return seamless Inertia flash response without page reloads.

## Acceptance Criteria

### Stack & Build Verification
- [ ] Laravel with Inertia Vue 3 builds cleanly (`npm run build` exits 0 with zero Vue / Tailwind compiler errors).
- [ ] Backend routes (`/`, `/contact` POST) function cleanly via Inertia without page reload.
- [ ] Database migration and model for contact submissions execute properly.

### UI & Layout Quality (Mobile & Desktop)
- [ ] No text overlap, horizontal scrollbars, or broken layout at viewports 360px, 390px (iPhone), 768px (iPad), and 1440px (Desktop).
- [ ] Interactive Mascot responds immediately to touch taps on mobile with high hop animation and audio synthesis.
- [ ] Terminal CLI, Talisman Forge, and Project Modal fit and scroll comfortably on mobile screens without being clipped.
- [ ] Sound toggle and synthesized Web Audio effects function reliably across modern browsers.
