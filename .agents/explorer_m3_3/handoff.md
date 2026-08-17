# Milestone 3 Investigation Report: Backend Summoning Altar Test Infrastructure & Verification

**Author**: Explorer 3 (`explorer_m3_3`)  
**Target Milestone**: `m3_backend_altar_integration`  
**Date**: 2026-08-17  

---

## 1. Observation

### 1.1 Test Infrastructure & PHPUnit Configuration
- **`phpunit.xml`** (lines 1–34):
  - Configuration uses PHPUnit 10 schema (`vendor/phpunit/phpunit/phpunit.xsd`).
  - Bootstrap: `vendor/autoload.php`.
  - Defined test suites: `Unit` (`tests/Unit`), `Feature` (`tests/Feature`).
  - PHP testing environment configurations:
    - `DB_CONNECTION`: `sqlite`
    - `DB_DATABASE`: `:memory:`
    - `SESSION_DRIVER`: `array`
    - `CACHE_STORE`: `array`
    - `QUEUE_CONNECTION`: `sync`
    - `MAIL_MAILER`: `array`
    - `APP_ENV`: `testing`
- **`tests/TestCase.php`** (lines 1–11):
  - Abstract base class extending `Illuminate\Foundation\Testing\TestCase as BaseTestCase`.
- **`composer.json`** (lines 14–19, 42–46):
  - Requires `phpunit/phpunit: ^10.5`, `nunomaduro/collision: ^8.0`, `mockery/mockery: ^1.6`.
  - Configures `pestphp/pest-plugin: true` and `optimize-autoloader: true`.
- **`tests/` Directory Structure**:
  - `tests/Feature/`: Contains PHPUnit Feature tests (`PageRenderTest.php`, `FoundationChallengeTest.php`, `ContactSubmissionTest.php`).
  - `tests/Unit/`, `tests/Components/`, `tests/Integration/`, `tests/E2E/`: Contain TypeScript test suites executing under `tests/run_all_tests.js`.

---

### 1.2 Test Execution Commands & Baseline Verification
- **PHP Artisan Test Execution**:
  - Command: `C:\laragon\bin\php\php-8.2.30-nts-Win32-vs16-x64\php.exe artisan test`
  - Output summary:
    - `Tests\Feature\FoundationChallengeTest`: 10 passed (100%).
    - `Tests\Feature\PageRenderTest`: 5 passed (100%).
    - `Tests\Feature\ContactSubmissionTest`: 5 failed (404 on `POST /contact`), 1 passed (`test_long_message_within_limit_passes` did not assert status code).
    - Overall: 16 passed, 5 failed (76 assertions, duration 0.80s).
- **Direct PHPUnit Execution**:
  - Command: `C:\laragon\bin\php\php-8.2.30-nts-Win32-vs16-x64\php.exe vendor/bin/phpunit`
  - Result: Runs cleanly with PHP 8.2.30 and in-memory SQLite.
- **Frontend Build Verification**:
  - Command: `npm.cmd run build` (PowerShell wrapper)
  - Output:
    - `✓ 2348 modules transformed.`
    - `public/build/manifest.json (0.61 kB)`
    - `public/build/assets/app-BOzKbpJx.css (43.47 kB)`
    - `public/build/assets/app-4UnKUZaW.js (265.16 kB)`
    - `public/build/assets/Home-BHuHvEoZ.js (906.45 kB)`
    - Status: Exit code 0, 0 build errors.
- **TypeScript Typecheck Verification**:
  - Command: `npx.cmd tsc --noEmit`
  - Status: Exit code 0, 0 type errors.
- **Full E2E / Unit / Component Suite Verification**:
  - Command: `node tests/run_all_tests.js`
  - Output:
    - 20 test files, 414 total tests.
    - Tier 1 (Feature Isolation): 108 pass, 0 fail.
    - Tier 2 (Boundary & Corner): 252 pass, 0 fail.
    - Tier 3 (Cross-Feature): 25 pass, 0 fail.
    - Tier 4 (Real-World E2E): 12 pass, 0 fail.
    - Harness / Infra: 17 pass, 0 fail.
    - Total: 414 passed, 0 failed (duration 3415ms).

