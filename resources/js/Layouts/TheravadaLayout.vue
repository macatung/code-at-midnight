<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import SeoHead from '@/Components/common/SeoHead.vue';
import ZenMascotLogo from '@/Components/theravada/ZenMascotLogo.vue';
import ZenBackgroundCanvas from '@/Components/theravada/ZenBackgroundCanvas.vue';
import PaliGlossaryModal from '@/Components/theravada/PaliGlossaryModal.vue';
import Icons from '@/Components/ui/Icons.vue';
import { mindfulBell } from '@/audio/mindfulBellAudio';
import { useZenTimeCycle } from '@/composables/useZenTimeCycle';
import { useI18n } from '@/composables/useI18n';

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

const page = usePage();
const isGlossaryOpen = ref(false);
const isMobileMenuOpen = ref(false);
const { activeZenPhase } = useZenTimeCycle();
const { locale, t, setLocale } = useI18n();

const navItems = computed(() => [
  { label: t('nav.home'), href: '/theravada', icon: 'Home' },
  { label: locale.value === 'en' ? 'Learn Pāḷi' : 'Học Pāḷi', href: '/theravada/hoc-pali', icon: 'GraduationCap', isHighlight: true },
  { label: locale.value === 'en' ? 'Dhamma Study' : 'Pháp Học', href: '/theravada/danh-muc/phap-hoc', icon: 'BookOpen' },
  { label: locale.value === 'en' ? 'Vipassanā Practice' : 'Pháp Hành (Vipassanā)', href: '/theravada/danh-muc/phap-hanh', icon: 'Sparkles' },
  { label: locale.value === 'en' ? 'Dharma Talks' : 'Pháp Thoại', href: '/theravada/danh-muc/phap-thoai', icon: 'Headphones' },
  { label: locale.value === 'en' ? 'Chanting' : 'Kinh Tụng', href: '/theravada/danh-muc/kinh-tung', icon: 'Scroll' },
  { label: locale.value === 'en' ? 'History' : 'Lịch Sử', href: '/theravada/danh-muc/lich-su', icon: 'Landmark' },
  { label: t('theravada.glossary'), href: '/theravada/tu-dien-pali', icon: 'Compass' },
]);

const isLinkActive = (href: string): boolean => {
  const currentUrl = page.url;
  if (href === '/theravada') return currentUrl === '/theravada';
  return currentUrl.startsWith(href);
};

const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value;
  mindfulBell.strikeWoodenFish();
};

const closeMobileMenu = () => {
  isMobileMenuOpen.value = false;
};

const handleNavClick = () => {
  closeMobileMenu();
  mindfulBell.strikeWoodenFish();
};

// Close on escape key
const handleKeyDown = (e: KeyboardEvent) => {
  if (e.key === 'Escape' && isMobileMenuOpen.value) {
    closeMobileMenu();
  }
};

onMounted(() => {
  if (typeof window !== 'undefined') {
    window.addEventListener('keydown', handleKeyDown);
  }
});

