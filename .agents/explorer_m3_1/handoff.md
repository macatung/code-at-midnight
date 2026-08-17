# Handoff Report: Milestone 3 Database Schema, Model & Validation Architecture

- **Author**: Explorer 1 (`explorer_m3_1`)
- **Milestone**: Milestone 3 (`m3_backend_altar_integration`)
- **Target**: Sub-Orchestrator M3 (`sub_orch_m3`) & Implementation Agents

---

## 1. Observation

### A. Database Configurations & Existing Migrations
1. **Database Configuration (`config/database.php` lines 7-17)**:
   - Default connection: `env('DB_CONNECTION', 'sqlite')`.
   - SQLite database path: `env('DB_DATABASE', database_path('database.sqlite'))` with `foreign_key_constraints` enabled by default.
2. **Environment Configuration (`.env` lines 12-13)**:
   - `DB_CONNECTION=sqlite`
   - `DB_DATABASE=d:/Work/macatung/database/database.sqlite`
3. **PHPUnit Testing Environment (`phpunit.xml` lines 25-26)**:
   - `<env name="DB_CONNECTION" value="sqlite"/>`
   - `<env name="DB_DATABASE" value=":memory:"/>`
4. **Existing Migration Files in `database/migrations/`**:
   - `0001_01_01_000000_create_users_table.php` (creates `users`, `password_reset_tokens`, `sessions`).
   - `0001_01_01_000001_create_cache_table.php` (creates `cache`, `cache_locks`).
   - Both migrations have been executed (`migrate:status` shows Batch [1] Ran).

### B. Existing Model Structure & Laravel 11 Patterns
1. **User Model (`app/Models/User.php`)**:
   - Uses Laravel 11 style method-based casts: `protected function casts(): array { return [...]; }`.
   - Uses `$fillable = ['name', 'email', 'password'];` and `$hidden = ['password', 'remember_token'];`.
   - Extends `Illuminate\Foundation\Auth\User as Authenticatable`.

### C. Test Expectations & Interface Contracts
1. **Feature Test Suite (`tests/Feature/ContactSubmissionTest.php`)**:
   - `test_valid_contact_submission_persists_and_redirects`: Submits payload to `POST /contact` and asserts status 302, `flash.reference_id` in session, and `flash.success` in session.
   - `test_missing_required_fields_fails_validation`: Asserts session errors for `name`, `email`, `message`.
   - `test_invalid_email_format_fails_validation`: Asserts session errors for `email`.
   - `test_short_message_fails_minimum_length_validation`: Asserts message under 10 chars fails with session errors for `message`.
   - `test_invalid_project_type_fails_validation`: Asserts project type not in allowed set fails with session error for `project_type`.
   - `test_long_message_within_limit_passes`: Asserts long message (3,500+ chars up to 5,000) passes validation.
2. **Inertia Integration Suite (`tests/Integration/SummoningAltarInertiaTest.test.ts`)**:
   - `[T1_F24_02]`: Flash response contains unique `reference_id` starting with `"SUMMON-"`.
   - `[T1_F24_03]`: Flash response contains Vietnamese message containing `"Tín hiệu đã được truyền đi qua màn đêm"`.
   - `[T2_F24_01]`: Error message for short text contains `"at least 10 characters"`.
   - `[T2_F24_02]`: Error message for invalid email contains `"valid email address"`.
   - `[T2_F24_03]`: Allowed project types are:
     * `'Full-Stack Web App'`
     * `'Creative UI/UX & Web Audio'`
     * `'High-Throughput Microservice'`
     * `'AI Agents & Automation'`
     * `'Tech Lead / Architecture Consulting'`
     * `'Other Quest'`
3. **Frontend Altar Component (`resources/js/Components/contact/ContactSection.vue`)**:
   - `projectTypes`: 6 predefined strings matching the contract above.
   - `coffeeOfferings`: 4 predefined suggestions (`'1 Ly Cà Phê Muối Nửa Đêm'`, `'Cold Brew Robusta 100%'`, `'Espresso Đậm Đặc Double Shot'`, `'Trà Đào Cam Sả'`), but tests also supply free-form strings like `'Cà phê muối 2 shot'`, `'Espresso'`, `'Cold brew'`, `'Robusta'`, `'Drip Coffee'`.
   - Displays receipt code: `SUMMON-XXXX`.