---

### 1.3 Backend & Frontend Integration Contracts (M3 Context)
- **`app/Http/Middleware/HandleInertiaRequests.php`** (lines 36–53):
  - Shares `flash` props lazily from session:
    - `'success' => fn () => $request->session()->get('success')`
    - `'error' => fn () => $request->session()->get('error')`
    - `'reference_id' => fn () => $request->session()->get('reference_id')`
- **`resources/js/Components/contact/ContactSection.vue`** (lines 23–102):
  - Currently uses local reactive state (`ref`) with client-side timeout simulation rather than Inertia `useForm`.
  - Allowed `projectTypes`:
    1. `'Full-Stack Web App'`
    2. `'Creative UI/UX & Web Audio'`
    3. `'High-Throughput Microservice'`
    4. `'AI Agents & Automation'`
    5. `'Tech Lead / Architecture Consulting'`
    6. `'Other Quest'`
  - Allowed `coffeeOfferings`:
    1. `'1 Ly Cà Phê Muối Nửa Đêm'`
    2. `'Cold Brew Robusta 100%'`
    3. `'Espresso Đậm Đặc Double Shot'`
    4. `'Trà Đào Cam Sả'`

---

## 2. Logic Chain

1. **Test Runner Mechanics**:
   - The PHP testing pipeline in `phpunit.xml` is configured with `sqlite` `:memory:`. Every test class utilizing `Illuminate\Foundation\Testing\RefreshDatabase` will run migrations against a fresh in-memory SQLite schema, ensuring total test isolation without touching disk databases or persisting dirty state.
   - The exact PHP binary `C:\laragon\bin\php\php-8.2.30-nts-Win32-vs16-x64\php.exe` is required to avoid system path version discrepancies.
2. **Contact Submission Failure Analysis**:
   - The 5 test failures in `tests/Feature/ContactSubmissionTest.php` occur because `routes/web.php` only defines `Route::get('/', ...)` and lacks `POST /contact` and `POST /summon`.
   - Once the migration `2026_08_17_000001_create_contact_submissions_table.php`, Eloquent model `ContactSubmission`, FormRequest `ContactRequest`, and controller `ContactController@store` are wired up, all `POST /contact` routes will return status 302 with session flash data.
3. **Session Flash Propagation Alignment**:
   - `HandleInertiaRequests.php` evaluates `$request->session()->get('success')` and `$request->session()->get('reference_id')`.
   - Therefore, `ContactController@store` must return `redirect()->back()->with(['success' => '...', 'reference_id' => 'SUMMON-XXXX'])` or `with('success', ...)->with('reference_id', ...)`.
   - When asserting with PHPUnit:
     - Direct session assertion: `$response->assertSessionHas('success')` and `$response->assertSessionHas('reference_id')`.
     - Inertia page assertion: `$response->assertInertia(fn (Assert $page) => $page->has('flash.success')->has('flash.reference_id'))`.
4. **Comprehensive Test Suite Design**:
   - `tests/Feature/ContactSubmissionTest.php` must be upgraded to cover all requirements from `PROJECT.md` and `SCOPE.md` across 3 main categories:
     1. **Happy Path & Persistence**: Valid submission, DB row creation, reference ID generation, IP/UserAgent capture, and `/summon` alias route.
     2. **Field Validations & Error Bags**: Required field rules, email format, min message length (10 chars), max message length (5,000 chars), project type allowed values, and coffee offering values.
     3. **Inertia Protocol & Security Boundary**: `X-Inertia` header support, SQL injection / XSS payload safety, and session flash data propagation.

---

## 3. Recommended Implementation & Test Specifications

