<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import Icons from '@/Components/ui/Icons.vue';

defineProps<{
  title?: string;
  description?: string;
  ogImage?: string;
}>();

const page = usePage();
const isMobileMenuOpen = ref(false);
const latencyDisplay = ref('0.3s');

// Simulation of telemetry clock
const systemClock = ref('00:00:00.000');
let timer: any = null;

const updateClock = () => {
  const now = new Date();
  const h = String(now.getHours()).padStart(2, '0');
  const m = String(now.getMinutes()).padStart(2, '0');
  const s = String(now.getSeconds()).padStart(2, '0');
  const ms = String(now.getMilliseconds()).padStart(3, '0');
  systemClock.value = `${h}:${m}:${s}.${ms}`;
};

onMounted(() => {
  updateClock();
  timer = setInterval(updateClock, 50);
});

onUnmounted(() => {
  if (timer) clearInterval(timer);
});

const isLinkActive = (path: string): boolean => {
  const currentUrl = page.url;
  if (path === '/decode') return currentUrl === '/decode' || currentUrl === '/';
  return currentUrl.startsWith(path);
};
</script>

<template>
  <div class="min-h-screen bg-[#060913] text-slate-200 font-sans selection:bg-[#00f5d4]/30 selection:text-[#00f5d4] relative overflow-x-hidden">
    <Head>
      <title>{{ title ? `${title} — Ma Giải Mã` : 'Ma Giải Mã — Mở nắp những hệ thống vô hình vận hành thế giới' }}</title>
      <meta name="description" :content="description || 'Khám phá các hệ thống công nghệ ngầm qua bản vẽ CAD Blueprint và kiến trúc Isometric 3D sống động.'" />
      <meta property="og:title" :content="title ? `${title} — Ma Giải Mã` : 'Ma Giải Mã — decode.macatung.dev'" />
      <meta property="og:description" :content="description || 'Mở nắp những hệ thống vô hình vận hành thế giới. Khám phá cùng @MaGiaiMa.'" />
      <meta property="og:image" :content="ogImage || '/brand/decode/og-decode-1200x630.png'" />
      <link rel="icon" type="image/x-icon" href="/brand/decode/favicon-decode.ico" />
      <link rel="icon" type="image/svg+xml" href="/brand/decode/favicon.svg" />
    </Head>

    <!-- Technical Blueprint CAD Grid Background -->
    <div class="fixed inset-0 pointer-events-none z-0">
      <!-- Grid Pattern -->
      <div
        class="w-full h-full opacity-[0.08]"
        style="background-image: linear-gradient(to right, #00b4d8 1px, transparent 1px), linear-gradient(to bottom, #00b4d8 1px, transparent 1px); background-size: 40px 40px;"
      />
      <!-- Radial Spotlight Glow -->
      <div class="absolute -top-[30%] left-1/2 -translate-x-1/2 w-[900px] h-[600px] bg-[#00b4d8]/10 blur-[140px] rounded-full" />
      <div class="absolute top-[40%] right-0 w-[500px] h-[500px] bg-[#00f5d4]/5 blur-[120px] rounded-full" />
    </div>

    <!-- HUD Telemetry Top Bar -->
    <header class="sticky top-0 z-50 border-b border-[#00b4d8]/20 bg-[#060913]/90 backdrop-blur-md">
      <!-- Topmost Telemetry Stream -->
      <div class="border-b border-[#00b4d8]/10 px-4 py-1 text-[11px] font-mono text-[#00b4d8]/70 flex items-center justify-between overflow-x-auto">
        <div class="flex items-center gap-3 shrink-0">
          <span class="inline-flex items-center gap-1.5 text-[#00f5d4]">
            <span class="w-1.5 h-1.5 rounded-full bg-[#00f5d4] animate-ping" />
            SYS.ONLINE
          </span>
          <span>•</span>
          <span>SUBDOMAIN: <span class="text-white font-semibold">decode.macatung.dev</span></span>
          <span class="hidden sm:inline">•</span>
          <span class="hidden sm:inline">LATENCY: <span class="text-amber-400 font-semibold">{{ latencyDisplay }}</span></span>
        </div>
        <div class="flex items-center gap-3 shrink-0 font-mono text-[10px]">
          <span class="hidden md:inline text-slate-400">UTC CLK: {{ systemClock }}</span>
          <span>•</span>
          <a href="https://macatung.dev" class="hover:text-white transition-colors">🧛‍♂️ macatung.dev ↗</a>
          <span>•</span>
          <a href="https://theravada.macatung.dev" class="hover:text-amber-300 transition-colors">☸️ Ma Tọa Thiền ↗</a>
        </div>
      </div>

      <!-- Main Navigation Header -->
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <!-- Logo Brand -->
        <Link href="/decode" class="flex items-center gap-3 group">
          <div class="relative w-10 h-10 rounded-xl bg-[#0a1426] border border-[#00f5d4]/40 flex items-center justify-center shadow-lg shadow-[#00f5d4]/10 group-hover:border-[#00f5d4] transition-all">
            <img src="/brand/decode/favicon.svg" alt="Ma Giải Mã" class="w-6 h-6 object-contain" />
            <div class="absolute -inset-0.5 rounded-xl bg-gradient-to-br from-[#00f5d4] to-[#0077b6] opacity-0 group-hover:opacity-30 blur transition-opacity" />
          </div>
          <div class="flex flex-col">
            <div class="flex items-center gap-2">
              <span class="font-display font-extrabold text-lg text-white tracking-wider group-hover:text-[#00f5d4] transition-colors">
                MA GIẢI MÃ
              </span>
              <span class="text-[9px] font-mono px-1.5 py-0.5 rounded bg-[#00f5d4]/15 text-[#00f5d4] border border-[#00f5d4]/30 font-bold">
                PILOT
              </span>
            </div>
            <span class="text-[11px] font-mono text-slate-400 tracking-tight">
              The Systems Anatomist • @MaGiaiMa
            </span>
          </div>
        </Link>

        <!-- Desktop Navigation Items -->
        <nav class="hidden md:flex items-center gap-1 font-mono text-xs">
          <Link
            href="/decode"
            class="px-3.5 py-2 rounded-lg transition-colors flex items-center gap-1.5"
            :class="isLinkActive('/decode') && !page.url.includes('/brand') && !page.url.includes('/tap') ? 'bg-[#00f5d4]/15 text-[#00f5d4] font-semibold border border-[#00f5d4]/30' : 'text-slate-300 hover:text-white hover:bg-white/5'"
          >
            <Icons name="Layout" :size="14" />
            Pilot Season
          </Link>

          <Link
            href="/decode/tap-1-visa-100k"
            class="px-3.5 py-2 rounded-lg transition-colors flex items-center gap-1.5"
            :class="page.url.includes('/tap') ? 'bg-[#00f5d4]/15 text-[#00f5d4] font-semibold border border-[#00f5d4]/30' : 'text-slate-300 hover:text-white hover:bg-white/5'"
          >
            <Icons name="Zap" :size="14" />
            Tập 01: Visa 100k
          </Link>

          <Link
            href="/decode/brand-kit"
            class="px-3.5 py-2 rounded-lg transition-colors flex items-center gap-1.5"
            :class="page.url.includes('/brand') ? 'bg-[#00f5d4]/15 text-[#00f5d4] font-semibold border border-[#00f5d4]/30' : 'text-slate-300 hover:text-white hover:bg-white/5'"
          >
            <Icons name="Sparkles" :size="14" />
            Brand Kit
          </Link>
        </nav>

        <!-- Channel Subscribe CTA Action -->
        <div class="hidden sm:flex items-center gap-3">
          <a
            href="https://youtube.com/@MaGiaiMa"
            target="_blank"
            rel="noopener noreferrer"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-[#ff0054] to-[#e11d48] text-white text-xs font-bold shadow-lg shadow-[#ff0054]/20 hover:shadow-[#ff0054]/40 hover:scale-[1.02] active:scale-[0.98] transition-all"
          >
            <Icons name="Play" :size="14" />
            <span>Kênh @MaGiaiMa</span>
          </a>
        </div>

        <!-- Mobile Menu Toggle Button -->
        <button
          type="button"
          class="md:hidden p-2 rounded-lg border border-[#00b4d8]/20 bg-[#0a1222] text-slate-300 hover:text-white"
          @click="isMobileMenuOpen = !isMobileMenuOpen"
          aria-label="Toggle Navigation"
        >
          <Icons :name="isMobileMenuOpen ? 'X' : 'Menu'" :size="20" />
        </button>
      </div>

      <!-- Mobile Dropdown Menu -->
      <div v-if="isMobileMenuOpen" class="md:hidden border-t border-[#00b4d8]/20 bg-[#060913]/98 px-4 py-4 space-y-2">
        <Link
          href="/decode"
          class="block px-3 py-2 rounded-lg text-sm font-mono"
          :class="isLinkActive('/decode') ? 'bg-[#00f5d4]/15 text-[#00f5d4]' : 'text-slate-300'"
          @click="isMobileMenuOpen = false"
        >
          Roadmap 5 Tập Pilot
        </Link>
        <Link
          href="/decode/tap-1-visa-100k"
          class="block px-3 py-2 rounded-lg text-sm font-mono"
          :class="page.url.includes('/tap') ? 'bg-[#00f5d4]/15 text-[#00f5d4]' : 'text-slate-300'"
          @click="isMobileMenuOpen = false"
        >
          Tập 01: Quẹt thẻ Visa 100k
        </Link>
        <Link
          href="/decode/brand-kit"
          class="block px-3 py-2 rounded-lg text-sm font-mono"
          :class="page.url.includes('/brand') ? 'bg-[#00f5d4]/15 text-[#00f5d4]' : 'text-slate-300'"
          @click="isMobileMenuOpen = false"
        >
          Bộ Nhận Diện Brand Kit
        </Link>
        <div class="pt-2 border-t border-white/10 flex items-center justify-between">
          <a
            href="https://youtube.com/@MaGiaiMa"
            target="_blank"
            rel="noopener noreferrer"
            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-[#ff0054] text-white text-xs font-bold"
          >
            <Icons name="Play" :size="12" /> Subscribe @MaGiaiMa
          </a>
          <a href="https://theravada.macatung.dev" class="text-xs font-mono text-amber-300">☸️ Ma Tọa Thiền ↗</a>
        </div>
      </div>
    </header>

    <!-- Main Content Slot -->
    <main class="relative z-10">
      <slot />
    </main>

    <!-- Blueprint Technical Footer -->
    <footer class="relative z-10 border-t border-[#00b4d8]/20 bg-[#04060d] text-slate-400 py-12 mt-20">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Top Footer Row -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 pb-10 border-b border-[#00b4d8]/10">
          <div class="md:col-span-2 space-y-3">
            <div class="flex items-center gap-3">
              <img src="/brand/decode/decode-badge-avatar.svg" alt="Ma Giải Mã" class="w-8 h-8 rounded-lg object-contain" />
              <span class="font-display font-black text-xl text-white tracking-wider">MA GIẢI MÃ</span>
            </div>
            <p class="text-sm text-slate-300 font-sans max-w-md leading-relaxed">
              Mở nắp những hệ thống vô hình vận hành thế giới. Kênh video kỹ thuật &amp; kiến trúc phân tán dưới góc nhìn giải phẫu Isometric 3D sinh động.
            </p>
            <div class="pt-2 flex items-center gap-3 text-xs font-mono">
              <span class="px-2 py-1 rounded bg-[#00f5d4]/10 text-[#00f5d4] border border-[#00f5d4]/20">@MaGiaiMa</span>
              <span class="px-2 py-1 rounded bg-slate-800 text-slate-300">decode.macatung.dev</span>
            </div>
          </div>

          <div>
            <h4 class="font-mono text-xs uppercase tracking-widest text-[#00f5d4] font-bold mb-3">Pilot Season</h4>
            <ul class="space-y-2 text-xs font-sans">
              <li><Link href="/decode/tap-1-visa-100k" class="hover:text-white transition-colors">01. Quẹt thẻ Visa 100k (2s)</Link></li>
              <li><Link href="/decode/tap/tap-02-google-tim-kiem-50-ty-trang-web-0-3-giay" class="hover:text-white transition-colors">02. Google Search 50 tỷ trang (0.3s)</Link></li>
              <li><Link href="/decode/tap/tap-03-cuoc-goi-xuyen-luc-dia-cap-quang-day-bien" class="hover:text-white transition-colors">03. Cáp quang đáy biển</Link></li>
              <li><Link href="/decode/tap/tap-04-cay-atm-khong-bao-gio-nha-nham-tien" class="hover:text-white transition-colors">04. Cây ATM nhả tiền chuẩn</Link></li>
              <li><Link href="/decode/tap/tap-05-bam-dat-grab-ve-tinh-gps-thuyet-tuong-doi-einstein" class="hover:text-white transition-colors">05. Đặt Grab &amp; Thuyết tương đối</Link></li>
            </ul>
          </div>

          <div>
            <h4 class="font-mono text-xs uppercase tracking-widest text-[#00f5d4] font-bold mb-3">Hệ Sinh Thái</h4>
            <ul class="space-y-2 text-xs font-sans">
              <li><a href="https://macatung.dev" class="hover:text-[#00f5a0] transition-colors">🧛‍♂️ Ma Cà Tưng (Portfolio) ↗</a></li>
              <li><a href="https://theravada.macatung.dev" class="hover:text-amber-300 transition-colors">☸️ Ma Tọa Thiền (Pāḷi Dhamma) ↗</a></li>
              <li><Link href="/decode/brand-kit" class="hover:text-white transition-colors">🎨 Bộ Nhận Diện Brand Kit</Link></li>
              <li><a href="https://youtube.com/@MaGiaiMa" target="_blank" rel="noopener noreferrer" class="hover:text-rose-400 transition-colors">▶ Kênh YouTube @MaGiaiMa</a></li>
            </ul>
          </div>
        </div>

        <!-- Bottom Copyright -->
        <div class="pt-6 flex flex-col sm:flex-row items-center justify-between text-xs font-mono text-slate-400 gap-4">
          <p>© 2026 Ma Giải Mã • Subdomain chính thức của macatung.dev</p>
          <div class="flex items-center gap-4">
            <span class="text-[#00f5d4]">CAD // ISOMETRIC ANATOMY</span>
            <span>•</span>
            <span>BYTEBYTEGO-INSPIRED</span>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>
