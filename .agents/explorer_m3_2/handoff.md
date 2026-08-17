# Investigation & Design Report: Summoning Altar Controller & Inertia Form Integration (Milestone 3)

**Explorer ID**: `explorer_m3_2`  
**Milestone**: `m3_backend_altar_integration`  
**Status**: COMPLETE (Hard Handoff)  
**Date**: 2026-08-17  

---

## 1. Observation

### 1.1 Current Routing & Middleware State
- **`routes/web.php`** (lines 1–7):
  ```php
  <?php

  use Illuminate\Support\Facades\Route;
  use App\Http\Controllers\HomeController;

  Route::get('/', [HomeController::class, 'index'])->name('home');
  ```
  Only `GET /` (`HomeController@index`) is defined. No `POST /contact` or `POST /summon` endpoints exist, causing 404 on form submission.

- **`app/Http/Middleware/HandleInertiaRequests.php`** (lines 36–53):
  ```php
  public function share(Request $request): array
  {
      return array_merge(parent::share($request), [
          'appName' => config('app.name', 'Macatung Portfolio'),
          'flash' => [
              'success' => fn () => $request->session()->get('success'),
              'error' => fn () => $request->session()->get('error'),
              'reference_id' => fn () => $request->session()->get('reference_id'),
          ],
          'auth' => [
              'user' => $request->user() ? [
                  'id' => $request->user()->id,
                  'name' => $request->user()->name,
                  'email' => $request->user()->email,
              ] : null,
          ],
      ]);
  }
  ```
  `HandleInertiaRequests` is registered in `bootstrap/app.php` (line 16). The `flash` prop currently reads `session()->get('success')`, `session()->get('error')`, `session()->get('reference_id')`.

### 1.2 Existing Contact Component State
- **`resources/js/Components/contact/ContactSection.vue`** (lines 1–103, 200–330):
  - Form state is currently a local `ref({ name: '', email: '', project_type: 'Full-Stack Web App', coffee_offering: '1 Ly Cà Phê Muối Nửa Đêm', message: '' })`.
  - `handleSubmit` simulates submission using `setTimeout(..., 600)` and a randomized client-side reference ID (`SUMMON-${Math.floor(1000 + Math.random() * 9000)}`).
  - Validation errors are managed through a local `errors` ref instead of Inertia's `form.errors`.
  - `canvas-confetti` and `sound.playSuccess()` / `sound.playTalisman()` / `sound.playClick()` are imported and functional.
  - Interactive options in template:
    - **`projectTypes`**: `['Full-Stack Web App', 'Creative UI/UX & Web Audio', 'High-Throughput Microservice', 'AI Agents & Automation', 'Tech Lead / Architecture Consulting', 'Other Quest']`
    - **`coffeeOfferings`**: `['1 Ly Cà Phê Muối Nửa Đêm', 'Cold Brew Robusta 100%', 'Espresso Đậm Đặc Double Shot', 'Trà Đào Cam Sả']`

### 1.3 Test Suite Contracts & Expectations
- **`tests/Feature/ContactSubmissionTest.php`** (lines 28–30, 47, 65, 83, 101):
  - Requires `POST /contact` to return status `302` on success.
  - Requires `assertSessionHas('flash.reference_id')` and `assertSessionHas('flash.success')`.
  - Asserts error bags for `'name'`, `'email'`, `'message'`, `'project_type'`.
  - Asserts message `min:10` and `max:5000`.
  - Command `C:\laragon\bin\php\php-8.2.30-nts-Win32-vs16-x64\php.exe vendor/phpunit/phpunit/phpunit` currently results in 5 failures due to missing `/contact` route and controller.
- **`tests/Integration/SummoningAltarInertiaTest.test.ts`** (lines 31–150, 263–368):
  - Verifies `useForm` lifecycle (`isDirty`, `processing`, `wasSuccessful`, `hasErrors`, `form.errors`).
  - Verifies `flash.reference_id` prefix `SUMMON-` and Vietnamese success text `"Tín hiệu đã được truyền đi qua màn đêm"`.
  - Verifies audio triggering (`sound.playSuccess()`) and confetti invocation.

---

## 2. Logic Chain & Architecture Design

