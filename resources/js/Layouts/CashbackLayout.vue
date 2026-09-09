<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import type { CashbackWalletData } from '@/types/cashback';

const props = defineProps<{
  title?: string;
  description?: string;
  wallet?: CashbackWalletData;
}>();

const copiedSubId = ref(false);

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
</script>

<template>
  <div class="min-h-screen bg-slate-950 text-slate-100 flex flex-col font-sans selection:bg-orange-500 selection:text-white relative">
    <Head>
      <title>{{ title ? `${title} — Hoàn Tiền Shopee | MacaTung` : 'Cổng Hoàn Tiền Shopee — Tích Lũy Tới 80% Hoa Hồng | MacaTung' }}</title>
      <meta name="description" :content="description || 'Hệ thống hoàn tiền Shopee Affiliate tự động. Dán link, mua sắm và nhận hoàn tiền trực tiếp vào tài khoản ngân hàng.'" />
      <meta property="og:title" :content="title || 'Cổng Hoàn Tiền Shopee — MacaTung'" />
      <meta property="og:description" :content="description || 'Nhận hoàn tiền lên tới 80% hoa hồng Shopee cho mọi đơn hàng.'" />
    </Head>

    <!-- Top Navigation Bar -->
    <header class="sticky top-0 z-40 bg-slate-900/90 backdrop-blur border-b border-slate-800">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <!-- Brand / Logo -->
        <div class="flex items-center gap-3">
          <Link :href="homeUrl" class="flex items-center gap-2 group">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-orange-600 via-orange-500 to-amber-400 flex items-center justify-center shadow-lg shadow-orange-500/20 group-hover:scale-105 transition-transform">
              <span class="text-white font-black text-xl tracking-tight">S</span>
            </div>
            <div>
              <div class="flex items-center gap-2">
                <span class="font-bold text-lg text-white tracking-tight group-hover:text-orange-400 transition-colors">
                  Hoàn Tiền Shopee
                </span>
                <span class="text-[10px] font-semibold uppercase px-2 py-0.5 rounded-full bg-orange-500/20 text-orange-400 border border-orange-500/30">
                  Affiliate
                </span>
              </div>
              <p class="text-xs text-slate-400 font-mono">hoantien.macatung.dev</p>
            </div>
          </Link>
        </div>

        <!-- Right Side: Wallet Quick Info & Actions -->
        <div class="flex items-center gap-3 sm:gap-4">
          <!-- SubId Tracking Pill -->
          <div v-if="wallet?.sub_id" class="hidden md:flex items-center gap-1.5 bg-slate-800/80 border border-slate-700/60 rounded-lg px-2.5 py-1 text-xs">
            <span class="text-slate-400">ID:</span>
            <span class="font-mono text-orange-400 font-semibold">{{ wallet.sub_id }}</span>
            <button
              @click="copySubId"
              type="button"
              class="ml-1 text-slate-400 hover:text-white transition-colors"
              title="Sao chép mã theo dõi"
            >
              <span v-if="copiedSubId" class="text-emerald-400 text-[11px]">✓</span>
              <span v-else class="text-[11px]">📋</span>
            </button>
          </div>

          <!-- Balance Pill -->
          <div v-if="wallet" class="flex items-center gap-2 bg-emerald-950/40 border border-emerald-500/30 rounded-lg px-3 py-1.5">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            <div class="text-right">
              <span class="text-[10px] uppercase text-emerald-400 block font-semibold leading-tight">Khả dụng</span>
              <span class="text-xs sm:text-sm font-bold text-emerald-300 font-mono leading-tight">
                {{ formatVND(wallet.available_balance) }}
              </span>
            </div>
          </div>

          <!-- Main Portfolio Backlink -->
          <a
            :href="mainPortfolioUrl"
            class="text-xs text-slate-400 hover:text-white transition-colors border border-slate-700/60 rounded-lg px-2.5 py-1.5 hidden sm:inline-block"
          >
            ← MacaTung
          </a>
        </div>
      </div>
    </header>

    <!-- Main Content Body -->
    <main class="flex-grow">
      <slot />
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 border-t border-slate-800/80 py-8 mt-16 text-slate-400 text-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-6">
          <div>
            <div class="flex items-center gap-2 mb-3">
              <div class="w-6 h-6 rounded-md bg-orange-500 flex items-center justify-center text-white font-bold text-xs">S</div>
              <span class="font-bold text-slate-200">Hệ Thống Hoàn Tiền Shopee</span>
            </div>
            <p class="text-xs text-slate-400 leading-relaxed">
              Giải pháp kết nối trực tiếp Shopee Open Platform Affiliate API, minh bạch đối soát đơn hàng và hoàn tiền nhanh chóng về tài khoản ngân hàng.
            </p>
          </div>

          <div>
            <h4 class="text-xs font-semibold uppercase tracking-wider text-slate-300 mb-3">Chính sách & An toàn</h4>
            <ul class="text-xs space-y-2">
              <li class="flex items-center gap-1.5 text-slate-400">
                <span class="text-emerald-400">✓</span> Đối soát tự động từ Shopee Open Platform
              </li>
              <li class="flex items-center gap-1.5 text-slate-400">
                <span class="text-emerald-400">✓</span> Sổ cái giao dịch minh bạch, không mất phí rút
              </li>
              <li class="flex items-center gap-1.5 text-slate-400">
                <span class="text-emerald-400">✓</span> Hạn mức rút tiền tối thiểu 50.000 ₫
              </li>
            </ul>
          </div>

          <div>
            <h4 class="text-xs font-semibold uppercase tracking-wider text-slate-300 mb-3">Hỗ trợ & Phát triển</h4>
            <p class="text-xs text-slate-400 mb-2">
              Được phát triển và vận hành bởi <a :href="mainPortfolioUrl" class="text-orange-400 hover:underline">MacaTung</a>.
            </p>
            <p class="text-xs text-slate-500">
              Shopee là thương hiệu đã đăng ký của Shopee Pte. Ltd. Hệ thống hoạt động theo chính sách Đối tác Tiếp thị liên kết chính thức.
            </p>
          </div>
        </div>

        <div class="border-t border-slate-800/60 pt-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500">
          <p>© 2026 MacaTung. All rights reserved.</p>
          <div class="flex items-center gap-4">
            <Link :href="homeUrl" class="hover:text-slate-300">Cổng Hoàn Tiền</Link>
            <a href="https://shopee.vn" target="_blank" rel="noopener" class="hover:text-slate-300">Shopee Việt Nam</a>
            <a :href="mainPortfolioUrl" class="hover:text-slate-300">Portfolio Chính</a>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>
