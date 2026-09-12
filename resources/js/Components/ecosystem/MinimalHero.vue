<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import MiniMascotLogo from '@/Components/mascot/MiniMascotLogo.vue';
import { sound } from '@/audio/soundEffects';
import { useI18n } from '@/composables/useI18n';

const { locale } = useI18n();
const isVi = computed(() => locale.value === 'vi');

const scrollToProducts = (e: Event) => {
  e.preventDefault();
  sound.playClick();
  const el = document.getElementById('products-section');
  if (el) {
    el.scrollIntoView({ behavior: 'smooth' });
  }
};
</script>

<template>
  <section class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-14 sm:pt-24 sm:pb-20 text-center flex flex-col items-center">
    <!-- Subtle Ambient Glow -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[300px] bg-phantom-mint/10 blur-[120px] rounded-full pointer-events-none -z-10" />

    <!-- Top Badge with Mascot Logo -->
    <div 
      class="inline-flex items-center gap-2.5 px-4 py-1.5 rounded-full border border-phantom-mint/30 bg-phantom-mint/10 text-xs font-mono text-phantom-mint mb-8 shadow-sm transition-transform hover:scale-105 cursor-pointer select-none"
      @click="sound.playHop(1.2)"
    >
      <MiniMascotLogo size="sm" :animated="true" />
      <span class="tracking-wide uppercase font-semibold">
        {{ isVi ? 'Hệ Sinh Thái Sản Phẩm Số' : 'Digital Software Ecosystem' }}
      </span>
    </div>

    <!-- Main Title -->
    <h1 class="text-4xl sm:text-6xl lg:text-7xl font-display font-black tracking-tight text-white leading-[1.1] max-w-4xl">
      <span v-if="isVi">
        Không Gian Số & Nền Tảng Kỹ Thuật <span class="text-transparent bg-clip-text bg-gradient-to-r from-phantom-mint via-teal-300 to-cyan-400">MacaTung</span>
      </span>
      <span v-else>
        The <span class="text-transparent bg-clip-text bg-gradient-to-r from-phantom-mint via-teal-300 to-cyan-400">MacaTung</span> Digital Ecosystem & Tools
      </span>
    </h1>

    <!-- Subtitle -->
    <p class="mt-6 text-base sm:text-lg text-slate-300 max-w-2xl leading-relaxed font-normal">
      {{ isVi 
        ? 'Quy tụ các nền tảng thực tế độc bản: Trợ lý AI Task Companion cho Windows, chuỗi podcast giải mã Decode, cổng hoàn tiền Shopee và không gian Phật giáo Theravāda.' 
        : 'A refined suite of production platforms: Native Windows Task Companion, Decode systems analysis, Shopee cashback utility, and canonical Theravāda studies.' 
      }}
    </p>

    <!-- Action Buttons -->
    <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
      <a
        href="#products-section"
        class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl bg-phantom-mint text-midnight-950 font-bold text-sm sm:text-base shadow-glow-mint hover:bg-phantom-mint/90 hover:-translate-y-0.5 transition-all duration-200 cursor-pointer"
        @click="scrollToProducts"
      >
        <span>{{ isVi ? 'Khám Phá Sản Phẩm' : 'Explore Platforms' }}</span>
        <span class="text-lg leading-none">↓</span>
      </a>

      <Link
        href="/desktop"
        class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl border border-white/15 bg-white/5 text-white font-semibold text-sm sm:text-base hover:bg-white/10 hover:border-phantom-mint/40 hover:-translate-y-0.5 transition-all duration-200"
        @click="sound.playClick()"
      >
        <span>💻</span>
        <span>{{ isVi ? 'Tải Task Companion (Win)' : 'Task Companion (Win)' }}</span>
      </Link>
    </div>

    <!-- Clean Product Highlights Pills -->
    <div class="mt-12 flex flex-wrap items-center justify-center gap-2 text-xs font-mono text-slate-400">
      <span class="px-3 py-1 rounded-lg bg-midnight-900/80 border border-white/5">🪷 Theravāda</span>
      <span class="px-3 py-1 rounded-lg bg-midnight-900/80 border border-white/5">🎙️ Decode CAD</span>
      <span class="px-3 py-1 rounded-lg bg-midnight-900/80 border border-white/5">🛍️ Shopee Hoàn Tiền</span>
      <span class="px-3 py-1 rounded-lg bg-midnight-900/80 border border-white/5">🖥️ Task Companion</span>
      <span class="px-3 py-1 rounded-lg bg-midnight-900/80 border border-white/5">🎮 Interactive Lab</span>
    </div>
  </section>
</template>