```
[User Form in ContactSection.vue]
   │  useForm({ name, email, project_type, coffee_offering, message })
   │  form.post('/contact', { preserveScroll: true })
   ▼
[Laravel Route: POST /contact / POST /summon]
   │
   ▼
[ContactRequest FormRequest Validation]
   │  - name: required|string|max:255
   │  - email: required|email|max:255
   │  - project_type: required|string|in:...
   │  - coffee_offering: required|string|max:255
   │  - message: required|string|min:10|max:5000
   │  (Fails -> 422 Redirect back with error bag -> form.errors)
   ▼
[ContactController@store]
   │  1. Generate unique reference_id: 'SUMMON-' . strtoupper(Str::random(4))
   │  2. ContactSubmission::create([...])
   │  3. return back()->with([
   │        'flash' => ['success' => '...', 'reference_id' => $refId],
   │        'success' => '...',
   │        'reference_id' => $refId,
   │     ])
   ▼
[HandleInertiaRequests Middleware]
   │  Shares props.flash = { success, error, reference_id }
   ▼
[ContactSection.vue onSuccess]
   │  - Extracts flash.reference_id
   │  - sound.playSuccess()
   │  - confetti({ ... })
   │  - form.reset()
   │  - Displays Success Overlay with verified reference_id
```

### 2.1 Backend Implementation Designs

#### A. Route Configuration (`routes/web.php`)
```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ContactController;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Summoning Altar Contact Endpoints
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
Route::post('/summon', [ContactController::class, 'store'])->name('contact.summon');
```

