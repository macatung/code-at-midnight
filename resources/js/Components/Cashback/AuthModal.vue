<script setup lang="ts">
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import Icons from '@/Components/ui/Icons.vue';

const props = defineProps<{
  show: boolean;
  initialMode?: 'login' | 'register';
}>();

const emit = defineEmits<{
  (e: 'close'): void;
  (e: 'success'): void;
}>();

const mode = ref<'login' | 'register'>(props.initialMode || 'login');
const errorMessage = ref('');

const loginForm = useForm({
  email: '',
  password: '',
  remember: true,
});

const registerForm = useForm({
  name: '',
  email: '',
  password: '',
});

// Dynamic endpoint resolution (subdomain vs fallback path /hoantien)
const getEndpoint = (endpoint: string) => {
  const isSubdomain = typeof window !== 'undefined' && window.location.hostname.startsWith('hoantien.');
  return isSubdomain ? `/${endpoint}` : `/hoantien/${endpoint}`;
};

const handleLogin = () => {
  errorMessage.value = '';
  loginForm.post(getEndpoint('auth/login'), {
    preserveScroll: true,
    onSuccess: () => {
      loginForm.reset('password');
      emit('success');
      emit('close');
      router.reload();
    },
    onError: (errors) => {
      if (errors.email) errorMessage.value = errors.email;
      else if (errors.password) errorMessage.value = errors.password;
      else errorMessage.value = 'Đăng nhập không thành công. Vui lòng kiểm tra lại thông tin.';
    },
  });
};

const handleRegister = () => {
  errorMessage.value = '';
  registerForm.post(getEndpoint('auth/register'), {
    preserveScroll: true,
    onSuccess: () => {
      registerForm.reset('password');
      emit('success');
      emit('close');
      router.reload();
    },
    onError: (errors) => {
      if (errors.email) errorMessage.value = errors.email;
      else if (errors.name) errorMessage.value = errors.name;
      else if (errors.password) errorMessage.value = errors.password;
      else errorMessage.value = 'Đăng ký không thành công. Vui lòng thử lại.';
    },
  });
};

const switchMode = (target: 'login' | 'register') => {
  mode.value = target;
  errorMessage.value = '';
  loginForm.clearErrors();
  registerForm.clearErrors();
};
</script>

