<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import MidnightClock from '@/Components/mascot/MidnightClock.vue';
import MiniMascotLogo from '@/Components/mascot/MiniMascotLogo.vue';
import SoundToggle from '@/Components/layout/SoundToggle.vue';
import Icons from '@/Components/ui/Icons.vue';
import { sound } from '@/audio/soundEffects';

interface NavItem {
  label: string;
  href: string;
  badge?: string;
}

const navLinks: NavItem[] = [
  { label: 'Dự Án', href: '#projects' },
  { label: 'Kỹ Năng', href: '#skills' },
  { label: 'Kinh Nghiệm', href: '#experience' },
  { label: 'Triết Lý', href: '#about' },
  { label: 'Bùa Dev', href: '#talisman', badge: 'HOT' },
  { label: 'Terminal', href: '#terminal', badge: 'CLI' },
];

const isScrolled = ref(false);
const isMobileDrawerOpen = ref(false);

const handleScroll = () => {
  if (typeof window !== 'undefined') {
    isScrolled.value = window.scrollY > 40;
  }
};

const toggleMobileDrawer = () => {
  isMobileDrawerOpen.value = !isMobileDrawerOpen.value;
  sound.playClick();
};

const closeMobileDrawer = () => {
  isMobileDrawerOpen.value = false;
};

const handleNavClick = () => {
  closeMobileDrawer();
  sound.playClick();
};

onMounted(() => {
  if (typeof window !== 'undefined') {
    window.addEventListener('scroll', handleScroll, { passive: true });
  }
});

onUnmounted(() => {
  if (typeof window !== 'undefined') {
    window.removeEventListener('scroll', handleScroll);
  }
});
</script>

