<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import Icons from '@/Components/ui/Icons.vue';

const currentTime = ref<Date>(new Date());
const pingMs = ref<number>(14);
let intervalId: number | undefined;

const pad = (n: number) => String(n).padStart(2, '0');

const formattedTime = computed(() => {
  const h = pad(currentTime.value.getHours());
  const m = pad(currentTime.value.getMinutes());
  const s = pad(currentTime.value.getSeconds());
  return `${h}:${m}:${s}`;
});

const isMidnightMode = computed(() => {
  const hours = currentTime.value.getHours();
  return hours >= 0 && hours < 5;
});

const statusBadge = computed(() => {
  if (isMidnightMode.value) {
    return { mode: 'midnight' as const, text: '🌙 Midnight Mode — Maximum Focus & Flow' };
  }
  return { mode: 'daylight' as const, text: '☀️ Daylight Prep — Charging & Rebuilding' };
});

const caffeineLevel = computed(() => {
  const hour = currentTime.value.getHours();
  if (hour >= 1 && hour <= 4) return 100;
  if (hour === 0 || hour === 5) return 85;
  if (hour >= 6 && hour <= 9) return 40;
  if (hour >= 10 && hour <= 17) return 25;
  if (hour >= 18 && hour <= 21) return 50;
  return 75;
});

onMounted(() => {
  intervalId = window.setInterval(() => {
    currentTime.value = new Date();
    // Natural sub-30ms ping
    pingMs.value = Math.max(8, Math.min(32, Math.floor(12 + Math.random() * 6)));
  }, 1000);
});

onUnmounted(() => {
  if (intervalId) clearInterval(intervalId);
});
</script>

<template>
  <div
    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full glass-panel border border-white/10 shadow-inner text-xs font-mono select-none transition-all hover:border-phantom-mint/30"
    :title="`${statusBadge.text} | Caffeine: ${caffeineLevel}% | Latency: ${pingMs}ms`"
  >
    <!-- Live Pulse & Time (Always Visible) -->
    <div class="flex items-center gap-1.5 whitespace-nowrap">
      <span
        class="w-2 h-2 rounded-full transition-colors flex-shrink-0"
        :class="isMidnightMode ? 'bg-phantom-mint animate-pulse shadow-glow-mint' : 'bg-talisman-gold animate-pulse shadow-glow-talisman'"
      />
      <span class="font-bold text-slate-100 tracking-wider tabular-nums text-[11px] sm:text-xs">
        {{ formattedTime }}
      </span>
    </div>

    <!-- Mode Badge (Hidden on medium screens to keep header compact, visible on 2xl) -->
    <div class="hidden 2xl:flex items-center gap-1 text-[11px] text-slate-300 border-l border-white/10 pl-2 whitespace-nowrap">
      <span v-if="isMidnightMode" class="text-phantom-mint font-semibold">Midnight Mode</span>
      <span v-else class="text-talisman-gold font-semibold">Daylight Prep</span>
    </div>

    <!-- Caffeine Tracker (Hidden on medium/large screens to avoid crowding) -->
    <div class="hidden 2xl:flex items-center gap-1 text-[11px] text-slate-300 border-l border-white/10 pl-2 whitespace-nowrap">
      <Icons name="Coffee" :size="12" class="text-amber-400" />
      <span class="text-amber-300 font-bold tabular-nums">{{ caffeineLevel }}%</span>
    </div>
  </div>
</template>