onUnmounted(() => {
  if (typeof window !== 'undefined') {
    window.removeEventListener('keydown', handleKeyDown);
  }
});
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
        class="absolute top-0 left-1/2 -translate-x-1/2 w-[350px] sm:w-[600px] lg:w-[800px] h-[300px] sm:h-[450px] rounded-full blur-[100px] sm:blur-[160px] opacity-20 transition-all duration-1000"
        :style="{ backgroundColor: activeZenPhase.accentHex }"
      />
      <div
        class="absolute bottom-0 right-0 sm:right-10 w-[300px] sm:w-[500px] lg:w-[600px] h-[300px] sm:h-[600px] rounded-full blur-[120px] sm:blur-[180px] opacity-15 transition-all duration-1000"
        :style="{ backgroundColor: activeZenPhase.accentHex }"
      />
    </div>

    <!-- Multi-Layer Zen Background Canvas (Dhamma Wheel, Petals, Bodhi Leaves & Incense Smoke) -->
    <ZenBackgroundCanvas />

    <!-- 1. Zen Top Navigation Header -->
    <header class="sticky top-0 z-40 w-full border-b border-amber-500/20 bg-stone-950/95 backdrop-blur-xl shadow-xl py-3 sm:py-4">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between gap-3 sm:gap-6">
        
        <!-- Brand / Zen Mascot Logo + Title: MA TỌA THIỀN -->
        <Link
          href="/theravada"
          class="flex items-center gap-2.5 sm:gap-3.5 group transition-transform duration-300 hover:scale-[1.02] shrink-0 min-w-0"
          @click="closeMobileMenu"
        >
          <!-- Mascot Tọa Thiền Tòa Sen Logo -->
          <ZenMascotLogo :size="42" class="sm:w-12 sm:h-12 shrink-0" />
          
          <div class="flex flex-col text-left justify-center min-w-0">
            <div class="flex items-center gap-1.5 sm:gap-2">
              <span class="text-base sm:text-xl lg:text-2xl font-serif font-bold text-amber-100 tracking-tight sm:tracking-wide truncate">
                {{ t('theravada.brand') }}
              </span>
              <span class="inline-block text-[10px] sm:text-[11px] font-sans px-2 py-0.5 rounded-full bg-gradient-to-r from-amber-500/20 to-yellow-500/20 text-amber-300 border border-amber-500/40 font-semibold shrink-0 shadow-sm">
                Theravāda
              </span>
            </div>
            <span class="text-[10px] sm:text-xs font-serif text-stone-400 italic truncate">
              {{ t('theravada.tagline') }}
            </span>
          </div>
        </Link>

        <!-- Desktop Navigation Links (visible on md: 768px and up) -->
        <nav class="hidden md:flex items-center gap-1 lg:gap-1.5 xl:gap-2 shrink-0" aria-label="Zen Desktop Navigation">
          <Link
            v-for="item in navItems"
            :key="item.href"
            :href="item.href"
            class="px-2.5 lg:px-3.5 xl:px-4 py-1.5 lg:py-2 rounded-2xl text-xs xl:text-sm font-serif transition-all font-semibold whitespace-nowrap shrink-0 focus:outline-none"
            :class="[
              item.isHighlight
                ? 'text-amber-300 hover:text-amber-100 bg-amber-500/15 hover:bg-amber-500/25 border border-amber-500/30 font-bold shadow-sm'
                : isLinkActive(item.href)
                  ? 'text-amber-300 bg-stone-900/90 border border-amber-500/40 shadow-inner'
                  : 'text-stone-200 hover:text-amber-300 hover:bg-stone-900/90'
            ]"
            @click="mindfulBell.strikeWoodenFish()"
          >
            <span>{{ item.isHighlight ? '✨ ' : '' }}{{ item.label }}</span>
          </Link>
        </nav>

        <div class="hidden md:flex items-center gap-2.5">
          <!-- YouTube Channel Link -->
          <a
            href="https://www.youtube.com/@matoathien"
            target="_blank"
            rel="noopener noreferrer"
            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border border-red-500/30 bg-stone-900/90 text-red-400 hover:text-red-300 hover:bg-red-500/15 hover:border-red-500/50 text-[11px] font-sans font-semibold transition-all duration-200 group shadow-sm shrink-0"
            title="Kênh YouTube Ma Tọa Thiền (@matoathien)"
            @click="mindfulBell.strikeWoodenFish()"
          >
            <svg class="w-3.5 h-3.5 text-red-500 group-hover:scale-110 transition-transform" viewBox="0 0 24 24" fill="currentColor">
              <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
            </svg>
            <span class="tracking-wide">YouTube</span>
          </a>

          <div class="flex items-center rounded-lg border border-amber-500/30 bg-stone-900/80 p-0.5 text-[10px] font-sans" role="group" aria-label="Language">
            <button type="button" class="px-2 py-1 rounded-md" :class="locale === 'en' ? 'bg-amber-400 text-stone-950 font-bold' : 'text-stone-400'" @click="setLocale('en')">EN</button>
            <button type="button" class="px-2 py-1 rounded-md" :class="locale === 'vi' ? 'bg-amber-400 text-stone-950 font-bold' : 'text-stone-400'" @click="setLocale('vi')">VI</button>
          </div>
        </div>

        <!-- Mobile Action Controls (visible only on < md: 768px) -->
        <div class="flex items-center gap-2 md:hidden shrink-0">
          <!-- Mobile Hamburger Drawer Toggle Button -->
          <button
            type="button"
            class="p-2.5 rounded-2xl bg-stone-900/90 border border-amber-500/30 text-amber-300 hover:text-amber-200 hover:bg-stone-800 transition-all min-h-[44px] min-w-[44px] flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-amber-400 cursor-pointer shadow-md"
            :aria-expanded="isMobileMenuOpen"
            aria-label="Toggle Mobile Menu"
            @click="toggleMobileMenu"
          >
            <Icons :name="isMobileMenuOpen ? 'X' : 'Menu'" :size="22" />
          </button>
        </div>

      </div>

      <!-- Dimmed Background Backdrop Overlay for Mobile Menu -->
      <transition
        enter-active-class="transition-opacity duration-300 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-opacity duration-200 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div
          v-if="isMobileMenuOpen"
          class="fixed inset-0 top-[62px] sm:top-[70px] bg-black/80 backdrop-blur-sm z-30 md:hidden"
          @click="closeMobileMenu"
        />
      </transition>

      <!-- Zen Mobile Slide-Down Navigation Drawer (100% Solid Opaque Dark Background) -->
      <transition
        enter-active-class="transition duration-250 ease-out"
        enter-from-class="opacity-0 -translate-y-4"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 -translate-y-4"
      >
        <div
          v-if="isMobileMenuOpen"
          class="md:hidden border-t border-b border-amber-500/30 bg-[#0c0a09] px-4 sm:px-6 py-5 space-y-3 absolute top-full left-0 w-full shadow-[0_25px_60px_rgba(0,0,0,0.95)] z-50 text-left max-h-[calc(100vh-75px)] overflow-y-auto"
          style="background-color: #0c0a09;"
        >
          <div class="grid grid-cols-1 gap-2">
            <Link
              v-for="item in navItems"
              :key="item.href"
              :href="item.href"
              class="flex items-center justify-between px-4 py-3 rounded-2xl text-sm font-serif font-semibold transition-all min-h-[46px] border"
              :class="[
                item.isHighlight
                  ? 'text-stone-950 bg-gradient-to-r from-amber-400 to-yellow-500 border-amber-400 font-bold shadow-md'
                  : isLinkActive(item.href)
                    ? 'text-amber-300 bg-amber-500/20 font-bold border-amber-500/50'
                    : 'text-stone-200 bg-stone-900/90 border-stone-800/80 hover:text-amber-300 hover:bg-stone-900 hover:border-amber-500/40'
              ]"
              @click="handleNavClick"
            >
              <div class="flex items-center gap-3">
                <span class="text-base">{{ item.isHighlight ? '✨' : item.icon === 'Home' ? '🏠' : item.icon === 'GraduationCap' ? '🎓' : item.icon === 'BookOpen' ? '📖' : item.icon === 'Sparkles' ? '🧘' : item.icon === 'Scroll' ? '📜' : item.icon === 'Landmark' ? '🏛️' : '☸️' }}</span>
                <span>{{ item.label }}</span>
              </div>
              <span class="text-xs opacity-60 font-mono">➔</span>
            </Link>
          </div>

          <div class="pt-3 border-t border-stone-800/80 flex flex-col gap-2">
            <a
              href="https://www.youtube.com/@matoathien"
              target="_blank"
              rel="noopener noreferrer"
              class="w-full py-3 px-4 rounded-2xl bg-gradient-to-r from-red-950/40 via-stone-900 to-stone-900 border border-red-500/30 text-red-300 hover:text-red-200 text-xs font-serif font-semibold text-center flex items-center justify-center gap-2 transition-all min-h-[44px]"
              @click="handleNavClick"
            >
              <svg class="w-4 h-4 text-red-500" viewBox="0 0 24 24" fill="currentColor">
                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
              </svg>
              <span>Kênh YouTube: Ma Tọa Thiền (@matoathien)</span>
            </a>

            <Link
              href="/"
              class="w-full py-3 px-4 rounded-2xl bg-stone-900 border border-stone-800 text-stone-300 hover:text-amber-300 hover:bg-stone-800 text-xs font-serif font-semibold text-center flex items-center justify-center gap-2 transition-all min-h-[44px]"
              @click="handleNavClick"
            >
              <span>🌐</span>
              <span>{{ t('theravada.home') }}</span>
            </Link>
          </div>
        </div>
      </transition>
    </header>

    <!-- Main Content Stage -->
    <main class="relative z-10 flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 sm:py-10">
      <slot />
    </main>

    <!-- 2. Zen Footer -->
    <footer class="relative z-10 border-t border-stone-800/90 bg-stone-950/98 py-10 sm:py-12 px-4 sm:px-6 lg:px-8 text-center font-serif text-stone-300 text-sm">
      <div class="max-w-4xl mx-auto flex flex-col items-center gap-4 sm:gap-5">
        <!-- Dharma Lotus Seal -->
        <div class="flex items-center justify-center gap-2 sm:gap-3 text-amber-400 text-base sm:text-xl">
          <span>🌸</span>
          <span class="h-px w-12 sm:w-20 bg-amber-500/40" />
          <ZenMascotLogo :size="36" class="sm:w-10 sm:h-10 shrink-0" />
          <span class="h-px w-12 sm:w-20 bg-amber-500/40" />
          <span>🌸</span>
        </div>

        <p class="italic text-stone-200 max-w-2xl leading-relaxed text-xs sm:text-base px-2">
          "Sabbapāpassa akaraṇaṃ, kusalassa upasampadā; Sacittapariyodapanaṃ, etaṃ buddhāna sāsanaṃ."<br />
          <span class="text-amber-300 text-[11px] sm:text-sm not-italic mt-1.5 block font-semibold leading-normal">
            (Avoid all evil, cultivate the good, purify the mind — the teaching of the Buddhas, Dhammapada 183)
          </span>
        </p>

        <!-- YouTube Channel Feature Badge in Footer -->
        <a
          href="https://www.youtube.com/@matoathien"
          target="_blank"
          rel="noopener noreferrer"
          class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-stone-900/90 hover:bg-red-950/40 border border-red-500/30 hover:border-red-500/60 text-red-300 hover:text-red-200 text-xs font-serif transition-all duration-300 shadow-sm group"
          @click="mindfulBell.strikeWoodenFish()"
        >
          <svg class="w-4 h-4 text-red-500 group-hover:scale-110 transition-transform" viewBox="0 0 24 24" fill="currentColor">
            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
          </svg>
          <span class="font-semibold tracking-wide">Kênh YouTube: Ma Tọa Thiền (@matoathien)</span>
          <span class="text-stone-400 text-[11px] group-hover:translate-x-0.5 transition-transform">➔</span>
        </a>

        <div class="flex flex-wrap items-center justify-center gap-2.5 sm:gap-4 text-xs sm:text-sm text-stone-300 pt-4 border-t border-stone-900 w-full font-serif leading-loose">
          <span class="w-full sm:w-auto font-medium">© 2026 Ma Tọa Thiền • Theravāda Dhamma • macatung.dev</span>
          <span class="hidden sm:inline">•</span>
          <Link href="/theravada/hoc-pali" class="hover:text-amber-300 font-semibold px-1 text-amber-300">{{ locale === 'en' ? 'Learn Pāḷi' : 'Học Pāḷi' }}</Link>
          <span>•</span>
          <Link href="/theravada/danh-muc/phap-hoc" class="hover:text-amber-300 font-semibold px-1">{{ locale === 'en' ? 'Dhamma Study' : 'Pháp Học' }}</Link>
          <span>•</span>
          <Link href="/theravada/danh-muc/phap-hanh" class="hover:text-amber-300 font-semibold px-1">{{ locale === 'en' ? 'Vipassanā Practice' : 'Thiền Vipassanā' }}</Link>
          <span>•</span>
          <Link href="/theravada/danh-muc/phap-thoai" class="hover:text-amber-300 font-semibold px-1">{{ locale === 'en' ? 'Dharma Talks' : 'Pháp Thoại' }}</Link>
          <span>•</span>
          <Link href="/theravada/danh-muc/kinh-tung" class="hover:text-amber-300 font-semibold px-1">{{ locale === 'en' ? 'Chanting' : 'Kinh Tụng' }}</Link>
          <span>•</span>
          <Link href="/theravada/danh-muc/lich-su" class="hover:text-amber-300 font-semibold px-1">{{ locale === 'en' ? 'Buddhist History' : 'Lịch Sử Phật Giáo' }}</Link>
          <span>•</span>
          <Link href="/theravada/tu-dien-pali" class="hover:text-amber-300 font-semibold px-1">{{ t('theravada.glossary') }}</Link>
          <span>•</span>
          <Link href="/" class="hover:text-amber-300 font-sans font-semibold px-1">macatung.dev</Link>
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
