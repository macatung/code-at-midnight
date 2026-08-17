# Scope: Milestone 3 — Summoning Altar Backend & Integration (`m3_backend_altar_integration`)

## Architecture
- Backend: Laravel 11 with SQLite database.
- Eloquent Model: `ContactSubmission` mapping table `contact_submissions`.
- Request Validation: `ContactRequest` with strict rules and custom messages.
- Controller: `ContactController` with `store` action generating reference ID (e.g. `SUMMON-XXXX`), saving to SQLite, redirecting back with Inertia flash data.
- Inertia Middleware: `HandleInertiaRequests` sharing `flash.success`, `flash.error`, `flash.reference_id`.
- Routes: `POST /contact` (`contact.store`) and `POST /summon` (`contact.summon`).
- Frontend: `resources/js/Components/contact/ContactSection.vue` updated with `@inertiajs/vue3` `useForm`, loading state, validation error display, success banner with reference ID, audio feedback (`sound.playSuccess()`), and confetti trigger.
- Automated Tests: PHPUnit feature tests in `tests/Feature/ContactSubmissionTest.php` and `tests/Feature/AdversarialContactTest.php`.

## Feature Inventory
| # | Feature | Description | Milestone | Status |
|---|---------|-------------|-----------|--------|
| 1 | Database Migration | `contact_submissions` table (name, email, project_type, coffee_offering, message, ip_address, user_agent, timestamps) | M3 | DONE |
| 2 | Eloquent Model | `ContactSubmission` with fillable attributes, scopes, casts | M3 | DONE |
| 3 | FormRequest | `ContactRequest` validating fields, min:10 on message, custom messages | M3 | DONE |
| 4 | Controller & Routes | `ContactController@store` generating reference_id, `POST /contact`, `POST /summon` | M3 | DONE |
| 5 | Inertia Flash Middleware | `HandleInertiaRequests` sharing `flash` state | M3 | DONE |
| 6 | Frontend Integration | `ContactSection.vue` using `useForm`, error handling, reference_id display, sound/confetti | M3 | DONE |
| 7 | Backend Feature Tests | `tests/Feature/ContactSubmissionTest.php` with 100% pass | M3 | DONE |

## Interface Contracts
### `ContactRequest` Validation Rules:
- `name`: `required|string|max:255`
- `email`: `required|email|max:255`
- `project_type`: `required|string|in:Full-Stack Web App,Creative UI/UX & Web Audio,High-Throughput Microservice,AI Agents & Automation,Tech Lead / Architecture Consulting,Other Quest`
- `coffee_offering`: `required|string|max:255`
- `message`: `required|string|min:10|max:5000`

### `ContactController@store` Response:
- Redirect back with session flash:
  * `flash`: `['success' => '...', 'reference_id' => 'SUMMON-XXXXXX']`
  * `success`: "Tín hiệu đã được truyền đi qua màn đêm! Ma Cà Tưng sẽ hồi đáp trong thời gian sớm nhất. ☕✨"
  * `reference_id`: "SUMMON-XXXXXX"

### Frontend `ContactSection.vue`:
- Submit form via `form.post('/contact', { preserveScroll: true, onSuccess: () => { ... } })`
- Display field validation errors dynamically from `form.errors`
- Display success flash alert containing `reference_id` upon successful submission.
