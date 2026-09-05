<script setup lang="ts">
import { computed } from 'vue';
import { useZenTimeCycle } from '@/composables/useZenTimeCycle';

const props = withDefaults(
  defineProps<{
    size?: number | string;
  }>(),
  {
    size: 48,
  }
);

const { activeZenPhase } = useZenTimeCycle();

const computedSize = computed(() => {
  return typeof props.size === 'number' ? `${props.size}px` : props.size;
});
</script>

<template>
  <div
    class="relative flex items-center justify-center select-none group shrink-0"
    :style="{ width: computedSize, height: computedSize }"
  >
    <!-- Dynamic Ambient Glow based on active phase -->
    <div
      class="absolute -inset-1 rounded-full blur-md transition-all duration-700 pointer-events-none opacity-80 group-hover:opacity-100 group-hover:blur-lg"
      :style="{ backgroundColor: activeZenPhase.accentGlow }"
    />

    <!-- Golden Emblem Border & Drop Shadow -->
    <div
      class="relative z-10 w-full h-full rounded-full p-0.5 bg-gradient-to-b from-amber-300/40 via-amber-500/20 to-yellow-600/30 border border-amber-400/40 shadow-[0_4px_16px_rgba(245,158,11,0.25)] transition-all duration-300 group-hover:scale-105 group-hover:border-amber-300/70 group-hover:shadow-[0_6px_22px_rgba(245,158,11,0.4)] flex items-center justify-center overflow-hidden"
    >
      <picture class="w-full h-full flex items-center justify-center">
        <source srcset="/brand/theravada/logo-ma-toa-thien.webp" type="image/webp" />
        <img
          src="/brand/theravada/logo-ma-toa-thien-256x256.png"
          srcset="/brand/theravada/logo-ma-toa-thien-128x128.png 128w, /brand/theravada/logo-ma-toa-thien-256x256.png 256w, /brand/theravada/logo-ma-toa-thien-512x512.png 512w"
          sizes="(max-width: 640px) 44px, 56px"
          alt="Ma Tọa Thiền • Theravāda"
          class="w-full h-full object-contain rounded-full select-none transform transition-transform duration-300"
          loading="eager"
          decoding="async"
        />
      </picture>
    </div>
  </div>
</template>

