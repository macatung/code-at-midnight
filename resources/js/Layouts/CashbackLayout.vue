<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import type { CashbackWalletData } from '@/types/cashback';
import Icons from '@/Components/ui/Icons.vue';
import AuthModal from '@/Components/Cashback/AuthModal.vue';

const props = defineProps<{
  title?: string;
  description?: string;
  wallet?: CashbackWalletData;
  authUser?: {
    id: number;
    name: string;
    email: string;
  } | null;
}>();

const showAuthModal = ref(false);
const authModalMode = ref<'login' | 'register'>('login');
const showUserDropdown = ref(false);
const copiedSubId = ref(false);

const openLogin = () => {
  authModalMode.value = 'login';
  showAuthModal.value = true;
};

const openRegister = () => {
  authModalMode.value = 'register';
  showAuthModal.value = true;
};

const copySubId = async () => {
  if (!props.wallet?.sub_id) return;
  try {
    await navigator.clipboard.writeText(props.wallet.sub_id);
    copiedSubId.value = true;
    setTimeout(() => {
      copiedSubId.value = false;
    }, 2000);
  } catch {
    // ignore
  }
};

const formatVND = (num: number) => {
  return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(num || 0);
};

const isSubdomain = typeof window !== 'undefined' && window.location.hostname.startsWith('hoantien.');
const homeUrl = isSubdomain ? '/' : '/hoantien';
const port = typeof window !== 'undefined' && window.location.port ? `:${window.location.port}` : '';
const mainPortfolioUrl = isSubdomain
  ? (typeof window !== 'undefined' && window.location.hostname.includes('localhost') ? `${window.location.protocol}//localhost${port}` : 'https://macatung.dev')
  : '/';

const handleLogout = () => {
  const logoutUrl = isSubdomain ? '/auth/logout' : '/hoantien/auth/logout';
  router.post(logoutUrl, {}, {
    preserveScroll: true,
    onSuccess: () => {
      showUserDropdown.value = false;
      router.reload();
    },
  });
};
</script>

