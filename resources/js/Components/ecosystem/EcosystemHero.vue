<script setup lang="ts">
import { ref, computed } from 'vue';
import { useI18n } from '@/composables/useI18n';
import { useTimeCycle } from '@/composables/useTimeCycle';
import Icons from '@/Components/ui/Icons.vue';
import MacatungMascot from '@/Components/mascot/MacatungMascot.vue';
import { sound } from '@/audio/soundEffects';

const { locale } = useI18n();
const isVi = computed(() => locale.value === 'vi');
const { activePhase, formattedTime } = useTimeCycle();

const mascotMessage = ref(
  isVi.value
    ? 'Chào mừng bạn đến với hệ sinh thái MacaTung! Bấm vào mình để tương tác nhé. ⚡'
    : 'Welcome to the MacaTung ecosystem! Click me for audio interaction. ⚡'
);

const onMascotInteraction = () => {
  sound.playHop();
  mascotMessage.value = isVi.value
    ? 'Hệ sinh thái đang vận hành trơn tru với 5 trụ cột trực tuyến! 🚀'
    : 'All 5 ecosystem platforms are operating smoothly online! 🚀';
};

const playChimeSound = () => {
  sound.playCelestialChime(activePhase.value.id);
  mascotMessage.value = isVi.value
    ? `Âm hưởng hòa âm ${activePhase.value.name} đã được kích hoạt! 🎵`
    : `${activePhase.value.name} harmonic chime activated! 🎵`;
};

const stats = computed(() => [
  { value: '5', label: isVi.value ? 'Trụ Cột Trực Tuyến' : 'Active Platforms', sub: 'Web + Windows x64' },
  { value: '80%', label: isVi.value ? 'Chuyển Giao Hoàn Tiền' : 'Cashback Pass-Through', sub: isVi.value ? 'Minh bạch 0đ phí' : 'Direct to wallet' },
  { value: '< 18ms', label: isVi.value ? 'Độ Trễ Phục Vụ' : 'System Latency', sub: isVi.value ? 'Cache đa tầng & CDN' : 'Multi-tier cache' },
  { value: '100%', label: isVi.value ? 'Strict Type-Safe' : 'Strict Type-Safe', sub: isVi.value ? 'TypeScript & Laravel' : 'Robust Architecture' },
]);
</script>

