<script setup lang="ts">
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import SeoHead from '@/Components/common/SeoHead.vue';
import ZenMascotLogo from '@/Components/theravada/ZenMascotLogo.vue';
import ZenBackgroundCanvas from '@/Components/theravada/ZenBackgroundCanvas.vue';
import PaliGlossaryModal from '@/Components/theravada/PaliGlossaryModal.vue';
import { useZenTimeCycle } from '@/composables/useZenTimeCycle';

defineProps<{
  title?: string;
  description?: string;
  keywords?: string;
  canonical?: string;
  ogType?: 'website' | 'article' | 'profile';
  ogImage?: string;
  article?: any;
  jsonLd?: any;
}>();

const isGlossaryOpen = ref(false);
const { activeZenPhase } = useZenTimeCycle();
</script>

<template>
  <div class="min-h-screen bg-stone-950 text-stone-100 selection:bg-amber-500 selection:text-stone-950 font-serif flex flex-col justify-between relative overflow-x-hidden antialiased">
    <SeoHead
      :title="title"
      :description="description"
      :keywords="keywords"
      :canonical="canonical || 'https://theravada.macatung.dev'"
      :og-type="ogType || 'website'"
      :og-image="ogImage"
      :article="article"
      :json-ld="jsonLd"
      :is-theravada="true"
    />

    <!-- Ambient Subtle Warm Light Aura Dynamically Tinted by 24H Monastic Time -->
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden">
      <div
        class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[450px] rounded-full blur-[160px] opacity-20 transition-all duration-1000"
        :style="{ backgroundColor: activeZenPhase.accentHex }"
      />
      <div
        class="absolute bottom-0 right-10 w-[600px] h-[600px] rounded-full blur-[180px] opacity-15 transition-all duration-1000"
        :style="{ backgroundColor: activeZenPhase.accentHex }"
      />
    </div>

    <!-- Multi-Layer Zen Background Canvas (Dhamma Wheel, Petals, Bodhi Leaves & Incense Smoke) -->
    <ZenBackgroundCanvas />

    <!-- 1. Zen Top Navigation Header (Pure Minimalist Navigation) -->
    <header class="sticky top-0 z-40 w-full border-b border-amber-500/20 bg-stone-950/95 backdrop-blur-xl shadow-xl py-3.5 sm:py-4">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between flex-nowrap shrink-0 overflow-x-auto lg:overflow-visible gap-4 sm:gap-8">
        
        <!-- Brand / Zen Mascot Logo + Title: MA TỌA THIỀN -->
        <Link
          href="/theravada"
          class="flex items-center gap-3.5 group transition-transform duration-300 hover:scale-[1.02] shrink-0 whitespace-nowrap py-1"
        >
          <!-- Mascot Tọa Thiền Tòa Sen Logo -->
          <ZenMascotLogo :size="48" />
          
          <div class="flex flex-col text-left justify-center whitespace-nowrap gap-0.5">
            <div class="flex items-center gap-2 flex-nowrap">
              <span class="text-base sm:text-xl lg:text-2xl font-serif font-bold text-amber-100 tracking-wide whitespace-nowrap">
                Ma Tọa Thiền
              </span>
              <span class="inline-block text-[11px] font-sans px-2.5 py-0.5 rounded-full bg-gradient-to-r from-amber-500/20 to-yellow-500/20 text-amber-300 border border-amber-500/40 font-semibold whitespace-nowrap shadow-sm">
                Theravāda
              </span>
            </div>
            <span class="text-[11px] sm:text-xs font-serif text-stone-400 italic whitespace-nowrap">
              Chánh Niệm Từng Giây • Theravāda
            </span>
          </div>
        </Link>

        <!-- Clean Main Navigation Links -->
        <nav class="flex items-center gap-1 sm:gap-2 shrink-0 whitespace-nowrap">
          <Link
            href="/theravada"
            class="px-3 sm:px-4 py-2 rounded-2xl text-xs sm:text-sm font-serif text-stone-300 hover:text-amber-200 hover:bg-stone-900/80 transition-all font-medium whitespace-nowrap shrink-0"
          >
            Trang Chủ
          </Link>
          <Link
            href="/theravada/danh-muc/phap-hoc"
            class="px-3 sm:px-4 py-2 rounded-2xl text-xs sm:text-sm font-serif text-stone-300 hover:text-amber-200 hover:bg-stone-900/80 transition-all font-medium whitespace-nowrap shrink-0"
          >
            Pháp Học
          </Link>
          <Link
            href="/theravada/danh-muc/phap-hanh"
            class="px-3 sm:px-4 py-2 rounded-2xl text-xs sm:text-sm font-serif text-stone-300 hover:text-amber-200 hover:bg-stone-900/80 transition-all font-medium whitespace-nowrap shrink-0"
          >
            Pháp Hành (Vipassanā)
          </Link>
          <Link
            href="/theravada/danh-muc/kinh-tung"
            class="px-3 sm:px-4 py-2 rounded-2xl text-xs sm:text-sm font-serif text-stone-300 hover:text-amber-200 hover:bg-stone-900/80 transition-all font-medium whitespace-nowrap shrink-0"
          >
            Kinh Tụng
          </Link>
          <Link
            href="/theravada/ung-dung-tu-hoc"
            class="px-3 sm:px-4 py-2 rounded-2xl text-xs sm:text-sm font-serif text-amber-300 hover:text-amber-100 bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/30 transition-all font-bold whitespace-nowrap shrink-0"
          >
            ✨ Ứng Dụng Tu Học
          </Link>
          <Link
            href="/theravada/tu-dien-pali"
            class="px-3 sm:px-4 py-2 rounded-2xl text-xs sm:text-sm font-serif text-stone-300 hover:text-amber-200 hover:bg-stone-900/80 transition-all font-medium whitespace-nowrap shrink-0"
          >
            Từ Điển Pāḷi
          </Link>
        </nav>
      </div>
    </header>

    <!-- Main Content Stage -->
    <main class="relative z-10 flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10">
      <slot />
    </main>

    <!-- 2. Zen Footer -->
    <footer class="relative z-10 border-t border-stone-800/80 bg-stone-950/95 py-12 px-4 sm:px-6 lg:px-8 text-center font-serif text-stone-400 text-sm">
      <div class="max-w-4xl mx-auto flex flex-col items-center gap-5">
        <!-- Dharma Lotus Seal -->
        <div class="flex items-center justify-center gap-3 text-amber-400 text-xl">
          <span>🌸</span>
          <span class="h-px w-20 bg-amber-500/40" />
          <ZenMascotLogo :size="40" />
          <span class="h-px w-20 bg-amber-500/40" />
          <span>🌸</span>
        </div>

        <p class="italic text-stone-300 max-w-2xl leading-relaxed text-base">
          "Sabbapāpassa akaraṇaṃ, kusalassa upasampadā; Sacittapariyodapanaṃ, etaṃ buddhāna sāsanaṃ."<br />
          <span class="text-amber-300 text-xs sm:text-sm not-italic mt-1 block">
            (Không làm mọi điều ác, Thành tựu các hạnh lành, Giữ tâm ý trong sạch, Đó lời chư Phật dạy — Kinh Pháp Cú 183)
          </span>
        </p>

        <div class="flex flex-wrap items-center justify-center gap-4 text-xs sm:text-sm text-stone-400 pt-4 border-t border-stone-900 w-full font-serif">
          <span>© 2026 Ma Tọa Thiền • Theravāda Dhamma • macatung.dev</span>
          <span>•</span>
          <Link href="/theravada/danh-muc/phap-hoc" class="hover:text-amber-300">Pháp Học</Link>
          <span>•</span>
          <Link href="/theravada/danh-muc/phap-hanh" class="hover:text-amber-300">Thiền Vipassanā</Link>
          <span>•</span>
          <Link href="/theravada/danh-muc/kinh-tung" class="hover:text-amber-300">Kinh Tụng</Link>
          <span>•</span>
          <Link href="/theravada/ung-dung-tu-hoc" class="hover:text-amber-300">Ứng Dụng</Link>
          <span>•</span>
          <Link href="/theravada/tu-dien-pali" class="hover:text-amber-300">Từ Điển Pāḷi</Link>
          <span>•</span>
          <Link href="/" class="hover:text-amber-300 font-sans">macatung.dev</Link>
        </div>
      </div>
    </footer>

    <!-- Interactive Pāḷi Glossary Modal -->
    <PaliGlossaryModal
      :is-open="isGlossaryOpen"
      @close="isGlossaryOpen = false"
    />
  </div>
</template>
