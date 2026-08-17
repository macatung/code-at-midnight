<script setup lang="ts">
import { ref } from 'vue';
import { sound } from '@/audio/soundEffects';

interface Props {
  animated?: boolean;
  size?: 'sm' | 'md' | 'lg';
  enableSound?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  animated: true,
  size: 'md',
  enableSound: true,
});

const emit = defineEmits<{
  (e: 'hop'): void;
}>();

const isJumping = ref(false);

const handleHopClick = () => {
  if (isJumping.value) return;

  isJumping.value = true;
  if (props.enableSound) {
    sound.playHop(1.35);
  }
  emit('hop');

  setTimeout(() => {
    isJumping.value = false;
  }, 420);
};

const sizeClasses = {
  sm: 'w-8 h-8',
  md: 'w-10 h-10',
  lg: 'w-12 h-12',
};
</script>

<template>
  <div
    class="relative select-none flex items-center justify-center rounded-xl bg-midnight-900 border border-phantom-mint/40 shadow-glow-mint overflow-hidden group/logo cursor-pointer transition-all hover:border-phantom-mint hover:scale-105 active:scale-95"
    :class="[
      sizeClasses[props.size],
      isJumping ? '-translate-y-1.5 shadow-[0_0_25px_rgba(0,245,160,0.7)]' : ''
    ]"
    role="button"
    aria-label="Ma Cà Tưng Logo Mascot"
    @click="handleHopClick"
  >
    <!-- Background Gradient Glow -->
    <div class="absolute inset-0 bg-gradient-to-b from-phantom-mint/10 via-transparent to-midnight-950/80 pointer-events-none" />

    <!-- Animated Hopping Mascot Container -->
    <div
      class="w-full h-full flex items-center justify-center p-0.5 transition-transform duration-200"
      :class="[
        props.animated && !isJumping ? 'animate-mini-hop group-hover/logo:animate-hop-fast' : '',
        isJumping ? 'scale-110 -translate-y-1' : ''
      ]"
    >
      <!-- Crisp Vector SVG Mascot -->
      <svg
        class="w-full h-full filter drop-shadow-[0_2px_6px_rgba(0,245,160,0.4)]"
        viewBox="0 0 100 115"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
      >
        <defs>
          <linearGradient id="miniRobeGrad" x1="0%" y1="0%" x2="0%" y2="100%">
            <stop offset="0%" stop-color="#151d33" />
            <stop offset="60%" stop-color="#0c1220" />
            <stop offset="100%" stop-color="#04070d" />
          </linearGradient>
          <linearGradient id="miniHatGrad" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#1e293b" />
            <stop offset="100%" stop-color="#070b14" />
          </linearGradient>
          <linearGradient id="miniTalismanGrad" x1="0%" y1="0%" x2="0%" y2="100%">
            <stop offset="0%" stop-color="#ffe57f" />
            <stop offset="50%" stop-color="#ffd166" />
            <stop offset="100%" stop-color="#f59e0b" />
          </linearGradient>
          <radialGradient id="miniGhostSkin" cx="50%" cy="40%" r="60%">
            <stop offset="0%" stop-color="#e2fbeb" />
            <stop offset="70%" stop-color="#b7e4c7" />
            <stop offset="100%" stop-color="#74c69d" />
          </radialGradient>
        </defs>

        <!-- Dynamic Ground Shadow -->
        <ellipse
          cx="50"
          cy="110"
          :rx="isJumping ? 12 : 22"
          :ry="isJumping ? 2 : 4"
          fill="rgba(0, 0, 0, 0.55)"
        />

        <!-- Outstretched Arms -->
        <g class="animate-talisman-flutter origin-center">
          <!-- Left Arm -->
          <path
            d="M32 66 C18 66 12 70 8 72"
            stroke="#0c1220"
            stroke-width="7"
            stroke-linecap="round"
          />
          <circle cx="8" cy="72" r="4" fill="#00f5a0" />

          <!-- Right Arm -->
          <path
            d="M68 66 C82 66 88 70 92 72"
            stroke="#0c1220"
            stroke-width="7"
            stroke-linecap="round"
          />
          <circle cx="92" cy="72" r="4" fill="#00f5a0" />
        </g>

        <!-- Robe Body -->
        <path
          d="M32 58 C32 58, 40 56, 50 56 C60 56, 68 58, 68 58 L76 102 C76 102, 50 106, 24 102 Z"
          fill="url(#miniRobeGrad)"
          stroke="#00f5a0"
          stroke-width="1.2"
        />
        <!-- Robe Collar -->
        <path d="M42 59 L50 71 L58 59" stroke="#00f5a0" stroke-width="1.5" fill="none" stroke-linecap="round" />
        <polygon points="50,75 55,78 55,84 50,87 45,84 45,78" fill="#070b14" stroke="#ffd166" stroke-width="0.8" />
        <text x="50" y="83" text-anchor="middle" font-size="4" font-family="monospace" font-weight="bold" fill="#00f5a0">{ }</text>

        <!-- Feet -->
        <ellipse cx="40" cy="103" rx="6" ry="3" fill="#070b14" stroke="#00f5a0" stroke-width="0.8" />
        <ellipse cx="60" cy="103" rx="6" ry="3" fill="#070b14" stroke="#00f5a0" stroke-width="0.8" />

        <!-- Ghost Head -->
        <circle cx="50" cy="42" r="21" fill="url(#miniGhostSkin)" stroke="#00f5a0" stroke-width="1.2" />

        <!-- Headphones -->
        <path d="M28 42 C28 24, 72 24, 72 42" stroke="#11182c" stroke-width="3.5" fill="none" stroke-linecap="round" />
        <rect x="25" y="36" width="5" height="13" rx="2.5" fill="#00f5a0" stroke="#070b14" stroke-width="0.8" />
        <rect x="70" y="36" width="5" height="13" rx="2.5" fill="#00f5a0" stroke="#070b14" stroke-width="0.8" />

        <!-- Mandarin Hat -->
        <path d="M30 30 C31 12, 69 12, 70 30 Z" fill="url(#miniHatGrad)" stroke="#ffd166" stroke-width="1.2" />
        <ellipse cx="50" cy="30" rx="23" ry="6" fill="#0c1220" stroke="#ffd166" stroke-width="1.2" />
        <!-- Hat Gem -->
        <circle cx="50" cy="22" r="2.8" fill="#ff0054" stroke="#ffd166" stroke-width="0.8" />
        <!-- Antenna -->
        <line x1="50" y1="19" x2="50" y2="10" stroke="#00f5a0" stroke-width="1.2" />
        <circle cx="50" cy="9" r="2" fill="#00f5a0" class="animate-pulse" />

        <!-- Cheeks -->
        <ellipse cx="38" cy="51" rx="3" ry="1.8" fill="#ff0054" opacity="0.4" />
        <ellipse cx="62" cy="51" rx="3" ry="1.8" fill="#ff0054" opacity="0.4" />

        <!-- Eyes -->
        <circle cx="42" cy="43" r="2.8" fill="#00f5a0" />
        <circle cx="43" cy="42" r="1" fill="#ffffff" />
        <circle cx="58" cy="43" r="2.8" fill="#00f5a0" />
        <circle cx="59" cy="42" r="1" fill="#ffffff" />

        <!-- Vampire Fangs Mouth -->
        <path d="M46 52 Q50 56 54 52" stroke="#070b14" stroke-width="1.2" fill="none" stroke-linecap="round" />
        <polygon points="47,52 48.5,55 50,52" fill="#ffffff" />
        <polygon points="50,52 51.5,55 53,52" fill="#ffffff" />

        <!-- Yellow Paper Forehead Talisman -->
        <g class="animate-talisman-flutter origin-top">
          <rect x="43" y="24" width="14" height="28" rx="1.5" fill="url(#miniTalismanGrad)" stroke="#c9182b" stroke-width="0.8" />
          <!-- Seal Circle -->
          <circle cx="50" cy="29" r="2.5" fill="#c9182b" />
          <text x="50" y="30.5" text-anchor="middle" font-size="3" font-family="monospace" font-weight="bold" fill="#ffd166">&lt;/&gt;</text>
          <!-- Code Inscription -->
          <text x="50" y="38" text-anchor="middle" font-size="3.5" font-family="monospace" font-weight="bold" fill="#c9182b">0 BUG</text>
          <path d="M46 44 L50 46 L54 44" stroke="#c9182b" stroke-width="0.8" stroke-linecap="round" fill="none" />
        </g>
      </svg>
    </div>
  </div>
</template>