<template>
  <div class="min-h-screen bg-[#04070d] text-slate-100 flex flex-col font-sans selection:bg-phantom-mint selection:text-midnight-950 relative antialiased">
    <Head>
      <title>{{ title ? `${title} — Hoàn Tiền Shopee | MacaTung` : 'Cổng Hoàn Tiền Shopee — Hoàn Tới 80% Hoa Hồng | MacaTung' }}</title>
      <meta name="description" :content="description || 'Hệ thống hoàn tiền Shopee Affiliate tự động. Dán link, mua sắm và nhận hoàn tiền trực tiếp vào tài khoản ngân hàng.'" />
      <meta property="og:title" :content="title || 'Cổng Hoàn Tiền Shopee — MacaTung'" />
      <meta property="og:description" :content="description || 'Nhận hoàn tiền lên tới 80% hoa hồng Shopee cho mọi đơn hàng.'" />
    </Head>

    <!-- Top Navigation Bar -->
    <header class="sticky top-0 z-40 bg-[#04070d]/90 backdrop-blur-xl border-b border-white/10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <!-- Brand / Logo -->
        <div class="flex items-center gap-3">
          <Link :href="homeUrl" class="flex items-center gap-2.5 group">
            <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-orange-600 via-orange-500 to-amber-400 flex items-center justify-center shadow-lg shadow-orange-500/20 group-hover:scale-105 transition-transform">
              <span class="text-white font-black text-xl tracking-tight">S</span>
            </div>
            <div>
              <div class="flex items-center gap-2">
                <span class="font-bold text-base sm:text-lg text-white tracking-tight group-hover:text-orange-400 transition-colors">
                  Hoàn Tiền Shopee
                </span>
                <span class="text-[10px] font-semibold uppercase px-2 py-0.5 rounded-full bg-phantom-mint/15 text-phantom-mint border border-phantom-mint/30 hidden xs:inline-block">
                  80% Cashback
                </span>
              </div>
              <p class="text-[11px] text-slate-400 font-mono">hoantien.macatung.dev</p>
            </div>
          </Link>
        </div>

        <!-- Right Side Navigation & Auth Actions -->
        <div class="flex items-center gap-3 sm:gap-4">
          <!-- Balance Pill -->
          <div v-if="wallet" class="flex items-center gap-2.5 bg-phantom-mint/10 border border-phantom-mint/30 rounded-xl px-3 py-1.5 shadow-sm">
            <span class="w-2 h-2 rounded-full bg-phantom-mint animate-pulse"></span>
            <div class="text-right">
              <span class="text-[9px] uppercase text-phantom-mint block font-bold tracking-wider leading-tight">Khả dụng</span>
              <span class="text-xs sm:text-sm font-bold text-white font-mono leading-tight">
                {{ formatVND(wallet.available_balance) }}
              </span>
            </div>
          </div>

          <!-- Logged In User Dropdown -->
          <div v-if="authUser" class="relative">
            <button
              type="button"
              class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-xs font-semibold text-white transition-all"
              @click="showUserDropdown = !showUserDropdown"
            >
              <div class="w-6 h-6 rounded-full bg-gradient-to-tr from-phantom-mint to-blue-500 flex items-center justify-center text-midnight-950 font-bold text-[11px]">
                {{ authUser.name.charAt(0).toUpperCase() }}
              </div>
              <span class="max-w-[100px] truncate hidden sm:inline-block">{{ authUser.name }}</span>
              <Icons name="ChevronDown" :size="14" class="text-slate-400" />
            </button>

            <!-- Dropdown Menu -->
            <div
              v-if="showUserDropdown"
              class="absolute right-0 mt-2 w-56 bg-midnight-900 border border-white/10 rounded-2xl shadow-2xl p-2 z-50 animate-in fade-in zoom-in-95 duration-150"
            >
              <div class="px-3 py-2 border-b border-white/10 mb-1">
                <div class="text-xs font-semibold text-white truncate">{{ authUser.name }}</div>
                <div class="text-[10px] text-slate-400 truncate font-mono">{{ authUser.email }}</div>
              </div>

              <div class="px-3 py-1.5 text-[11px] text-slate-400 flex items-center justify-between">
                <span>Mã Tracking:</span>
                <span class="font-mono text-orange-400 font-semibold">{{ wallet?.sub_id }}</span>
              </div>

              <button
                type="button"
                class="w-full text-left px-3 py-2 text-xs text-rose-400 hover:bg-rose-500/10 rounded-xl transition-colors flex items-center gap-2 mt-1"
                @click="handleLogout"
              >
                <Icons name="X" :size="14" />
                <span>Đăng xuất tài khoản</span>
              </button>
            </div>
          </div>

          <!-- Guest: Login / Register Trigger Button -->
          <div v-else class="flex items-center gap-2">
            <button
              type="button"
              class="px-3 py-1.5 rounded-xl text-xs font-semibold text-slate-300 hover:text-white hover:bg-white/5 transition-all"
              @click="openLogin"
            >
              Đăng Nhập
            </button>
            <button
              type="button"
              class="px-3.5 py-1.5 rounded-xl bg-phantom-mint hover:bg-phantom-mint/90 text-midnight-950 text-xs font-bold transition-all shadow-md shadow-phantom-mint/10"
              @click="openRegister"
            >
              Đăng Ký
            </button>
          </div>

          <!-- Main Portfolio Backlink -->
          <a
            :href="mainPortfolioUrl"
            class="text-xs text-slate-400 hover:text-white transition-colors border border-white/10 hover:border-white/20 rounded-xl px-3 py-1.5 hidden md:inline-flex items-center gap-1.5"
            title="Trở về website chính macatung.dev"
          >
            <span>← macatung.dev</span>
          </a>
        </div>
      </div>
    </header>

    <!-- Main Content Slot -->
    <main class="flex-grow">
      <slot />
    </main>

    <!-- Footer -->
    <footer class="bg-black/60 border-t border-white/10 py-10 mt-20 text-slate-400 text-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
          <div>
            <div class="flex items-center gap-2.5 mb-3">
              <div class="w-7 h-7 rounded-xl bg-orange-500 flex items-center justify-center text-white font-black text-xs shadow-md shadow-orange-500/20">S</div>
              <span class="font-bold text-white tracking-tight">Cổng Hoàn Tiền Shopee</span>
            </div>
            <p class="text-xs text-slate-400 leading-relaxed">
              Hệ thống kết nối trực tiếp Shopee Open Platform Affiliate API, chia sẻ tới 80% hoa hồng cho người mua sắm và chi trả minh bạch qua tài khoản ngân hàng.
            </p>
          </div>

          <div>
            <h4 class="text-xs font-semibold uppercase tracking-wider text-slate-300 mb-3">Chính Sách & Bảo Mật</h4>
            <ul class="text-xs space-y-2">
              <li class="flex items-center gap-2 text-slate-400">
                <span class="text-phantom-mint font-bold">✓</span> Đối soát đơn hàng tự động từ Shopee
              </li>
              <li class="flex items-center gap-2 text-slate-400">
                <span class="text-phantom-mint font-bold">✓</span> Rút tiền miễn phí qua tài khoản ngân hàng / VietQR
              </li>
              <li class="flex items-center gap-2 text-slate-400">
                <span class="text-phantom-mint font-bold">✓</span> Sổ cái tài chính minh bạch, cập nhật tức thì
              </li>
            </ul>
          </div>

          <div>
            <h4 class="text-xs font-semibold uppercase tracking-wider text-slate-300 mb-3">Vận Hành & Phát Triển</h4>
            <p class="text-xs text-slate-400 mb-2 leading-relaxed">
              Hệ sinh thái sản phẩm công nghệ phát triển bởi <a :href="mainPortfolioUrl" class="text-phantom-mint hover:underline font-medium">Ma Cà Tưng</a>.
            </p>
            <p class="text-[11px] text-slate-500 leading-normal">
              Shopee là thương hiệu của Shopee Pte. Ltd. Hệ thống hoàn tiền độc lập tuân thủ chương trình Shopee Affiliate Partner.
            </p>
          </div>
        </div>

        <div class="border-t border-white/10 pt-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
          <p>© 2026 macatung.dev — All rights reserved.</p>
          <div class="flex items-center gap-4">
            <Link :href="homeUrl" class="hover:text-slate-300">Cổng Hoàn Tiền</Link>
            <a href="https://shopee.vn" target="_blank" rel="noopener" class="hover:text-slate-300">Shopee VN</a>
            <a :href="mainPortfolioUrl" class="hover:text-slate-300">macatung.dev</a>
          </div>
        </div>
      </div>
    </footer>

    <!-- Global Auth Modal -->
    <AuthModal
      :show="showAuthModal"
      :initial-mode="authModalMode"
      @close="showAuthModal = false"
      @success="showAuthModal = false"
    />
  </div>
</template>
