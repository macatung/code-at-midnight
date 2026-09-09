<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import Icons from '@/Components/ui/Icons.vue';

defineProps<{
  title?: string;
  description?: string;
  ogImage?: string;
}>();

const page = usePage();
const isMobileMenuOpen = ref(false);

const isHomeActive = (): boolean => {
  const currentUrl = page.url;
  return currentUrl === '/decode' || currentUrl === '/' || currentUrl.startsWith('/decode#');
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

    <!-- Clean Engineering Publication Header -->
    <header class="sticky top-0 z-50 border-b border-slate-800/80 bg-[#070a12]/95 backdrop-blur-md">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <!-- Logo & Masthead -->
        <Link href="/decode" class="flex items-center gap-3 group">
          <div class="w-9 h-9 rounded-lg bg-slate-900 border border-slate-700/80 flex items-center justify-center shadow-sm group-hover:border-[#00f5d4] transition-colors">
            <img src="/brand/decode/decode-badge-avatar.svg" alt="Ma Giải Mã" class="w-6 h-6 object-contain" />
          </div>
          <div class="flex flex-col text-left">
            <div class="flex items-center gap-2">
              <span class="font-display font-extrabold text-base tracking-wide text-white group-hover:text-[#00f5d4] transition-colors">
                MA GIẢI MÃ
              </span>
              <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-slate-800 text-slate-300 border border-slate-700">
                BLOG
              </span>
            </div>
            <span class="text-[11px] text-slate-400 font-sans tracking-tight">
              Khảo Cứu Kiến Trúc Hệ Thống • by Ma Cà Tưng
            </span>
          </div>
        </Link>

        <!-- Desktop Navigation Items -->
        <nav class="hidden md:flex items-center gap-6 text-sm font-sans">
          <Link
            href="/decode"
            class="transition-colors hover:text-[#00f5d4]"
            :class="isHomeActive() && !page.url.includes('/tap') ? 'text-[#00f5d4] font-semibold' : 'text-slate-300'"
          >
            Tất Cả Bài Viết
          </Link>

          <a
            href="/decode#series-pilot"
            class="text-slate-300 hover:text-[#00f5d4] transition-colors"
          >
            Series Pilot Season
          </a>

          <a
            href="https://macatung.dev"
            target="_blank"
            class="text-slate-300 hover:text-white transition-colors flex items-center gap-1"
          >
            <span>Về Tác Giả</span>
            <span class="text-xs text-slate-400">↗</span>
          </a>

          <a
            href="https://youtube.com/@MaGiaiMa"
            target="_blank"
            rel="noopener noreferrer"
            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg bg-[#ff0054]/10 hover:bg-[#ff0054]/20 border border-[#ff0054]/30 text-[#ff0054] text-xs font-bold font-mono transition-all"
          >
            <Icons name="Play" :size="12" />
            <span>Kênh @MaGiaiMa</span>
          </a>
        </nav>

        <!-- Mobile Menu Toggle Button -->
        <button
          type="button"
          class="md:hidden p-2 rounded-lg border border-slate-800 bg-slate-900/60 text-slate-300 hover:text-white"
          @click="isMobileMenuOpen = !isMobileMenuOpen"
          aria-label="Toggle Navigation"
        >
          <Icons :name="isMobileMenuOpen ? 'X' : 'Menu'" :size="20" />
        </button>
      </div>

      <!-- Mobile Dropdown Menu -->
      <div v-if="isMobileMenuOpen" class="md:hidden border-t border-slate-800 bg-[#070a12] px-4 py-4 space-y-3 text-left">
        <Link
          href="/decode"
          class="block py-2 text-sm font-sans text-slate-200 hover:text-[#00f5d4]"
          @click="isMobileMenuOpen = false"
        >
          Tất Cả Bài Viết
        </Link>
        <a
          href="/decode#series-pilot"
          class="block py-2 text-sm font-sans text-slate-300 hover:text-[#00f5d4]"
          @click="isMobileMenuOpen = false"
        >
          Series: Pilot Season (5 Tập)
        </a>
        <a
          href="https://macatung.dev"
          target="_blank"
          class="block py-2 text-sm font-sans text-slate-300 hover:text-white"
        >
          Về Tác Giả (macatung.dev) ↗
        </a>
        <div class="pt-3 border-t border-slate-800 flex items-center justify-between">
          <a
            href="https://youtube.com/@MaGiaiMa"
            target="_blank"
            rel="noopener noreferrer"
            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-[#ff0054] text-white text-xs font-bold"
          >
            <Icons name="Play" :size="12" /> Kênh @MaGiaiMa
          </a>
          <a href="https://theravada.macatung.dev" class="text-xs text-amber-300 font-sans">☸️ Ma Tọa Thiền ↗</a>
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
            <h4 class="font-mono text-xs uppercase tracking-wider text-[#00f5d4] font-bold mb-3">Series: Pilot Season</h4>
            <ul class="space-y-2 text-xs font-sans">
              <li><Link href="/decode/tap/tap-01-quet-the-visa-100k-2-giay-du-hanh" class="hover:text-white transition-colors">01. Quẹt thẻ Visa 100k</Link></li>
              <li><Link href="/decode/tap/tap-02-google-tim-kiem-50-ty-trang-web-0-3-giay" class="hover:text-white transition-colors">02. Google Search 0.3s</Link></li>
              <li><Link href="/decode/tap/tap-03-cuoc-goi-xuyen-luc-dia-cap-quang-day-bien" class="hover:text-white transition-colors">03. Cáp quang đáy biển</Link></li>
              <li><Link href="/decode/tap/tap-04-cay-atm-khong-bao-gio-nha-nham-tien" class="hover:text-white transition-colors">04. Cây ATM rút tiền</Link></li>
              <li><Link href="/decode/tap/tap-05-bam-dat-grab-ve-tinh-gps-thuyet-tuong-doi-einstein" class="hover:text-white transition-colors">05. Grab, GPS &amp; Einstein</Link></li>
            </ul>
          </div>

          <div>
            <h4 class="font-mono text-xs uppercase tracking-wider text-slate-300 font-bold mb-3">Hệ Sinh Thái</h4>
            <ul class="space-y-2 text-xs font-sans">
              <li><a href="https://macatung.dev" class="hover:text-[#00f5a0] transition-colors">🧛‍♂️ Ma Cà Tưng (Portfolio) ↗</a></li>
              <li><a href="https://theravada.macatung.dev" class="hover:text-amber-300 transition-colors">☸️ Ma Tọa Thiền (Pāḷi Dhamma) ↗</a></li>
              <li><a href="/decode#series-pilot" class="hover:text-[#00f5d4] transition-colors">📚 Series Pilot Season</a></li>
              <li><a href="https://youtube.com/@MaGiaiMa" target="_blank" rel="noopener noreferrer" class="hover:text-rose-400 transition-colors">▶ Kênh YouTube @MaGiaiMa</a></li>
            </ul>
          </div>
        </div>

        <!-- Bottom Copyright -->
        <div class="pt-6 flex flex-col sm:flex-row items-center justify-between text-xs font-sans text-slate-400 gap-4">
          <p>© 2026 Ma Giải Mã • decode.macatung.dev</p>
          <div class="flex items-center gap-4 text-xs font-mono text-slate-400">
            <span>ENGINEERING PUBLICATION</span>
            <span>•</span>
            <span>SYSTEMS ANATOMY</span>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>