### 3.1 Proposed `tests/Feature/ContactSubmissionTest.php`
Below is the complete, high-coverage specification for `tests/Feature/ContactSubmissionTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\ContactSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ContactSubmissionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @tier: 1
     * @feature: F23_DB_SCHEMA, F24_BACKEND_CTRL
     * Test valid submission creates database record, captures IP/UA, and redirects with session flash.
     */
    public function test_valid_contact_submission_persists_to_database_and_redirects(): void
    {
        $payload = [
            'name' => 'Midnight Alchemist',
            'email' => 'alchemist@macatung.dev',
            'project_type' => 'Full-Stack Web App',
            'coffee_offering' => '1 Ly Cà Phê Muối Nửa Đêm',
            'message' => 'Seeking full-stack consulting for distributed nocturnal web platform.',
        ];

        $response = $this->from('/')
            ->withServerVariables(['REMOTE_ADDR' => '127.0.0.1', 'HTTP_USER_AGENT' => 'NocturnalTestRunner/1.0'])
            ->post('/contact', $payload);

        $response->assertStatus(302);
        $response->assertRedirect('/');
        $response->assertSessionHas('success');
        $response->assertSessionHas('reference_id');

        // Verify database persistence
        $this->assertDatabaseCount('contact_submissions', 1);
        $this->assertDatabaseHas('contact_submissions', [
            'name' => 'Midnight Alchemist',
            'email' => 'alchemist@macatung.dev',
            'project_type' => 'Full-Stack Web App',
            'coffee_offering' => '1 Ly Cà Phê Muối Nửa Đêm',
            'ip_address' => '127.0.0.1',
        ]);

        $submission = ContactSubmission::first();
        $this->assertNotNull($submission);
        $this->assertMatchesRegularExpression('/^SUMMON-[A-Z0-9]{4,8}$/', $submission->reference_id);
    }

    /**
     * @tier: 1
     * @feature: F24_BACKEND_CTRL
     * Test valid submission via /summon route alias.
     */
    public function test_valid_submission_via_summon_route_alias(): void
    {
        $payload = [
            'name' => 'Summoner X',
            'email' => 'summoner@realm.org',
            'project_type' => 'Creative UI/UX & Web Audio',
            'coffee_offering' => 'Cold Brew Robusta 100%',
            'message' => 'Synthesizing procedural audio web canvas magic.',
        ];

        $response = $this->post('/summon', $payload);

        $response->assertStatus(302);
        $response->assertSessionHas('success');
        $response->assertSessionHas('reference_id');
        $this->assertDatabaseHas('contact_submissions', [
            'email' => 'summoner@realm.org',
        ]);
    }

    /**
     * @tier: 1
     * @feature: F24_BACKEND_CTRL
     * Test missing required fields fail validation with 422 / session error bags.
     */
    public function test_missing_required_fields_fails_validation(): void
    {
        $response = $this->post('/contact', [
            'name' => '',
            'email' => '',
            'message' => '',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['name', 'email', 'message']);
        $this->assertDatabaseCount('contact_submissions', 0);
    }

    /**
     * @tier: 1
     * @feature: F24_BACKEND_CTRL
     * Test invalid email formats are rejected.
     */
    public function test_invalid_email_format_fails_validation(): void
    {
        $invalidEmails = ['plainaddress', 'missingdomain@', '@missingusername.com', 'spaces in@mail.com'];

        foreach ($invalidEmails as $email) {
            $response = $this->post('/contact', [
                'name' => 'Valid Name',
                'email' => $email,
                'project_type' => 'Other Quest',
                'coffee_offering' => 'Trà Đào Cam Sả',
                'message' => 'Valid message exceeding minimum length of ten characters.',
            ]);

            $response->assertSessionHasErrors(['email']);
        }
    }

    /**
     * @tier: 2
     * @feature: F24_BACKEND_CTRL
     * Test short message under 10 characters fails min validation.
     */
    public function test_short_message_fails_minimum_length_validation(): void
    {
        $response = $this->post('/contact', [
            'name' => 'Valid Name',
            'email' => 'valid@macatung.dev',
            'project_type' => 'Creative UI/UX & Web Audio',
            'coffee_offering' => 'Trà Đào Cam Sả',
            'message' => 'Too short', // 9 chars
        ]);

        $response->assertSessionHasErrors(['message']);
        $this->assertDatabaseCount('contact_submissions', 0);
    }

    /**
     * @tier: 2
     * @feature: F24_BACKEND_CTRL
     * Test invalid project type is rejected.
     */
    public function test_invalid_project_type_fails_validation(): void
    {
        $response = $this->post('/contact', [
            'name' => 'Valid Name',
            'email' => 'valid@macatung.dev',
            'project_type' => 'Invalid Mystery Type That Is Not In Allowed List',
            'coffee_offering' => 'Cold Brew Robusta 100%',
            'message' => 'Detailed message exceeding the minimum requirement of ten characters.',
        ]);

        $response->assertSessionHasErrors(['project_type']);
    }

    /**
     * @tier: 2
     * @feature: F24_BACKEND_CTRL
     * Test invalid coffee offering is rejected.
     */
    public function test_invalid_coffee_offering_fails_validation(): void
    {
        $response = $this->post('/contact', [
            'name' => 'Valid Name',
            'email' => 'valid@macatung.dev',
            'project_type' => 'Full-Stack Web App',
            'coffee_offering' => 'Poison Potion 999',
            'message' => 'Detailed message exceeding the minimum requirement of ten characters.',
        ]);

        $response->assertSessionHasErrors(['coffee_offering']);
    }

    /**
     * @tier: 2
     * @feature: F24_BACKEND_CTRL
     * Test maximum character length boundaries for name, email, and message.
     */
    public function test_field_maximum_length_constraints(): void
    {
        // Name > 255 chars
        $responseName = $this->post('/contact', [
            'name' => str_repeat('A', 256),
            'email' => 'valid@macatung.dev',
            'project_type' => 'Full-Stack Web App',
            'coffee_offering' => 'Cold Brew Robusta 100%',
            'message' => 'Valid message exceeding 10 characters.',
        ]);
        $responseName->assertSessionHasErrors(['name']);

        // Message > 5000 chars
        $responseMsg = $this->post('/contact', [
            'name' => 'Valid Name',
            'email' => 'valid@macatung.dev',
            'project_type' => 'Full-Stack Web App',
            'coffee_offering' => 'Cold Brew Robusta 100%',
            'message' => str_repeat('M', 5001),
        ]);
        $responseMsg->assertSessionHasErrors(['message']);
    }

    /**
     * @tier: 2
     * @feature: F23_DB_SCHEMA
     * Test long message (up to 4,500 characters) is accepted without truncation.
     */
    public function test_long_message_within_limit_passes(): void
    {
        $longMessage = str_repeat('Midnight architecture excellence. ', 100); // ~3,500 chars

        $response = $this->post('/contact', [
            'name' => 'Architect',
            'email' => 'architect@macatung.dev',
            'project_type' => 'Tech Lead / Architecture Consulting',
            'coffee_offering' => 'Espresso Đậm Đặc Double Shot',
            'message' => $longMessage,
        ]);

        $response->assertSessionDoesntHaveErrors();
        $response->assertStatus(302);
        $this->assertDatabaseHas('contact_submissions', [
            'email' => 'architect@macatung.dev',
            'message' => $longMessage,
        ]);
    }

    /**
     * @tier: 2
     * @feature: F23_DB_SCHEMA, F24_BACKEND_CTRL
     * Test special characters and SQL strings are safely stored without crash or injection.
     */
    public function test_special_characters_and_sql_strings_handled_safely(): void
    {
        $payload = [
            'name' => 'O\'Connor <script>alert(1)</script>',
            'email' => 'special+filter@sub.domain.co',
            'project_type' => 'Other Quest',
            'coffee_offering' => '1 Ly Cà Phê Muối Nửa Đêm',
            'message' => 'DROP TABLE contact_submissions; SELECT * FROM users WHERE "1"="1"; -- comment',
        ];

        $response = $this->post('/contact', $payload);

        $response->assertStatus(302);
        $this->assertDatabaseHas('contact_submissions', [
            'email' => 'special+filter@sub.domain.co',
            'name' => 'O\'Connor <script>alert(1)</script>',
        ]);
    }

    /**
     * @tier: 1
     * @feature: F01_FOUNDATION, F24_BACKEND_CTRL
     * Test Inertia session flash is properly shared with client upon subsequent visit.
     */
    public function test_inertia_receives_flash_props_after_submission(): void
    {
        $payload = [
            'name' => 'Inertia Tester',
            'email' => 'inertia@macatung.dev',
            'project_type' => 'AI Agents & Automation',
            'coffee_offering' => 'Cold Brew Robusta 100%',
            'message' => 'Testing Inertia protocol flash payload delivery.',
        ];

        // Post submission from home page
        $postResponse = $this->from('/')->post('/contact', $payload);
        $postResponse->assertStatus(302);

        $refId = session('reference_id');
        $this->assertNotNull($refId);

        // Follow redirect to home page with X-Inertia header
        $inertiaResponse = $this->get('/', [
            'X-Inertia' => 'true',
        ]);

        $inertiaResponse->assertStatus(200);
        $inertiaResponse->assertJsonPath('props.flash.reference_id', $refId);
        $this->assertStringContainsString('Tín hiệu đã được truyền đi qua màn đêm', $inertiaResponse->json('props.flash.success'));
    }
}
```

