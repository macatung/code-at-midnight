# Scope: Milestone 2 — Frontend Components & Responsive Polish

## Architecture
- Framework: Vue 3 (Composition API `<script setup lang="ts">`) + Inertia.js (`@inertiajs/vue3`)
- UI System: TailwindCSS v3 (`#06080d` obsidian carbon dark theme, neon mint `#00f5a0`, talisman gold `#ffd166`, cinnabar red `#ff0054`, phantom purple `#9d4edd`), glassmorphism, responsive typography (`clamp()`, `break-words`, `tracking-tight`), min 44x44px touch targets.
- Interactive Engines: Web Audio API Synthesizer (`soundEffects.ts`), 2D Canvas Talisman particle engine (`TalismanCanvas.vue`), interactive Mascot with mood states & hop counter (`MacatungMascot.vue`), interactive REPL Terminal (`MidnightTerminal.vue`), Developer Talisman Forge (`TalismanGenerator.vue`), Grimoire Project Showcase & Modal (`ProjectsSection.vue`, `ProjectModal.vue`), Midnight Clock (`MidnightClock.vue`), Stats & Manifesto (`AboutSection.vue`), Skills Arsenal (`SkillsSection.vue`), Experience Quest Timeline (`ExperienceSection.vue`), Hero (`HeroSection.vue`), Navbar & Footer (`Navbar.vue`, `Footer.vue`, `SoundToggle.vue`, `Icons.vue`), Contact Altar Layout (`ContactSection.vue`), and Root Portfolio Page (`Home.vue`).

## Component Inventory
| # | Component | File Path | Scope & Key Requirements |
|---|-----------|-----------|--------------------------|
| 1 | Audio Synthesizer | `resources/js/audio/soundEffects.ts` | Web Audio API procedural sound synthesizer (hop, talisman, click, key, success, pitch variation, mute persistence). |
| 2 | Mascot Component | `resources/js/Components/mascot/MacatungMascot.vue` | SVG anatomy (robe, hat, fangs, glowing eyes, forehead talisman, headphones), 4 mood states (`normal`, `caffeine`, `sleepy`, `rage`), 450ms squash-stretch hop animation, touch/tap handler, speech quotes, milestone celebrations (confetti + fanfare every 10 hops), localStorage hop persistence. |
| 3 | Talisman Canvas | `resources/js/Components/mascot/TalismanCanvas.vue` | 2D Canvas background particle engine with paper talismans (tech runes), glowing fireflies, embers, mouse repulsion (100px radius), screen wrapping, onMounted/onUnmounted lifecycle. |
| 4 | Midnight Clock | `resources/js/Components/mascot/MidnightClock.vue` | Live digital clock `HH:mm:ss`, live pulse dot, Midnight Mode (>=22:00 or <=05:00) vs Daylight Prep badge, caffeine calculator, latency ping. |
| 5 | Midnight Terminal | `resources/js/Components/terminal/MidnightTerminal.vue` | Interactive REPL shell (`macatung-cli`), full 11-command suite (`help`, `whoami`/`bio`, `projects`/`ls`, `skills`, `hop`, `coffee`, `talisman`, `slogan`, `summon`, `sudo rm -rf bugs`, `clear`), command history (ArrowUp/ArrowDown), expand/collapse, copy logs. |
| 6 | Talisman Forge | `resources/js/Components/talisman/TalismanGenerator.vue` | Developer talisman forge, 6 presets, custom name & wish, 4 color palettes, live preview, Khai Quang seal animation with rotated badge, ASCII exporter. |
| 7 | Projects Showcase & Modal | `resources/js/Components/projects/ProjectsSection.vue`, `ProjectModal.vue` | Grimoire showcase, category filters (`all`, `fullstack`, `creative`, `ai-web3`, `tools`), 6 projects, modal dialog with architecture highlights, midnight lore, Escape key and backdrop dismiss, body scroll lock. |
| 8 | About Section | `resources/js/Components/about/AboutSection.vue` | 4 developer stat cards, 3-tab manifesto panel (`Triết Lý 00:00 AM`, `Day vs Night`, `Khắc Bùa Chất Lượng`). |
| 9 | Skills Section | `resources/js/Components/skills/SkillsSection.vue` | 4 categories, 18 skills with interactive proficiency bars (82-100%), runes, verification pledge. |
| 10 | Experience Timeline | `resources/js/Components/experience/ExperienceSection.vue` | Chronological timeline, achievement bullets, tech stack pills, Midnight Quest Lore. |
| 11 | Hero Section | `resources/js/Components/hero/HeroSection.vue` | Hero title `"Code at midnight."` with neon gradient, interactive Mascot stage, CTAs, trust badges, social links. |
| 12 | Navigation & Layout | `resources/js/Components/layout/Navbar.vue`, `Footer.vue`, `SoundToggle.vue`, `resources/js/Components/ui/Icons.vue` | Sticky header with backdrop blur, brand logo, navigation links, mobile drawer toggle, MidnightClock & SoundToggle integration, Hop-to-Top button, sound synthesis, copyright, Easter egg heart celebration. |
| 13 | Contact Section (UI) | `resources/js/Components/contact/ContactSection.vue` | Summoning Altar UI layout, direct spectral channels, prepared for M3 backend form submission. |
| 14 | Main Page Integration | `resources/js/Pages/Home.vue` | Master layout assembling all sections with smooth scrolling, section anchors, and dynamic state integration. |

## Responsive & Layout Requirements
- Viewport compatibility: 360px–480px (mobile), 768px–1024px (tablet), 1440px+ (desktop).
- Zero text collisions or overlapping typography.
- Fluid typography using `clamp()` and responsive Tailwind classes (`text-sm md:text-base lg:text-lg`).
- Minimum touch target 44x44px for buttons, toggles, filters, and interactive triggers.
- Safe padding and overflow containment (`overflow-x-hidden`) to avoid any horizontal scrollbar on small screens.
