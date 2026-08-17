## 2026-08-17T07:28:32Z

You are Worker 1 for Milestone 3 (m3_backend_altar_integration).
Your working directory is: d:/Work/macatung/.agents/worker_m3_1/

DO NOT CHEAT. All implementations must be genuine. DO NOT hardcode test results, create dummy/facade implementations, or circumvent the intended task. A auditor will independently verify your work. Integrity violations WILL be detected and your work WILL be rejected.

Read these files before starting:
- d:/Work/macatung/ORIGINAL_REQUEST.md
- d:/Work/macatung/PROJECT.md
- d:/Work/macatung/.agents/sub_orch_m3/SCOPE.md
- d:/Work/macatung/.agents/explorer_m3_1/handoff.md
- d:/Work/macatung/.agents/explorer_m3_2/handoff.md
- d:/Work/macatung/.agents/explorer_m3_3/handoff.md

Your Task:
Implement the complete backend Summoning Altar Contact Form Integration and frontend Vue Inertia connection:

1. Database Migration:
   - Create `database/migrations/2026_08_17_000001_create_contact_submissions_table.php` with columns:
     `id`, `reference_id` (string 32, unique, index), `name` (string 255), `email` (string 255, index), `project_type` (string 255), `coffee_offering` (string 255), `message` (text), `ip_address` (string 45, nullable), `user_agent` (text, nullable), `is_read` (boolean, default false, index), timestamps.
   - Run migration using PHP 8.2 binary:
     `& "C:\laragon\bin\php\php-8.2.30-nts-Win32-vs16-x64\php.exe" artisan migrate`

2. Eloquent Model:
   - Create `app/Models/ContactSubmission.php` with:
     * `$fillable` array containing all fields (`reference_id`, `name`, `email`, `project_type`, `coffee_offering`, `message`, `ip_address`, `user_agent`, `is_read`)
     * Laravel 11 `casts()` method casting `is_read` to boolean, `created_at` and `updated_at` to datetime
     * `booted()` model event auto-generating `SUMMON-` + uppercase random 4-6 alphanumeric string if `reference_id` is empty
     * Scopes: `scopeUnread`, `scopeRecent`, `scopeByProjectType`

3. FormRequest:
   - Create `app/Http/Requests/ContactRequest.php`:
     * `authorize(): bool` returns `true`
     * Validation rules:
       - `name`: `['required', 'string', 'max:255']`
       - `email`: `['required', 'string', 'email', 'max:255']`
       - `project_type`: `['required', 'string', Rule::in(['Full-Stack Web App', 'Creative UI/UX & Web Audio', 'High-Throughput Microservice', 'AI Agents & Automation', 'Tech Lead / Architecture Consulting', 'Other Quest'])]`
       - `coffee_offering`: `['required', 'string', 'max:255']`
       - `message`: `['required', 'string', 'min:10', 'max:5000']`
     * `messages()` providing clear custom messages matching both test expectations and UX.
     * `prepareForValidation()` trimming string inputs.

4. Controller & Routes:
   - Create `app/Http/Controllers/ContactController.php` with `store(ContactRequest $request)`:
     * Validates data via `$request->validated()`
     * Generates a unique collision-free `SUMMON-XXXX` reference ID
     * Creates `ContactSubmission` with `$request->ip()` and `$request->userAgent()`
     * Redirects `redirect()->back()->with(['flash' => ['success' => 'Tín hiệu đã được truyền đi qua màn đêm! Ma Cà Tưng sẽ hồi đáp trong thời gian sớm nhất. ☕✨', 'reference_id' => $referenceId], 'success' => 'Tín hiệu đã được truyền đi qua màn đêm! Ma Cà Tưng sẽ hồi đáp trong thời gian sớm nhất. ☕✨', 'reference_id' => $referenceId])`
   - In `routes/web.php`, add:
     `Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');`
     `Route::post('/summon', [ContactController::class, 'store'])->name('contact.summon');`
   - In `app/Http/Middleware/HandleInertiaRequests.php`, update `flash` in `share()` to safely read both nested `flash.*` and root session keys (`flash.success`, `flash.error`, `flash.reference_id`).

5. Frontend Integration:
   - Update `resources/js/Components/contact/ContactSection.vue` to use `@inertiajs/vue3` `useForm` and `usePage`.
   - Wire up `form.name`, `form.email`, `form.project_type`, `form.coffee_offering`, `form.message`.
   - Display field errors using `form.errors.name`, `form.errors.email`, `form.errors.project_type`, `form.errors.coffee_offering`, `form.errors.message`, and clear errors on input/selection.
   - On submit, call `form.post('/contact', { preserveScroll: true, onSuccess: (pageProps) => { sound.playSuccess(); confetti(...); submittedReferenceId = ...; isSubmitted = true; form.reset(); }, onError: () => { sound.playClick(); } })`.
   - Display a responsive Success Confirmation screen with the generated reference ID and a button to send another message.

6. Backend Automated Feature Tests:
   - Update / enhance `tests/Feature/ContactSubmissionTest.php` with comprehensive tests using `RefreshDatabase`:
     * `test_valid_contact_submission_persists_to_database_and_redirects`
     * `test_valid_submission_via_summon_route_alias`
     * `test_missing_required_fields_fails_validation`
     * `test_invalid_email_format_fails_validation`
     * `test_short_message_fails_minimum_length_validation`
     * `test_invalid_project_type_fails_validation`
     * `test_invalid_coffee_offering_fails_validation`
     * `test_field_maximum_length_constraints`
     * `test_long_message_within_limit_passes`
     * `test_special_characters_and_sql_strings_handled_safely`
     * `test_inertia_receives_flash_props_after_submission`

7. Verification:
   - Run: `& "C:\laragon\bin\php\php-8.2.30-nts-Win32-vs16-x64\php.exe" artisan test` (must pass 100%).
   - Run: `npm.cmd run build` (must exit 0 with 0 errors).
   - Run: `npx.cmd tsc --noEmit` (must exit 0 with 0 type errors).
   - Run: `node tests/run_all_tests.js` (must pass 100% of all 414+ tests).

8. Write full handoff report to `d:/Work/macatung/.agents/worker_m3_1/handoff.md` and report back via send_message.