<template>
  <header
    class="sticky top-0 z-40 transition-all duration-300 border-b"
    :class="isScrolled
      ? 'bg-midnight-950/95 backdrop-blur-xl border-white/10 shadow-xl shadow-black/50 py-2.5'
      : 'bg-midnight-950/80 backdrop-blur-md border-white/5 py-3.5'"
  >
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between gap-4">
      
      <!-- Brand Logo with Animated Hopping Mascot -->
      <a
        href="#hero"
        class="flex items-center gap-3 select-none group focus:outline-none flex-shrink-0"
        @click="sound.playHop(1.3)"
      >
        <!-- Animated Mini Vector Mascot Badge -->
        <MiniMascotLogo size="md" :animated="true" />

        <div class="flex flex-col">
          <span class="font-display font-bold text-base sm:text-lg tracking-tight text-white flex items-center group-hover:text-phantom-mint transition-colors">
            macatung<span class="text-phantom-mint">.dev</span>
          </span>
          <span class="text-[10px] font-mono text-slate-400 -mt-0.5 tracking-wider hidden sm:inline-block">
            Code at midnight
          </span>
        </div>
      </a>

      <!-- Desktop Nav Items (Centered & Clean with whitespace-nowrap) -->
      <nav class="hidden lg:flex items-center gap-1 xl:gap-2 px-3 py-1.5 rounded-2xl glass-panel border border-white/5" aria-label="Main Navigation">
        <a
          v-for="item in navLinks"
          :key="item.href"
          :href="item.href"
          class="px-3 py-1.5 rounded-xl text-xs xl:text-sm font-sans font-medium text-slate-300 hover:text-white hover:bg-white/10 transition-all relative flex items-center gap-1.5 whitespace-nowrap focus:outline-none"
          @click="sound.playClick()"
        >
          <span>{{ item.label }}</span>
          <span
            v-if="item.badge"
            class="text-[9px] font-mono px-1.5 py-0.5 rounded font-bold uppercase tracking-wider whitespace-nowrap"
            :class="item.badge === 'HOT'
              ? 'bg-amber-500/20 text-talisman-gold border border-talisman-gold/40'
              : 'bg-purple-500/20 text-purple-300 border border-purple-400/40'"
          >
            {{ item.badge }}
          </span>
        </a>
      </nav>

      <!-- Right Actions: Clock, Sound Toggle, CTA & Mobile Toggle -->
      <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">
        <!-- Compact Midnight Clock -->
        <MidnightClock class="hidden md:inline-flex" />

        <!-- Sound Toggle -->
        <SoundToggle />

        <!-- High-Impact Primary CTA Button -->
        <a
          href="#contact"
          class="hidden sm:inline-flex px-4 py-2 rounded-xl bg-phantom-mint text-midnight-950 text-xs font-mono font-extrabold hover:brightness-110 active:scale-95 transition-all shadow-glow-mint min-h-[40px] items-center gap-1.5 whitespace-nowrap"
          @click="sound.playTalisman()"
        >
          <span>Triệu Hồi</span>
          <span>⚡</span>
        </a>

        <!-- Mobile Menu Toggle Button -->
        <button
          type="button"
          class="lg:hidden p-2 rounded-xl text-slate-300 hover:text-white hover:bg-white/5 transition-colors min-h-[40px] min-w-[40px] flex items-center justify-center border border-white/10"
          :aria-expanded="isMobileDrawerOpen"
          aria-label="Mở Menu Điều Hướng"
          @click="toggleMobileDrawer"
        >
          <Icons v-if="!isMobileDrawerOpen" name="Menu" :size="20" />
          <Icons v-else name="X" :size="20" />
        </button>
      </div>
    </div>

    <!-- Mobile Drawer Overlay & Sheet -->
    <div
      v-if="isMobileDrawerOpen"
      class="fixed inset-0 z-50 bg-midnight-950/95 backdrop-blur-2xl lg:hidden flex flex-col p-6 overflow-y-auto animate-fadeIn"
      role="dialog"
      aria-modal="true"
    >
      <!-- Mobile Drawer Header -->
      <div class="flex items-center justify-between pb-6 border-b border-white/10">
        <a
          href="#hero"
          class="flex items-center gap-3"
          @click="handleNavClick"
        >
          <MiniMascotLogo size="sm" :animated="true" />
          <span class="font-display font-bold text-lg text-white">
            macatung<span class="text-phantom-mint">.dev</span>
          </span>
        </a>
        <button
          type="button"
          class="p-2 rounded-xl text-slate-400 hover:text-white bg-white/5 min-h-[44px] min-w-[44px] flex items-center justify-center"
          aria-label="Đóng Menu"
          @click="closeMobileDrawer"
        >
          <Icons name="X" :size="20" />
        </button>
      </div>

      <!-- Mobile Clock Pill -->
      <div class="py-4 flex justify-center">
        <MidnightClock />
      </div>

      <!-- Mobile Navigation Links -->
      <nav class="flex flex-col gap-2 py-4" aria-label="Mobile Navigation">
        <a
          v-for="item in navLinks"
          :key="item.href"
          :href="item.href"
          class="text-base font-medium text-slate-200 hover:text-phantom-mint py-3 px-4 rounded-xl hover:bg-white/5 transition-all flex items-center justify-between min-h-[44px]"
          @click="handleNavClick"
        >
          <span class="font-sans">{{ item.label }}</span>
          <span
            v-if="item.badge"
            class="text-[10px] font-mono px-2 py-0.5 rounded font-bold uppercase"
            :class="item.badge === 'HOT' ? 'bg-amber-500/20 text-talisman-gold' : 'bg-purple-500/20 text-purple-300'"
          >
            {{ item.badge }}
          </span>
        </a>
      </nav>

      <!-- Mobile Drawer Footer Actions -->
      <div class="mt-auto pt-6 border-t border-white/10 flex flex-col gap-3">
        <a
          href="#contact"
          class="w-full py-3.5 rounded-2xl bg-phantom-mint text-midnight-950 font-display font-extrabold text-sm text-center shadow-glow-mint min-h-[48px] flex items-center justify-center gap-2"
          @click="handleNavClick"
        >
          <span>Triệu Hồi Ngay 🚀</span>
        </a>
      </div>
    </div>
  </header>
</template>
