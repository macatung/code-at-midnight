<script setup lang="ts">
import { useZenTimeCycle } from '@/composables/useZenTimeCycle';
import { mindfulBell } from '@/audio/mindfulBellAudio';

const {
  currentHour,
  isRealTime,
  activeZenPhase,
  ZEN_PHASES,
  setSimulatedHour,
  resetToRealTime
} = useZenTimeCycle();

const handleSliderChange = (e: Event) => {
  const val = parseInt((e.target as HTMLInputElement).value, 10);
  setSimulatedHour(val);
  mindfulBell.strikeWoodenFish();
};

const handlePhaseClick = (h: number) => {
  setSimulatedHour(h);
  mindfulBell.ringBell(432, 4.0);
};

const handleResetRealTime = () => {
  resetToRealTime();
  mindfulBell.ringBell(432, 4.0);
};
</script>

<template>
  <div class="w-full max-w-3xl mx-auto my-8 p-5 sm:p-6 rounded-3xl bg-stone-900/90 border border-amber-500/30 shadow-2xl backdrop-blur-xl font-serif text-stone-100 relative overflow-hidden">
    <!-- Ambient Glow from current active phase -->
    <div
      class="absolute -right-10 -bottom-10 w-48 h-48 rounded-full blur-3xl opacity-25 pointer-events-none transition-all duration-700"
      :style="{ backgroundColor: activeZenPhase.accentHex }"
    />

    <div class="relative z-10 space-y-4">
      <!-- Header: Current Phase & Real-Time Sync -->
      <div class="flex flex-wrap items-center justify-between gap-3 border-b border-amber-500/20 pb-3.5">
        <div class="flex items-center gap-3">
          <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-amber-500/15 border border-amber-500/30 text-xl text-amber-300">
            {{ activeZenPhase.icon }}
          </span>
          <div>
            <div class="flex items-center gap-2">
              <h4 class="text-sm sm:text-base font-bold text-amber-200">
                {{ activeZenPhase.vietnameseName }}
              </h4>
              <span class="px-2 py-0.5 rounded-full text-[10px] font-sans font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40">
                {{ currentHour }}:00
              </span>
            </div>
            <p class="text-[11px] text-stone-400 italic">
              Pāḷi: {{ activeZenPhase.paliName }} • Khung giờ: {{ activeZenPhase.timeRange }}
            </p>
          </div>
        </div>

        <!-- Real-Time Sync Button -->
        <button
          v-if="!isRealTime"
          @click="handleResetRealTime"
          class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-500 text-stone-950 text-xs font-serif font-bold shadow-md hover:bg-amber-400 transition-all hover:scale-105 active:scale-95 cursor-pointer"
          title="Đồng bộ lại theo giờ thực tế của thiết bị"
        >
          <span>⏱️</span>
          <span>Đồng Bộ Giờ Thực</span>
        </button>
        <span v-else class="text-xs font-sans text-amber-400/80 flex items-center gap-1">
          <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
          <span>Đang theo Giờ Thực Tế</span>
        </span>
      </div>

      <!-- 24H Slider Control -->
      <div class="space-y-2">
        <div class="flex items-center justify-between text-[11px] font-mono text-stone-400">
          <span>00:00 (Khuya)</span>
          <span class="text-amber-300 font-bold font-serif text-xs">
            ⏳ Tua Giờ Thiền Môn 24H (Kéo thanh trượt để đổi thời khắc)
          </span>
          <span>23:00 (Tối)</span>
        </div>

        <input
          type="range"
          min="0"
          max="23"
          :value="currentHour"
          @input="handleSliderChange"
          class="w-full h-2.5 bg-stone-950 rounded-lg appearance-none cursor-pointer accent-amber-500 border border-stone-800 focus:outline-none"
        />
      </div>

      <!-- Quick Phase Jump Presets -->
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-2">
        <button
          @click="handlePhaseClick(2)"
          :class="[
            'flex items-center justify-center gap-1.5 p-2 rounded-xl text-xs font-serif transition-all border cursor-pointer',
            activeZenPhase.id === 'midnight'
              ? 'bg-amber-500/25 border-amber-400 text-amber-200 font-bold shadow-md'
              : 'bg-stone-950/60 border-stone-800 text-stone-400 hover:text-stone-200 hover:bg-stone-800'
          ]"
        >
          <span>🌌</span>
          <span>Dạ Khuya (02:00)</span>
        </button>

        <button
          @click="handlePhaseClick(7)"
          :class="[
            'flex items-center justify-center gap-1.5 p-2 rounded-xl text-xs font-serif transition-all border cursor-pointer',
            activeZenPhase.id === 'dawn'
              ? 'bg-amber-500/25 border-amber-400 text-amber-200 font-bold shadow-md'
              : 'bg-stone-950/60 border-stone-800 text-stone-400 hover:text-stone-200 hover:bg-stone-800'
          ]"
        >
          <span>🌅</span>
          <span>Bình Minh (07:00)</span>
        </button>

        <button
          @click="handlePhaseClick(14)"
          :class="[
            'flex items-center justify-center gap-1.5 p-2 rounded-xl text-xs font-serif transition-all border cursor-pointer',
            activeZenPhase.id === 'afternoon'
              ? 'bg-amber-500/25 border-amber-400 text-amber-200 font-bold shadow-md'
              : 'bg-stone-950/60 border-stone-800 text-stone-400 hover:text-stone-200 hover:bg-stone-800'
          ]"
        >
          <span>☀️</span>
          <span>Quá Ngọ (14:00)</span>
        </button>

        <button
          @click="handlePhaseClick(20)"
          :class="[
            'flex items-center justify-center gap-1.5 p-2 rounded-xl text-xs font-serif transition-all border cursor-pointer',
            activeZenPhase.id === 'twilight'
              ? 'bg-amber-500/25 border-amber-400 text-amber-200 font-bold shadow-md'
              : 'bg-stone-950/60 border-stone-800 text-stone-400 hover:text-stone-200 hover:bg-stone-800'
          ]"
        >
          <span>🕯️</span>
          <span>Hoàng Hôn (20:00)</span>
        </button>
      </div>

      <!-- Practice Advice for Current Time -->
      <div class="p-3.5 rounded-2xl bg-stone-950/80 border border-stone-800/80 text-xs flex items-start gap-2.5">
        <span class="text-amber-400 text-base shrink-0">🌸</span>
        <div class="leading-relaxed">
          <strong class="text-amber-300 font-serif">{{ activeZenPhase.practiceTitle }}:</strong>
          <span class="text-stone-300 ml-1">{{ activeZenPhase.practiceDesc }}</span>
        </div>
      </div>
    </div>
  </div>
</template>