4. **Shared Inertia Props (`app/Http/Middleware/HandleInertiaRequests.php` lines 40-44)**:
   - Shares `flash` array (`success`, `error`, `reference_id`).

---

## 2. Logic Chain

1. **Database Schema Design**:
   - SQLite stores strings and texts without strict fixed-width limits, but adhering to database typing conventions ensures portability to MySQL/Postgres.
   - The table must store:
     * `id`: Auto-incrementing primary key.
     * `reference_id`: String (e.g. `SUMMON-A7B9` or `SUMMON-8492`), unique index for fast lookups.
     * `name`: String (max 255), required.
     * `email`: String (max 255), indexed for querying.
     * `project_type`: String (max 255), required.
     * `coffee_offering`: String (max 255), required.
     * `message`: Text column (supporting up to 5,000 characters without truncation).
     * `ip_address`: Nullable string(45) for telemetry/audit logging.
     * `user_agent`: Nullable text for telemetry/browser detection.
     * `is_read`: Boolean default `false` for admin status tracking.
     * `timestamps`: `created_at` and `updated_at`.
2. **Eloquent Model Design**:
   - Following Laravel 11 conventions (`casts(): array` method), `ContactSubmission` should declare `$fillable` for all user-provided fields plus metadata.
   - Implement an automated `booted()` model event or helper `generateReferenceId()` that assigns a unique `SUMMON-XXXX` identifier on creation if not explicitly provided.
   - Cast `is_read` to boolean and timestamps to datetime.
   - Add query scopes: `scopeUnread()`, `scopeRecent()`, and `scopeByProjectType()`.
3. **FormRequest Validation Rules & Messages**:
   - `name`: `['required', 'string', 'max:255']`
   - `email`: `['required', 'email', 'max:255']` (standard RFC email format, avoiding network-dependent DNS checks in test environments).
   - `project_type`: `['required', 'string', Rule::in([...])]` covering the exact 6 project types.
   - `coffee_offering`: `['required', 'string', 'max:255']` to accept both UI presets and test payload variants.
   - `message`: `['required', 'string', 'min:10', 'max:5000']`.
   - Custom messages must satisfy both standard Laravel wording and test expectations (`"The message must be at least 10 characters."`, `"The email field must be a valid email address."`, `"The name field is required."`).
4. **Session Flash Interoperability**:
   - In `ContactSubmissionTest.php`, assertions check `$response->assertSessionHas('flash.reference_id')` and `$response->assertSessionHas('flash.success')`.
   - In `HandleInertiaRequests.php`, Inertia extracts `flash.success`, `flash.error`, and `flash.reference_id`.
   - Therefore, the controller should flash the session with both nested array key `'flash'` and flat keys, or `HandleInertiaRequests.php` should evaluate `$request->session()->get('flash.success') ?? $request->session()->get('success')`.

---

## 3. Caveats

1. **In-Memory SQLite Migration in Tests**:
   - In `tests/Feature/ContactSubmissionTest.php`, `use Illuminate\Foundation\Testing\RefreshDatabase;` is imported on line 6, but the trait `use RefreshDatabase;` is not declared inside the class body. The implementer should ensure `use RefreshDatabase;` is active inside the test class so `:memory:` SQLite schema runs migrations during test execution.
2. **Email Validation Strategy**:
   - Do NOT use `email:rfc,dns` in validation rules because automated unit/feature tests run in hermetic/offline environments where DNS lookups will fail for dummy domains like `@macatung.dev` or `@nightowl.dev`. Use `'email'` or `'email:rfc'`.
3. **Coffee Offering Flexibility**:
   - Do not restrict `coffee_offering` to an overly rigid `in:` enum because the tests use different text variants (`'Cà phê muối 2 shot'`, `'Espresso'`, `'Cold brew'`, `'Drip Coffee'`) while the UI provides 4 presets. Use `required|string|max:255`.

---

## 4. Conclusion & Recommended Implementations

### A. Exact Migration Definition
**File**: `database/migrations/2026_08_17_000001_create_contact_submissions_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contact_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('reference_id', 32)->unique()->index();
            $table->string('name', 255);
            $table->string('email', 255)->index();
            $table->string('project_type', 255);
            $table->string('coffee_offering', 255);
            $table->text('message');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->boolean('is_read')->default(false)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_submissions');
    }
};
```

---

