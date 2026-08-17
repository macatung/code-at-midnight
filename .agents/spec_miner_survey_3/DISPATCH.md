## 2026-08-17T06:52:48Z
You are the Requirements Spec Miner for the macatung.dev full-stack migration.
Your working directory is d:/Work/macatung/.agents/spec_miner_survey_3/.

Instructions:
1. Read `d:/Work/macatung/ORIGINAL_REQUEST.md`.
2. Probing all requirements, extract an exhaustive functional & non-functional specification:
   - Every UI component (Mascot, Canvas, Audio, Terminal, Talisman Forge, Projects & Modal, Midnight Clock, About/Philosophy/Experience/Skills, Summoning Altar contact form).
   - Interaction specifications (tap/click triggers, audio frequencies/envelopes, terminal commands & help output, talisman generation formulas/stamps, modal focus/close).
   - Responsive layout constraints: 360px-480px mobile, 768px-1024px tablet, 1440px desktop. Avoid text clipping, overlapping, awkward wraps. Minimum 44x44px touch targets.
   - Backend requirements: Route `/contact` or `/summon`, validation rules (name, email, project type, coffee offering, message), SQLite/MySQL storage, flash message via Inertia.
   - Acceptance criteria checklist and edge cases.
3. Write your specification report to `d:/Work/macatung/.agents/spec_miner_survey_3/handoff.md` and notify the orchestrator with send_message.
