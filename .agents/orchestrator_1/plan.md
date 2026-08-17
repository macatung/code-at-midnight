# Orchestration Plan — macatung.dev Full-Stack Migration

## Objectives
1. Migrate macatung.dev portfolio to Laravel + Inertia.js (Vue 3 Composition API `<script setup>`) + Vite + TailwindCSS.
2. Port all frontend features into modular Vue 3 components (Mascot with touch & audio, Particles Canvas, Web Audio Synthesizer, Midnight Terminal CLI, Talisman Forge, Project Showcase & Modal, Midnight Clock, Timeline & Skills).
3. Fix all text collisions, line breaks, and responsive layout across mobile (360px-480px), tablet (768px-1024px), and desktop.
4. Implement backend contact form handling (Summoning Altar) with SQLite/MySQL database storage, validation, and Inertia flash messages.
5. Setup comprehensive E2E tests (Tiers 1-4) and adversarial coverage hardening (Tier 5).

## Phases
1. **Phase 0: Survey & Codebase Reconnaissance**
   - Dispatch 3 parallel Explorers:
     - Explorer 1: Explore current codebase structure, assets, existing scripts, styles, components, and design tokens.
     - Explorer 2: Analyze environment, PHP / Composer setup (using PHP 8.2+ path), Node / Vite setup, Laravel structure.
     - Spec Miner / Explorer 3: Extract exhaustive feature requirements from `ORIGINAL_REQUEST.md` and UI behaviors.
   - Aggregate into `PROJECT.md` (Feature Inventory, Architecture, Interface Contracts, Milestones).

2. **Phase 1: Dual Track Launch**
   - E2E Testing Track Orchestrator (`e2e_testing_orch`) launched in parallel to design test infrastructure, runners, and Tiers 1-4 test cases.
   - Implementation Track Milestones:
     - **Milestone 1**: Laravel + Inertia Vue 3 Foundation & Environment Setup
     - **Milestone 2**: Frontend Vue 3 Modular Component Porting & Responsive Layout Polish
     - **Milestone 3**: Backend Summoning Altar Integration (DB, Controller, Validation, Flash)
     - **Milestone 4 (Final)**: Pass 100% E2E tests (Phase 1) + Adversarial Coverage Hardening (Phase 2).

3. **Phase 2: Milestone Iteration & Gating**
   - Every milestone executed with: Explorer(s) → Worker → 2 Reviewers + 2 Challengers + Forensic Auditor.
   - Gate verdict checked in `GATE_STATUS.md`.

4. **Phase 3: Final Verification & Handover**
   - Final end-to-end verification.
   - Soft/hard handoff to Sentinel.