<template>
  <div
    v-if="show"
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-md animate-in fade-in duration-200"
  >
    <div
      class="bg-midnight-900/95 border border-white/15 rounded-2xl p-5 sm:p-6 max-w-md w-full shadow-2xl space-y-4 relative overflow-hidden backdrop-blur-xl"
    >
      <!-- Background Ambient Glow -->
      <div class="absolute -top-20 -right-20 w-48 h-48 bg-phantom-mint/10 rounded-full blur-3xl pointer-events-none"></div>

      <!-- Header & Close -->
      <div class="flex items-center justify-between relative">
        <div class="flex items-center gap-2.5">
          <span class="p-2 rounded-xl bg-phantom-mint/10 text-phantom-mint border border-phantom-mint/20">
            <Icons name="Zap" :size="18" />
          </span>
          <div>
            <h3 class="text-base font-bold font-display text-white">
              {{ mode === 'login' ? 'Đăng Nhập Cổng Hoàn Tiền' : 'Tạo Tài Khoản Hoàn Tiền' }}
            </h3>
            <p class="text-[11px] text-slate-400">
              {{ mode === 'login' ? 'Đăng nhập để xem số dư và rút tiền về ví' : 'Lưu trữ tiền hoàn vĩnh viễn & rút về tài khoản' }}
            </p>
          </div>
        </div>
        <button
          type="button"
          class="p-1.5 rounded-lg text-slate-400 hover:text-white bg-white/5 hover:bg-white/10 transition-colors"
          @click="emit('close')"
        >
          <Icons name="X" :size="16" />
        </button>
      </div>

      <!-- Mode Switch Pills -->
      <div class="flex p-1 bg-black/40 border border-white/10 rounded-xl">
        <button
          type="button"
          class="flex-1 py-1.5 text-xs font-semibold rounded-lg transition-all"
          :class="mode === 'login' ? 'bg-phantom-mint text-midnight-950 shadow-glow-mint font-bold' : 'text-slate-400 hover:text-white'"
          @click="switchMode('login')"
        >
          Đăng Nhập
        </button>
        <button
          type="button"
          class="flex-1 py-1.5 text-xs font-semibold rounded-lg transition-all"
          :class="mode === 'register' ? 'bg-phantom-mint text-midnight-950 shadow-glow-mint font-bold' : 'text-slate-400 hover:text-white'"
          @click="switchMode('register')"
        >
          Đăng Ký Mới
        </button>
      </div>

      <!-- Error Alert -->
      <div
        v-if="errorMessage"
        class="p-3 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-xs flex items-center gap-2"
      >
        <Icons name="X" :size="15" class="shrink-0" />
        <span>{{ errorMessage }}</span>
      </div>

      <!-- LOGIN FORM -->
      <form v-if="mode === 'login'" @submit.prevent="handleLogin" class="space-y-3.5">
        <div class="space-y-1">
          <label class="text-xs font-semibold text-slate-300">Địa chỉ Email</label>
          <div class="relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500">
              <Icons name="Mail" :size="15" />
            </span>
            <input
              v-model="loginForm.email"
              type="email"
              required
              placeholder="name@example.com"
              class="w-full pl-9 pr-3.5 py-2 rounded-xl bg-black/50 border border-white/15 text-white text-xs placeholder:text-slate-600 focus:outline-none focus:border-phantom-mint transition-colors"
            />
          </div>
        </div>

        <div class="space-y-1">
          <label class="text-xs font-semibold text-slate-300">Mật khẩu</label>
          <div class="relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500">
              <Icons name="Lock" :size="15" />
            </span>
            <input
              v-model="loginForm.password"
              type="password"
              required
              placeholder="••••••••"
              class="w-full pl-9 pr-3.5 py-2 rounded-xl bg-black/50 border border-white/15 text-white text-xs placeholder:text-slate-600 focus:outline-none focus:border-phantom-mint transition-colors"
            />
          </div>
        </div>

        <div class="flex items-center justify-between text-[11px] pt-0.5">
          <label class="flex items-center gap-2 text-slate-400 cursor-pointer">
            <input
              v-model="loginForm.remember"
              type="checkbox"
              class="rounded border-white/20 bg-white/5 text-phantom-mint focus:ring-phantom-mint"
            />
            <span>Ghi nhớ đăng nhập</span>
          </label>
          <span class="text-slate-500 hover:text-slate-300 cursor-pointer">Quên mật khẩu?</span>
        </div>

        <button
          type="submit"
          class="w-full py-2.5 rounded-xl bg-phantom-mint hover:bg-phantom-mint/90 text-midnight-950 text-xs font-bold transition-all shadow-glow-mint disabled:opacity-50 flex items-center justify-center gap-2 mt-1"
          :disabled="loginForm.processing"
        >
          <Icons v-if="loginForm.processing" name="RotateCcw" :size="14" class="animate-spin" />
          <span>{{ loginForm.processing ? 'Đang đăng nhập...' : 'Đăng Nhập Vào Ví Hoàn Tiền' }}</span>
        </button>
      </form>

      <!-- REGISTER FORM -->
      <form v-else @submit.prevent="handleRegister" class="space-y-3.5">
        <div class="space-y-1">
          <label class="text-xs font-semibold text-slate-300">Họ và tên</label>
          <div class="relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500">
              <Icons name="User" :size="15" />
            </span>
            <input
              v-model="registerForm.name"
              type="text"
              required
              placeholder="Nguyễn Văn A"
              class="w-full pl-9 pr-3.5 py-2 rounded-xl bg-black/50 border border-white/15 text-white text-xs placeholder:text-slate-600 focus:outline-none focus:border-phantom-mint transition-colors"
            />
          </div>
        </div>

        <div class="space-y-1">
          <label class="text-xs font-semibold text-slate-300">Email nhận thông báo</label>
          <div class="relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500">
              <Icons name="Mail" :size="15" />
            </span>
            <input
              v-model="registerForm.email"
              type="email"
              required
              placeholder="name@example.com"
              class="w-full pl-9 pr-3.5 py-2 rounded-xl bg-black/50 border border-white/15 text-white text-xs placeholder:text-slate-600 focus:outline-none focus:border-phantom-mint transition-colors"
            />
          </div>
        </div>

        <div class="space-y-1">
          <label class="text-xs font-semibold text-slate-300">Mật khẩu (tối thiểu 6 ký tự)</label>
          <div class="relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500">
              <Icons name="Lock" :size="15" />
            </span>
            <input
              v-model="registerForm.password"
              type="password"
              required
              minlength="6"
              placeholder="••••••••"
              class="w-full pl-9 pr-3.5 py-2 rounded-xl bg-black/50 border border-white/15 text-white text-xs placeholder:text-slate-600 focus:outline-none focus:border-phantom-mint transition-colors"
            />
          </div>
        </div>

        <p class="text-[11px] text-slate-400 bg-black/40 p-2.5 rounded-xl border border-white/5 leading-relaxed">
          ✨ <span class="text-phantom-mint font-semibold">Tự động gộp ví</span>: Số dư và các link Shopee bạn vừa tạo trên thiết bị này sẽ được tự động chuyển sang tài khoản mới.
        </p>

        <button
          type="submit"
          class="w-full py-2.5 rounded-xl bg-phantom-mint hover:bg-phantom-mint/90 text-midnight-950 text-xs font-bold transition-all shadow-glow-mint disabled:opacity-50 flex items-center justify-center gap-2 mt-1"
          :disabled="registerForm.processing"
        >
          <Icons v-if="registerForm.processing" name="RotateCcw" :size="14" class="animate-spin" />
          <span>{{ registerForm.processing ? 'Đang tạo tài khoản...' : 'Đăng Ký & Nhận Tiền Hoàn Ngay' }}</span>
        </button>
      </form>
    </div>
  </div>
</template>