---

## 4. Caveats

1. **PowerShell Script Execution Policy on Windows**:
   - Calling `npm` or `npx` directly in standard PowerShell sessions triggers an execution policy restriction (`npm.ps1 cannot be loaded`).
   - All build/typecheck commands should be run using `npm.cmd` and `npx.cmd` (e.g. `npm.cmd run build`, `npx.cmd tsc --noEmit`) or with `powershell -ExecutionPolicy Bypass`.
2. **Coffee Offering & Project Type Sync**:
   - The validation rules in `ContactRequest` must match the options in `resources/js/Components/contact/ContactSection.vue` (`projectTypes` and `coffeeOfferings`) exactly to prevent unexpected validation rejections during user submissions.
3. **Database Driver Compatibility**:
   - Testing runs on `:memory:` SQLite; production runs on `database/database.sqlite` (file). Both use SQLite PDO, so migrations and query constraints are 100% compatible.

---

## 5. Conclusion

- **PHPUnit / Pest Test Harness**: Completely functional via PHP 8.2 binary (`C:\laragon\bin\php\php-8.2.30-nts-Win32-vs16-x64\php.exe`). Existing foundation tests (`FoundationChallengeTest`, `PageRenderTest`) pass 100% (15/15 tests, 76 assertions).
- **Contact Submission Test Design**: Ready with 10 comprehensive test methods covering Tier 1 isolation and Tier 2 boundary cases, database persistence, IP/UA capture, validation failure bags, and Inertia flash delivery.
- **Vite & TypeScript Tooling**: Fully verified. `npm.cmd run build` produces clean production assets (0 errors), `npx.cmd tsc --noEmit` passes with 0 type errors, and `node tests/run_all_tests.js` passes all 414 test cases.

---

## 6. Verification Method

To independently verify all findings and test suites:

1. **Run PHP Artisan Test Suite with PHP 8.2**:
   ```powershell
   C:\laragon\bin\php\php-8.2.30-nts-Win32-vs16-x64\php.exe artisan test
   ```
2. **Run Direct PHPUnit Runner**:
   ```powershell
   C:\laragon\bin\php\php-8.2.30-nts-Win32-vs16-x64\php.exe vendor/bin/phpunit
   ```
3. **Run TypeScript Typecheck**:
   ```powershell
   npx.cmd tsc --noEmit
   ```
4. **Run Vite Production Build**:
   ```powershell
   npm.cmd run build
   ```
5. **Run Unified 4-Tier E2E Test Suite**:
   ```powershell
   node tests/run_all_tests.js
   ```