### B. Exact Eloquent Model Definition
**File**: `app/Models/ContactSubmission.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ContactSubmission extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'contact_submissions';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'reference_id',
        'name',
        'email',
        'project_type',
        'coffee_offering',
        'message',
        'ip_address',
        'user_agent',
        'is_read',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function (ContactSubmission $submission) {
            if (empty($submission->reference_id)) {
                $submission->reference_id = self::generateReferenceId();
            }
        });
    }

    /**
     * Generate a unique Summoning Altar reference ID.
     */
    public static function generateReferenceId(): string
    {
        return 'SUMMON-' . strtoupper(Str::random(6));
    }

    /**
     * Scope a query to only include unread submissions.
     */
    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope a query to order by most recent submissions.
     */
    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Scope a query to filter by project type.
     */
    public function scopeByProjectType(Builder $query, string $type): Builder
    {
        return $query->where('project_type', $type);
    }
}
```

---

### C. Exact FormRequest Definition
**File**: `app/Http/Requests/ContactRequest.php`

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContactRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Allowed project types list.
     *
     * @var list<string>
     */
    public const ALLOWED_PROJECT_TYPES = [
        'Full-Stack Web App',
        'Creative UI/UX & Web Audio',
        'High-Throughput Microservice',
        'AI Agents & Automation',
        'Tech Lead / Architecture Consulting',
        'Other Quest',
    ];

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'project_type' => ['required', 'string', Rule::in(self::ALLOWED_PROJECT_TYPES)],
            'coffee_offering' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'name.max' => 'The name field must not exceed 255 characters.',
            'email.required' => 'The email field is required.',
            'email.email' => 'The email field must be a valid email address.',
            'email.max' => 'The email field must not exceed 255 characters.',
            'project_type.required' => 'The project type field is required.',
            'project_type.in' => 'The selected project type is invalid.',
            'coffee_offering.required' => 'The coffee offering field is required.',
            'coffee_offering.max' => 'The coffee offering must not exceed 255 characters.',
            'message.required' => 'The message field is required.',
            'message.min' => 'The message must be at least 10 characters.',
            'message.max' => 'The message must not exceed 5000 characters.',
        ];
    }

    /**
     * Prepare data for validation (trim whitespace).
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => is_string($this->name) ? trim($this->name) : $this->name,
            'email' => is_string($this->email) ? trim($this->email) : $this->email,
            'message' => is_string($this->message) ? trim($this->message) : $this->message,
        ]);
    }
}
```

---

### D. Recommended Controller Implementation Guidelines
**File**: `app/Http/Controllers/ContactController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Models\ContactSubmission;
use Illuminate\Http\RedirectResponse;

class ContactController extends Controller
{
    /**
     * Store a newly created contact submission in storage.
     */
    public function store(ContactRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        
        $submission = ContactSubmission::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'project_type' => $validated['project_type'],
            'coffee_offering' => $validated['coffee_offering'],
            'message' => $validated['message'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $successMessage = 'Tín hiệu đã được truyền đi qua màn đêm! Ma Cà Tưng sẽ hồi đáp trong thời gian sớm nhất. ☕✨';

        return redirect()->back()
            ->with('flash', [
                'success' => $successMessage,
                'reference_id' => $submission->reference_id,
            ])
            ->with('success', $successMessage)
            ->with('reference_id', $submission->reference_id);
    }
}
```

---

### E. Recommended Route Registration
**File**: `routes/web.php`

```php
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
Route::post('/summon', [ContactController::class, 'store'])->name('contact.summon');
```

---

## 5. Verification Method

1. **Run Migration**:
   ```bash
   C:\laragon\bin\php\php-8.2.30-nts-Win32-vs16-x64\php.exe artisan migrate
   ```
2. **Execute PHPUnit Feature Tests**:
   ```bash
   C:\laragon\bin\php\php-8.2.30-nts-Win32-vs16-x64\php.exe artisan test --filter=ContactSubmissionTest
   ```
3. **Execute Full PHP Feature Testsuite**:
   ```bash
   C:\laragon\bin\php\php-8.2.30-nts-Win32-vs16-x64\php.exe artisan test --testsuite=Feature
   ```
4. **Execute TypeScript Harness Suites**:
   ```bash
   node tests/run_all_tests.js
   ```
   Verifying that `SummoningAltarInertiaTest` and `Scenarios_07_to_12` pass 100%.