#### B. FormRequest Validation (`app/Http/Requests/ContactRequest.php`)
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
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'project_type' => [
                'required',
                'string',
                Rule::in([
                    'Full-Stack Web App',
                    'Creative UI/UX & Web Audio',
                    'High-Throughput Microservice',
                    'AI Agents & Automation',
                    'Tech Lead / Architecture Consulting',
                    'Other Quest',
                ]),
            ],
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
            'email.required' => 'The email field is required.',
            'email.email' => 'The email field must be a valid email address.',
            'project_type.required' => 'The project type field is required.',
            'project_type.in' => 'The selected project type is invalid.',
            'coffee_offering.required' => 'The coffee offering field is required.',
            'message.required' => 'The message field is required.',
            'message.min' => 'The message must be at least 10 characters.',
            'message.max' => 'The message may not be greater than 5000 characters.',
        ];
    }
}
```

#### C. Controller (`app/Http/Controllers/ContactController.php`)
```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Models\ContactSubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class ContactController extends Controller
{
    /**
     * Store a new contact / summoning altar submission.
     */
    public function store(ContactRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Generate unique reference_id: SUMMON-XXXX (4 alphanumeric characters)
        do {
            $referenceId = 'SUMMON-' . strtoupper(Str::random(4));
        } while (ContactSubmission::where('reference_id', $referenceId)->exists());

        // Persist record to database
        ContactSubmission::create([
            'reference_id' => $referenceId,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'project_type' => $validated['project_type'],
            'coffee_offering' => $validated['coffee_offering'],
            'message' => $validated['message'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $successMessage = 'Tín hiệu đã được truyền đi qua màn đêm! Ma Cà Tưng sẽ hồi đáp trong thời gian sớm nhất. ☕✨';

        return back()->with([
            'flash' => [
                'success' => $successMessage,
                'reference_id' => $referenceId,
            ],
            'success' => $successMessage,
            'reference_id' => $referenceId,
        ]);
    }
}
```

#### D. Inertia Middleware Optimization (`app/Http/Middleware/HandleInertiaRequests.php`)
To guarantee seamless flash retrieval whether session data is nested under `flash` or stored at the root:
```php
'flash' => [
    'success' => fn () => $request->session()->get('flash.success') ?? $request->session()->get('success'),
    'error' => fn () => $request->session()->get('flash.error') ?? $request->session()->get('error'),
    'reference_id' => fn () => $request->session()->get('flash.reference_id') ?? $request->session()->get('reference_id'),
],
```

---

### 2.2 Frontend `ContactSection.vue` Design

#### Proposed `ContactSection.vue` Architecture:
```vue
<script setup lang="ts">
import { useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import confetti from 'canvas-confetti';
import { sound } from '@/audio/soundEffects';
import Icons from '@/Components/ui/Icons.vue';

const projectTypes = [
  'Full-Stack Web App',
  'Creative UI/UX & Web Audio',
  'High-Throughput Microservice',
  'AI Agents & Automation',
  'Tech Lead / Architecture Consulting',
  'Other Quest',
];

const coffeeOfferings = [
  '1 Ly Cà Phê Muối Nửa Đêm',
  'Cold Brew Robusta 100%',
  'Espresso Đậm Đặc Double Shot',
  'Trà Đào Cam Sả',
];

const form = useForm({
  name: '',
  email: '',
  project_type: 'Full-Stack Web App',
  coffee_offering: '1 Ly Cà Phê Muối Nửa Đêm',
  message: '',
});

const page = usePage();
const copySuccess = ref(false);
const submittedReferenceId = ref<string>('');
const isSubmitted = ref(false);

const copyEmail = async () => {
  try {
    if (typeof navigator !== 'undefined' && navigator.clipboard) {
      await navigator.clipboard.writeText('dev@macatung.dev');
    }
    copySuccess.value = true;
    sound.playClick();
    setTimeout(() => {
      copySuccess.value = false;
    }, 2500);
  } catch {
    // Fallback
  }
};

const handleSubmit = () => {
  if (form.processing) return;

  sound.playTalisman();

  form.post('/contact', {
    preserveScroll: true,
    onSuccess: (pageProps: any) => {
      const flash = pageProps?.props?.flash || (page.props as any)?.flash;
      const refId = flash?.reference_id || 'SUMMON-0000';
      submittedReferenceId.value = refId;
      isSubmitted.value = true;

      sound.playSuccess();

      try {
        confetti({
          particleCount: 80,
          spread: 70,
          origin: { y: 0.6 },
          colors: ['#00f5a0', '#ffd166', '#ff0054'],
        });
      } catch {
        // Fallback for headless environments
      }

      form.reset();
    },
    onError: () => {
      sound.playClick();
    },
  });
};

const resetForm = () => {
  form.reset();
  form.clearErrors();
  isSubmitted.value = false;
  submittedReferenceId.value = '';
};
</script>

<template>
  <section id="contact" class="scroll-mt-24 w-full py-16 sm:py-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto text-left">
    <!-- Header -->
    <div class="flex flex-col items-start mb-12">
      <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-phantom-mint/10 border border-phantom-mint/30 text-phantom-mint text-xs font-mono mb-3 shadow-glow-mint">
        🔮 Summoning Altar
      </span>
      <h2 class="text-3xl sm:text-5xl font-display font-extrabold text-white tracking-tight">
        Bàn Thờ <span class="text-transparent bg-clip-text bg-gradient-to-r from-phantom-mint via-phantom-cyan to-talisman-gold">Triệu Hồi</span>
      </h2>
      <p class="text-sm sm:text-base text-slate-400 mt-2 max-w-2xl font-sans">
        Gửi tín hiệu triệu hồi qua màn đêm để khởi động dự án mới, hợp tác kiến trúc hoặc đơn giản là mời một ly Robusta 0-bug.
      </p>
    </div>

    <!-- Altar Grid: 2 Columns -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
      <!-- Left Column: Direct Channels & Ritual Guidelines -->
      <div class="lg:col-span-5 flex flex-col gap-6">
        <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-white/10 text-left">
          <h3 class="font-display font-bold text-lg sm:text-xl text-white mb-2">Kênh Thần Giao Cách Cảm</h3>
          <p class="text-xs sm:text-sm text-slate-400 font-sans leading-relaxed mb-6">
            Mọi thông điệp gửi đến đều được xử lý trực tiếp bởi Alchemist trong khung giờ 00:00 - 05:00 AM.
          </p>

          <!-- Email Copy Pill -->
          <div class="p-4 rounded-2xl bg-midnight-950/80 border border-white/10 flex items-center justify-between gap-3 mb-6">
            <div class="flex items-center gap-3 min-w-0">
              <div class="w-10 h-10 rounded-xl bg-midnight-900 border border-white/10 flex items-center justify-center text-phantom-mint shrink-0">
                <Icons name="Mail" :size="18" />
              </div>
              <div class="min-w-0">
                <div class="text-[10px] font-mono text-slate-400 uppercase tracking-wider">Email Trực Tiếp</div>
                <div class="font-mono text-xs sm:text-sm font-bold text-white truncate">dev@macatung.dev</div>
              </div>
            </div>
            <button
              type="button"
              class="px-3 py-2 rounded-xl bg-white/5 hover:bg-phantom-mint hover:text-midnight-950 text-slate-300 text-xs font-mono font-semibold transition-all shrink-0 min-h-[38px] flex items-center gap-1.5"
              @click="copyEmail"
            >
              <Icons v-if="copySuccess" name="Check" :size="14" />
              <Icons v-else name="Copy" :size="14" />
              <span>{{ copySuccess ? 'Đã Copy' : 'Copy' }}</span>
            </button>
          </div>

          <!-- Status & Availability -->
          <div class="space-y-3 text-xs font-mono">
            <div class="flex items-center justify-between p-3 rounded-xl bg-midnight-950/50 border border-white/5">
              <span class="text-slate-400">Realm:</span>
              <span class="text-slate-200 font-semibold">GMT+7 (Midnight Zone)</span>
            </div>
            <div class="flex items-center justify-between p-3 rounded-xl bg-midnight-950/50 border border-white/5">
              <span class="text-slate-400">Trạng Thái:</span>
              <span class="text-phantom-mint font-semibold flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-phantom-mint animate-pulse" />
                Sẵn Sàng Nhận Quest
              </span>
            </div>
            <div class="flex items-center justify-between p-3 rounded-xl bg-midnight-950/50 border border-white/5">
              <span class="text-slate-400">Thời Gian Hồi Đáp:</span>
              <span class="text-talisman-gold font-semibold">&lt; 24 Giờ</span>
            </div>
          </div>
        </div>

        <!-- Social Runes -->
        <div class="glass-panel p-6 rounded-3xl border border-white/10">
          <div class="text-xs font-mono text-slate-400 uppercase tracking-wider mb-3">Mạng Lưới Kỹ Thuật Số</div>
          <div class="flex flex-wrap gap-2.5">
            <a
              href="https://github.com/macatung"
              target="_blank"
              rel="noopener noreferrer"
              class="px-4 py-2.5 rounded-xl bg-midnight-900 border border-white/10 hover:border-phantom-mint text-slate-300 hover:text-white text-xs font-mono transition-all flex items-center gap-2 min-h-[44px]"
            >
              <Icons name="Github" :size="15" />
              <span>GitHub</span>
            </a>
            <a
              href="https://linkedin.com"
              target="_blank"
              rel="noopener noreferrer"
              class="px-4 py-2.5 rounded-xl bg-midnight-900 border border-white/10 hover:border-phantom-mint text-slate-300 hover:text-white text-xs font-mono transition-all flex items-center gap-2 min-h-[44px]"
            >
              <Icons name="ExternalLink" :size="15" />
              <span>LinkedIn</span>
            </a>
          </div>
        </div>
      </div>

      <!-- Right Column: Summoning Form -->
      <div class="lg:col-span-7">
        <div class="glass-panel p-6 sm:p-8 rounded-3xl border border-white/10 text-left relative">
          <!-- Success Overlay -->
          <div
            v-if="isSubmitted || form.wasSuccessful"
            class="p-8 text-center flex flex-col items-center justify-center space-y-4 min-h-[400px]"
          >
            <div class="w-16 h-16 rounded-full bg-phantom-mint/10 border-2 border-phantom-mint flex items-center justify-center text-3xl shadow-glow-mint">
              ✨
            </div>
            <h3 class="text-2xl font-display font-bold text-white">Triệu Hồi Thành Công!</h3>
            <p class="text-sm text-slate-300 max-w-md leading-relaxed font-sans">
              Tín hiệu đã được truyền đi qua màn đêm. Mã biên nhận: <span class="font-mono text-phantom-mint font-bold">{{ submittedReferenceId || ($page.props.flash as any)?.reference_id || 'SUMMON-XXXX' }}</span>. Alchemist sẽ hồi đáp bạn sớm nhất!
            </p>
            <button
              type="button"
              class="mt-4 px-6 py-3 rounded-xl bg-midnight-800 border border-white/10 hover:border-phantom-mint text-white font-mono text-xs font-bold transition-all min-h-[44px]"
              @click="resetForm"
            >
              Gửi Thêm Lời Triệu Hồi Khác
            </button>
          </div>

          <!-- The Form -->
          <form v-else class="space-y-5" @submit.prevent="handleSubmit">
            <!-- Name & Email Inputs -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-mono text-slate-300 uppercase tracking-wider mb-1.5">
                  1. Tên Lữ Khách / Kỹ Sư <span class="text-rose-400">*</span>
                </label>
                <input
                  v-model="form.name"
                  type="text"
                  placeholder="e.g. Alchemist Tưng"
                  class="w-full px-4 py-3 rounded-xl bg-midnight-900 border border-white/10 text-white font-sans text-sm placeholder-slate-600 focus:border-phantom-mint focus:outline-none min-h-[44px] transition-colors"
                  :class="{ 'border-rose-500/80': form.errors.name }"
                  @input="form.clearErrors('name')"
                />
                <span v-if="form.errors.name" class="text-[11px] font-mono text-rose-400 mt-1 block">{{ form.errors.name }}</span>
              </div>

              <div>
                <label class="block text-xs font-mono text-slate-300 uppercase tracking-wider mb-1.5">
                  2. Địa Chỉ Thần Giao (Email) <span class="text-rose-400">*</span>
                </label>
                <input
                  v-model="form.email"
                  type="email"
                  placeholder="e.g. yourname@domain.com"
                  class="w-full px-4 py-3 rounded-xl bg-midnight-900 border border-white/10 text-white font-sans text-sm placeholder-slate-600 focus:border-phantom-mint focus:outline-none min-h-[44px] transition-colors"
                  :class="{ 'border-rose-500/80': form.errors.email }"
                  @input="form.clearErrors('email')"
                />
                <span v-if="form.errors.email" class="text-[11px] font-mono text-rose-400 mt-1 block">{{ form.errors.email }}</span>
              </div>
            </div>

            <!-- Project Type Selector -->
            <div>
              <label class="block text-xs font-mono text-slate-300 uppercase tracking-wider mb-2">
                3. Loại Quest / Nhiệm Vụ
              </label>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                <button
                  v-for="type in projectTypes"
                  :key="type"
                  type="button"
                  class="p-2.5 rounded-xl border text-left text-xs font-sans font-medium transition-all min-h-[44px] flex items-center justify-between"
                  :class="form.project_type === type
                    ? 'bg-midnight-800 border-phantom-mint text-phantom-mint shadow-glow-mint'
                    : 'bg-midnight-950/60 border-white/5 hover:border-white/20 text-slate-400 hover:text-slate-200'"
                  @click="form.project_type = type; form.clearErrors('project_type'); sound.playClick()"
                >
                  <span class="truncate">{{ type }}</span>
                  <span v-if="form.project_type === type" class="text-phantom-mint font-bold">✓</span>
                </button>
              </div>
              <span v-if="form.errors.project_type" class="text-[11px] font-mono text-rose-400 mt-1 block">{{ form.errors.project_type }}</span>
            </div>

            <!-- Coffee Offering -->
            <div>
              <label class="block text-xs font-mono text-slate-300 uppercase tracking-wider mb-2">
                4. Lễ Vật Cà Phê Tiếp Sức
              </label>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                <button
                  v-for="coffee in coffeeOfferings"
                  :key="coffee"
                  type="button"
                  class="p-2.5 rounded-xl border text-left text-xs font-sans transition-all min-h-[44px] flex items-center justify-between"
                  :class="form.coffee_offering === coffee
                    ? 'bg-midnight-800 border-talisman-gold text-talisman-gold shadow-glow-talisman'
                    : 'bg-midnight-950/60 border-white/5 hover:border-white/20 text-slate-400 hover:text-slate-200'"
                  @click="form.coffee_offering = coffee; form.clearErrors('coffee_offering'); sound.playClick()"
                >
                  <span class="truncate">☕ {{ coffee }}</span>
                  <span v-if="form.coffee_offering === coffee" class="text-talisman-gold font-bold">✓</span>
                </button>
              </div>
              <span v-if="form.errors.coffee_offering" class="text-[11px] font-mono text-rose-400 mt-1 block">{{ form.errors.coffee_offering }}</span>
            </div>

            <!-- Message Detail Input -->
            <div>
              <label class="block text-xs font-mono text-slate-300 uppercase tracking-wider mb-1.5">
                5. Chi Tiết Nhiệm Vụ & Kỳ Vọng <span class="text-rose-400">*</span>
              </label>
              <textarea
                v-model="form.message"
                rows="4"
                placeholder="Mô tả mục tiêu dự án, thời gian dự kiến và bất kỳ yêu cầu kiến trúc đặc thù nào..."
                class="w-full p-4 rounded-xl bg-midnight-900 border border-white/10 text-white font-sans text-sm placeholder-slate-600 focus:border-phantom-mint focus:outline-none transition-colors"
                :class="{ 'border-rose-500/80': form.errors.message }"
                @input="form.clearErrors('message')"
              />
              <span v-if="form.errors.message" class="text-[11px] font-mono text-rose-400 mt-1 block">{{ form.errors.message }}</span>
            </div>

            <!-- Submit Button -->
            <button
              type="submit"
              class="w-full py-4 rounded-2xl font-display font-extrabold text-sm sm:text-base transition-all shadow-xl flex items-center justify-center gap-2 min-h-[52px]"
              :class="form.processing
                ? 'bg-slate-700 text-slate-400 cursor-not-allowed animate-pulse'
                : 'bg-gradient-to-r from-phantom-mint via-phantom-cyan to-talisman-gold text-midnight-950 hover:brightness-110 active:scale-[0.99] shadow-glow-mint'"
              :disabled="form.processing"
            >
              <span v-if="form.processing">⏳ Đang Truyền Tín Hiệu...</span>
              <span v-else>🚀 Khởi Động Triệu Hồi Ma Cà Tưng</span>
            </button>
          </form>
        </div>
      </div>
    </div>
  </section>
</template>
```

---

## 3. Caveats

1. **PHP CLI Environment in Windows**:
   - `php artisan test` may throw an environment variable block size exception on Windows due to Symfony Process env serialization.
   - Independent verification should use direct invocation: `C:\laragon\bin\php\php-8.2.30-nts-Win32-vs16-x64\php.exe vendor/phpunit/phpunit/phpunit`.
2. **Session Flash Shape**:
   - Both `$request->session()->flash('flash.reference_id', ...)` / nested `flash` array and root keys `'success'`, `'reference_id'` must be populated to satisfy both PHPUnit `assertSessionHas('flash.reference_id')` and Inertia shared middleware props.
3. **Database Migration Co-Dependency**:
   - The controller requires table `contact_submissions` created by Explorer 1 / Developer (`database/migrations/2026_08_17_000001_create_contact_submissions_table.php`) and model `App\Models\ContactSubmission`.

---

## 4. Conclusion

The architecture for Milestone 3 Summoning Altar backend & Inertia integration is fully analyzed and designed:
1. **Routes**: `POST /contact` and `POST /summon` map to `ContactController@store`.
2. **FormRequest**: `ContactRequest` applies strict validation (name, email, project_type enum, coffee_offering, message min:10, max:5000) with custom error messages.
3. **Controller**: `ContactController@store` generates collision-free `SUMMON-XXXX` reference IDs, creates `ContactSubmission` records with IP and User Agent, and redirects back with session flash data.
4. **Middleware**: `HandleInertiaRequests` seamlessly shares `flash.success`, `flash.error`, and `flash.reference_id` with Vue.
5. **Frontend**: `ContactSection.vue` leverages `@inertiajs/vue3` `useForm`, binds inputs, clears errors on user edit, displays server errors, manages `processing` disabled state, plays audio (`sound.playSuccess()`), pops confetti, and displays the returned reference ID in a dedicated success overlay.

All designs are 100% compliant with existing test contracts in `ContactSubmissionTest.php` and `SummoningAltarInertiaTest.test.ts`.

---

## 5. Verification Method

### 5.1 Independent Test Commands

1. **PHPUnit Feature Tests**:
   ```powershell
   C:\laragon\bin\php\php-8.2.30-nts-Win32-vs16-x64\php.exe vendor/phpunit/phpunit/phpunit tests/Feature/ContactSubmissionTest.php
   ```
   *Expected*: All 5 tests pass (100%).

2. **Frontend & E2E Test Suite**:
   ```powershell
   node tests/run_all_tests.js
   ```
   *Expected*: All 414 tests pass (100%).

3. **Vite Production Build**:
   ```powershell
   npm run build
   ```
   *Expected*: Vite builds cleanly without TypeScript or Vue compilation errors.

### 5.2 Invalidation Conditions
- If `ContactRequest` project_type enum fails to match frontend options list, validation tests will fail.
- If `reference_id` does not begin with `SUMMON-`, `SummoningAltarInertiaTest.test.ts` `[T1_F24_02]` will fail.
- If `flash.reference_id` or `flash.success` is omitted from session/props, `ContactSubmissionTest` line 29-30 will fail.
