## 2026-08-17T07:25:55Z
You are the Sub-Orchestrator for Milestone 3: `m3_backend_altar_integration`.
Your working directory is d:/Work/macatung/.agents/sub_orch_m3/.

Scope & Mission:
1. Read `d:/Work/macatung/ORIGINAL_REQUEST.md` and `d:/Work/macatung/PROJECT.md`.
2. Implement backend Summoning Altar Contact Form Integration:
   - Database Migration: Create `database/migrations/2026_08_17_000002_create_contact_submissions_table.php` (or similar) with columns (`name`, `email`, `project_type`, `coffee_offering`, `message`, `ip_address`, `user_agent`, timestamps). Execute migration using PHP 8.2+ (`C:\laragon\bin\php\php-8.2.30-nts-Win32-vs16-x64\php.exe artisan migrate`).
   - Eloquent Model: Create `app/Models/ContactSubmission.php` with fillable attributes, casts, and helper scopes.
   - FormRequest Validation: Create `app/Http/Requests/ContactRequest.php` enforcing strict validation rules for `name`, `email`, `project_type`, `coffee_offering`, and `message` (min:10, max:5000) with custom error messages.
   - Controller: Create `app/Http/Controllers/ContactController.php` with `store(ContactRequest $request)` that persists data safely in SQLite, generates a `reference_id` (e.g. `SUMMON-XXXX`), and redirects back with Inertia flash data (`flash.success`, `flash.reference_id`).
   - Routes: Register `POST /contact` (named `contact.store`) and `POST /summon` in `routes/web.php`. Ensure `HandleInertiaRequests` properly shares flash data (`success`, `error`, `reference_id`).
   - Frontend Integration: Update `resources/js/Components/contact/ContactSection.vue` to use `@inertiajs/vue3` `useForm` connecting directly to `contact.store`. Handle `form.processing`, `form.errors`, `onSuccess` confetti burst and success chime (`sound.playSuccess()`), and render the flash confirmation with reference ID.
3. Backend Automated Tests:
   - Create `tests/Feature/ContactSubmissionTest.php` testing:
     * Successful submission creates record in SQLite database
     * Validation errors on missing required fields (name, email, message)
     * Validation errors on invalid email format
     * Validation errors on message < 10 characters
     * Validation errors on invalid project_type
     * Inertia flash session data returned properly
4. Execute the full Iteration Loop:
   - Spawn Explorer (`teamwork_preview_explorer`)
   - Spawn Worker (`teamwork_preview_worker`) with mandatory integrity warning:
     "DO NOT CHEAT. All implementations must be genuine. DO NOT hardcode test results, create dummy/facade implementations, or circumvent the intended task. A auditor will independently verify your work. Integrity violations WILL be detected and your work WILL be rejected."
   - Spawn Reviewers (2) (`teamwork_preview_reviewer`)
   - Spawn Challengers (2) (`teamwork_preview_challenger`)
   - Spawn Forensic Auditor (`teamwork_preview_auditor`)
   - Gate check in `GATE_STATUS.md`
5. Verification:
   - `& "C:\laragon\bin\php\php-8.2.30-nts-Win32-vs16-x64\php.exe" artisan test` passes with 100% success.
   - `npm.cmd run build` exits 0 with zero Vue compiler errors.
   - `npx.cmd tsc --noEmit` exits 0.
   - `node tests/run_all_tests.js` passes all tests.
6. Once gate passes, write `d:/Work/macatung/.agents/sub_orch_m3/handoff.md` and notify Project Orchestrator via `send_message`.