<template>
  <header class="relative z-10 pt-10 pb-16 sm:pt-16 sm:pb-24 overflow-hidden" aria-label="Ecosystem Hero Section">
    <!-- Background Ambient Mesh -->
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[500px] bg-gradient-to-tr from-phantom-mint/10 via-cyan-500/10 to-transparent rounded-full blur-3xl pointer-events-none -z-10"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid items-center gap-12 lg:grid-cols-[1.15fr_.85fr] lg:gap-16">
        <!-- Hero Left Column: Brand & Copy -->
        <div>
          <!-- Live Ecosystem Status Pill -->
          <div class="inline-flex items-center gap-2 rounded-full border border-phantom-mint/30 bg-midnight-900/90 px-3.5 py-1.5 text-xs font-mono text-slate-200 backdrop-blur-md shadow-lg shadow-black/40">
            <span class="relative flex h-2 w-2">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-phantom-mint opacity-75"></span>
              <span class="relative inline-flex rounded-full h-2 w-2 bg-phantom-mint"></span>
            </span>
            <span class="font-bold text-phantom-mint">{{ isVi ? 'MACATUNG ECOSYSTEM' : 'MACATUNG ECOSYSTEM' }}</span>
            <span class="text-slate-600">·</span>
            <span>{{ isVi ? '5 Nền Tảng Trực Tuyến' : '5 Live Platforms' }}</span>
          </div>

          <!-- Hero Headline -->
          <h1 class="mt-6 text-4xl sm:text-6xl font-display font-black tracking-tight text-white leading-[1.1]">
            {{ isVi ? 'Hệ Sinh Thái Phần Mềm &' : 'The MacaTung Software' }}<br />
            <span class="bg-gradient-to-r from-phantom-mint via-cyan-300 to-amber-300 bg-clip-text text-transparent">
              {{ isVi ? 'Nền Tảng Công Nghệ Số' : 'Ecosystem & Product Suite' }}
            </span>
          </h1>

          <!-- Hero Subtitle -->
          <p class="mt-6 text-base sm:text-lg text-slate-300 leading-relaxed max-w-2xl">
            {{ isVi
              ? 'Không gian tích hợp các sản phẩm và nghiên cứu kỹ thuật số: ứng dụng desktop điều phối AI Agent cho Windows, khảo cứu chuyên sâu kiến trúc hệ thống ngầm toàn cầu, cổng hoàn tiền Shopee 80%, nền tảng Phật giáo Theravāda nguyên thủy và phòng thí nghiệm tương tác.'
              : 'A unified ecosystem of production platforms and digital research: native Windows AI agent orchestration, systemic architectural deep-dives, 80% Shopee cashback pass-through, canonical Theravāda Buddhist scholarship, and creative developer tools.'
            }}
          </p>

          <!-- Primary Actions -->
          <div class="mt-8 flex flex-wrap items-center gap-3">
            <a
              href="#ecosystem-grid"
              class="inline-flex items-center gap-2 rounded-xl bg-phantom-mint px-6 py-3.5 font-bold text-midnight-950 shadow-glow-mint transition-all duration-200 hover:-translate-y-0.5 hover:bg-emerald-400"
            >
              <span>{{ isVi ? 'Khám Phá 5 Trụ Cột' : 'Explore 5 Pillars' }}</span>
              <Icons name="ChevronDown" size="16" />
            </a>

            <a
              href="/desktop"
              class="inline-flex items-center gap-2 rounded-xl border border-white/15 bg-white/5 px-5 py-3.5 font-bold text-white backdrop-blur-sm transition-all duration-200 hover:border-phantom-mint/50 hover:bg-white/10"
            >
              <Icons name="Laptop" size="16" class="text-phantom-mint" />
              <span>{{ isVi ? 'Task Companion (Windows)' : 'Task Companion (Win)' }}</span>
            </a>

            <a
              href="/hoantien"
              class="inline-flex items-center gap-2 rounded-xl border border-orange-500/30 bg-orange-950/20 px-4 py-3.5 font-bold text-orange-300 transition-all duration-200 hover:border-orange-400 hover:bg-orange-950/40"
            >
              <span>🔥 {{ isVi ? 'Cổng Hoàn Tiền' : 'Cashback Portal' }}</span>
            </a>
          </div>

          <!-- Quick Metrics Bar -->
          <div class="mt-12 pt-8 border-t border-white/10 grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div v-for="stat in stats" :key="stat.label">
              <div class="text-2xl sm:text-3xl font-display font-black text-white tracking-tight">
                {{ stat.value }}
              </div>
              <div class="text-xs font-bold text-slate-200 mt-0.5">
                {{ stat.label }}
              </div>
              <div class="text-[11px] font-mono text-slate-500">
                {{ stat.sub }}
              </div>
            </div>
          </div>
        </div>

        <!-- Hero Right Column: Refined Mascot Stage with Audio Interaction -->
        <div class="relative flex flex-col items-center">
          <!-- Mascot Pedestal Glass Glow -->
          <div class="relative w-full max-w-md rounded-3xl border border-white/10 bg-midnight-900/60 p-6 sm:p-8 backdrop-blur-xl shadow-2xl shadow-black/80 flex flex-col items-center text-center">
            <!-- Phase & Time Status -->
            <div class="w-full flex items-center justify-between pb-4 mb-4 border-b border-white/10 text-xs font-mono text-slate-400">
              <span class="flex items-center gap-2">
                <span class="h-2 w-2 rounded-full animate-pulse" :style="{ backgroundColor: activePhase.accentHex }"></span>
                <span class="font-bold text-slate-200">{{ activePhase.name }}</span>
              </span>
              <span class="px-2 py-0.5 rounded bg-black/40 text-slate-300 font-mono">{{ formattedTime }}</span>
            </div>

            <!-- Mascot Speech Bubble -->
            <div class="relative mb-6 px-4 py-2.5 rounded-2xl bg-midnight-950/90 border border-white/10 text-xs text-slate-200 shadow-inner max-w-xs transition-all duration-300">
              <p>{{ mascotMessage }}</p>
              <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 w-0 h-0 border-l-[6px] border-l-transparent border-r-[6px] border-r-transparent border-t-[8px] border-t-midnight-950"></div>
            </div>

            <!-- Mascot Component (Clickable with Hop) -->
            <div
              @click="onMascotInteraction"
              class="cursor-pointer transition-transform duration-200 hover:scale-105 active:scale-95"
              :title="isVi ? 'Bấm vào Ma Cà Tưng để tương tác' : 'Click mascot to interact'"
            >
              <MacatungMascot size="hero" :showControls="false" />
            </div>

            <!-- Mascot Controls Pill -->
            <div class="mt-6 flex flex-wrap items-center justify-center gap-2 text-xs font-mono">
              <button
                type="button"
                @click="onMascotInteraction"
                class="px-3 py-1.5 rounded-xl border border-phantom-mint/30 bg-phantom-mint/10 hover:bg-phantom-mint/20 text-phantom-mint transition-colors flex items-center gap-1.5"
              >
                <Icons name="Zap" size="13" />
                <span>{{ isVi ? 'Nhảy Cùng Mascot' : 'Mascot Hop' }}</span>
              </button>

              <button
                type="button"
                @click="playChimeSound"
                class="px-3 py-1.5 rounded-xl border border-white/10 bg-white/5 hover:bg-white/10 text-slate-300 transition-colors flex items-center gap-1.5"
              >
                <Icons name="Sparkles" size="13" class="text-amber-400" />
                <span>{{ isVi ? 'Hòa Âm Chime' : 'Celestial Chime' }}</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>
