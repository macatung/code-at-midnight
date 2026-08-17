<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import confetti from 'canvas-confetti';
import { sound } from '@/audio/soundEffects';
import { trackEvent } from '@/utils/analytics';

type Mood = 'normal' | 'caffeine' | 'sleepy' | 'rage';
type MascotSize = 'sm' | 'md' | 'lg' | 'hero';

interface Props {
  size?: MascotSize;
  showControls?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  size: 'hero',
  showControls: true,
});

const emit = defineEmits<{
  (e: 'hop-count-change', count: number): void;
  (e: 'mood-change', mood: Mood): void;
  (e: 'milestone', count: number): void;
  (e: 'hop-end'): void;
}>();

const hopCount = ref(0);
const isHopping = ref(false);
const mood = ref<Mood>('normal');
const currentQuoteIndex = ref(0);

const quotes = [
  'Code lúc nửa đêm là chân ái! 🌙',
  'Robusta 100% không đường, 0 bug! ☕',
  'Thứ Sáu deploy, thứ Bảy ngủ ngon! 🚀',
  'Bùa chú đã yểm vào từng dòng code! ✨',
  'Nhảy nhót tí cho tỉnh táo nào! 🧛‍♂️'
];

const currentQuote = computed(() => quotes[currentQuoteIndex.value % quotes.length]);

const getPitchMultiplier = (): number => {
  switch (mood.value) {
    case 'caffeine':
      return 1.35;
    case 'sleepy':
      return 0.75;
    case 'rage':
      return 1.8;
    default:
      return 1.0;
  }
};

const setMood = (newMood: Mood) => {
  if (['normal', 'caffeine', 'sleepy', 'rage'].includes(newMood)) {
    mood.value = newMood;
  } else {
    mood.value = 'normal';
  }
  emit('mood-change', mood.value);
  sound.playClick();
};

const triggerHop = () => {
  hopCount.value++;
  isHopping.value = true;

  try {
    if (typeof localStorage !== 'undefined') {
      localStorage.setItem('macatung_hop_count', String(hopCount.value));
    }
  } catch {
    // LocalStorage quota fallback
  }

  currentQuoteIndex.value = (currentQuoteIndex.value + 1) % quotes.length;
  emit('hop-count-change', hopCount.value);

  const pitch = getPitchMultiplier();
  sound.playHop(pitch);

  // Track analytics event
  trackEvent('hop_mascot', { hop_count: hopCount.value, mood: mood.value });

  // Milestone celebration on every multiple of 10
  if (hopCount.value > 0 && hopCount.value % 10 === 0) {
    emit('milestone', hopCount.value);
    sound.playSuccess();
    try {
      confetti({
        particleCount: 50,
        spread: 65,
        origin: { y: 0.65 },
        colors: ['#00f5a0', '#ffd166', '#ff0054', '#9d4edd'],
      });
    } catch {
      // Graceful fallback
    }
  }

  setTimeout(() => {
    isHopping.value = false;
    emit('hop-end');
  }, 450);
};

const handleTouchStart = (e: TouchEvent) => {
  if (e.touches && e.touches.length > 0) {
    triggerHop();
  }
};

onMounted(() => {
  if (typeof localStorage !== 'undefined') {
    const saved = localStorage.getItem('macatung_hop_count');
    if (saved) {
      const parsed = parseInt(saved, 10);
      if (!Number.isNaN(parsed) && parsed >= 0) {
        hopCount.value = parsed;
      }
    }
  }
});

// Size Dimensions Mapping
const dimensions = computed(() => {
  switch (props.size) {
    case 'sm':
      return { width: 'w-24', height: 'h-28', bubbleMax: 'max-w-[200px]' };
    case 'md':
      return { width: 'w-36', height: 'h-44', bubbleMax: 'max-w-[240px]' };
    case 'lg':
      return { width: 'w-52', height: 'h-64', bubbleMax: 'max-w-[280px]' };
    case 'hero':
    default:
      return { width: 'w-64 sm:w-72', height: 'h-72 sm:h-80', bubbleMax: 'max-w-[320px]' };
  }
});
</script>

<template>
  <div class="macatung-mascot-wrapper flex flex-col items-center select-none relative group">
    <!-- Speech Bubble (Visible on md, lg, hero sizes) -->
    <div
      v-if="size !== 'sm'"
      class="mb-3 px-4 py-2 rounded-2xl glass-panel-talisman border border-talisman-yellow/30 text-xs sm:text-sm font-sans font-medium text-amber-200/95 text-center shadow-lg transition-all duration-300 relative"
      :class="dimensions.bubbleMax"
    >
      <span>{{ currentQuote }}</span>
      <div class="absolute -bottom-1.5 left-1/2 -translate-x-1/2 w-3 h-3 bg-midnight-950 border-b border-r border-talisman-yellow/30 rotate-45" />
    </div>

    <!-- Interactive Mascot Stage with Tap/Click handlers -->
    <div
      class="mascot-avatar relative cursor-pointer flex flex-col items-center transition-transform duration-200 ease-out focus:outline-none"
      :class="[
        dimensions.width,
        dimensions.height,
        isHopping ? '-translate-y-8 scale-y-110 animate-squash-stretch' : 'hover:scale-105 active:scale-95'
      ]"
      tabindex="0"
      role="button"
      aria-label="Ma Cà Tưng mascot - click to hop"
      @click="triggerHop"
      @touchstart.passive="handleTouchStart"
      @keydown.space.prevent="triggerHop"
      @keydown.enter.prevent="triggerHop"
    >
      <!-- Jiangshi Cyber Mascot SVG (viewBox: 0 0 240 280) -->
      <svg
        class="mascot-svg w-full h-full filter drop-shadow-[0_10px_20px_rgba(0,245,160,0.25)]"
        viewBox="0 0 240 280"
        xmlns="http://www.w3.org/2000/svg"
      >
        <defs>
          <linearGradient id="robeGrad" x1="0%" y1="0%" x2="0%" y2="100%">
            <stop offset="0%" stop-color="#11182c" />
            <stop offset="60%" stop-color="#0c1220" />
            <stop offset="100%" stop-color="#04070d" />
          </linearGradient>
          <linearGradient id="hatGrad" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#1e293b" />
            <stop offset="100%" stop-color="#070b14" />
          </linearGradient>
          <linearGradient id="talismanGrad" x1="0%" y1="0%" x2="0%" y2="100%">
            <stop offset="0%" stop-color="#ffe57f" />
            <stop offset="50%" stop-color="#ffd166" />
            <stop offset="100%" stop-color="#f59e0b" />
          </linearGradient>
          <radialGradient id="ghostSkin" cx="50%" cy="40%" r="60%">
            <stop offset="0%" stop-color="#e2fbeb" />
            <stop offset="70%" stop-color="#b7e4c7" />
            <stop offset="100%" stop-color="#74c69d" />
          </radialGradient>
        </defs>

        <!-- Dynamic Ground Shadow -->
        <ellipse
          class="mascot-shadow transition-all duration-200"
          cx="120"
          cy="265"
          :rx="isHopping ? 28 : 55"
          :ry="isHopping ? 4 : 9"
          :fill="isHopping ? 'rgba(0,0,0,0.25)' : 'rgba(0,0,0,0.65)'"
        />

        <!-- Outstretched Hopping Arms -->
        <g class="animate-talisman-flutter origin-center">
          <!-- Left Arm -->
          <path
            d="M75 145 C40 145 25 155 18 160"
            stroke="#0c1220"
            stroke-width="16"
            stroke-linecap="round"
            fill="none"
          />
          <circle cx="16" cy="160" r="9" fill="#00f5a0" />
          <path d="M12 155 L8 153 M10 162 L5 163 M14 168 L10 171" stroke="#04070d" stroke-width="2" stroke-linecap="round" />

          <!-- Right Arm -->
          <path
            d="M165 145 C200 145 215 155 222 160"
            stroke="#0c1220"
            stroke-width="16"
            stroke-linecap="round"
            fill="none"
          />
          <circle cx="224" cy="160" r="9" fill="#00f5a0" />
          <path d="M228 155 L232 153 M230 162 L235 163 M226 168 L230 171" stroke="#04070d" stroke-width="2" stroke-linecap="round" />
        </g>

        <!-- Main Jiangshi Robe -->
        <g class="mascot-robe">
          <path
            d="M75 130 C75 130, 95 125, 120 125 C145 125, 165 130, 165 130 L182 245 C182 245, 120 252, 58 245 Z"
            fill="url(#robeGrad)"
            stroke="rgba(0, 245, 160, 0.4)"
            stroke-width="2"
          />
          <!-- Golden/Mint Robe Collar Trim -->
          <path d="M100 132 L120 162 L140 132" stroke="#00f5a0" stroke-width="2.5" fill="none" stroke-linecap="round" />
          <!-- Central Hexagon Core Rune -->
          <polygon points="120,172 132,179 132,193 120,200 108,193 108,179" fill="#070b14" stroke="#ffd166" stroke-width="1.5" />
          <text x="120" y="190" text-anchor="middle" font-size="9" font-family="monospace" font-weight="bold" fill="#00f5a0">{ }</text>
        </g>

        <!-- Hopping Feet -->
        <ellipse cx="96" cy="248" rx="14" ry="7" fill="#070b14" stroke="#00f5a0" stroke-width="1.5" />
        <ellipse cx="144" cy="248" rx="14" ry="7" fill="#070b14" stroke="#00f5a0" stroke-width="1.5" />

        <!-- Ghost Head & Headphone Band -->
        <circle cx="120" cy="95" r="48" fill="url(#ghostSkin)" stroke="#00f5a0" stroke-width="2" />
        <path d="M68 95 C68 58, 172 58, 172 95" stroke="#11182c" stroke-width="8" fill="none" stroke-linecap="round" />
        <!-- Headphone Ear Cups -->
        <rect x="62" y="80" width="12" height="30" rx="6" fill="#00f5a0" stroke="#070b14" stroke-width="1.5" />
        <rect x="166" y="80" width="12" height="30" rx="6" fill="#00f5a0" stroke="#070b14" stroke-width="1.5" />

        <!-- Jiangshi Mandarin Hat with Antenna -->
        <g class="mascot-hat">
          <path d="M74 68 C76 28, 164 28, 166 68 Z" fill="url(#hatGrad)" stroke="#ffd166" stroke-width="2" />
          <ellipse cx="120" cy="68" rx="54" ry="14" fill="#0c1220" stroke="#ffd166" stroke-width="2" />
          <!-- Hat Golden Gem -->
          <circle cx="120" cy="50" r="6" fill="#ff0054" stroke="#ffd166" stroke-width="1.5" />
          <!-- Cyber Antenna & Jade Tip -->
          <line x1="120" y1="44" x2="120" y2="24" stroke="#00f5a0" stroke-width="2" />
          <circle cx="120" cy="22" r="4" fill="#00f5a0" class="animate-pulse" />
        </g>

        <!-- Blushing Cheeks -->
        <ellipse cx="92" cy="116" rx="7" ry="4" fill="#ff0054" opacity="0.35" />
        <ellipse cx="148" cy="116" rx="7" ry="4" fill="#ff0054" opacity="0.35" />

        <!-- Dynamic Eyes Based on Mood -->
        <g class="mascot-eyes">
          <!-- Normal Eyes -->
          <template v-if="mood === 'normal'">
            <circle cx="102" cy="98" r="6" fill="#00f5a0" />
            <circle cx="104" cy="96" r="2" fill="#ffffff" />
            <circle cx="138" cy="98" r="6" fill="#00f5a0" />
            <circle cx="140" cy="96" r="2" fill="#ffffff" />
          </template>

          <!-- Caffeine Eyes (Glowing Yellow) -->
          <template v-else-if="mood === 'caffeine'">
            <circle cx="102" cy="98" r="7" fill="#ffd166" class="animate-ping" />
            <circle cx="102" cy="98" r="6.5" fill="#ffd166" />
            <circle cx="104" cy="96" r="2.5" fill="#ffffff" />
            <circle cx="138" cy="98" r="7" fill="#ffd166" class="animate-ping" />
            <circle cx="138" cy="98" r="6.5" fill="#ffd166" />
            <circle cx="140" cy="96" r="2.5" fill="#ffffff" />
          </template>

          <!-- Sleepy Eyes (Half-closed Violet Lines) -->
          <template v-else-if="mood === 'sleepy'">
            <path d="M96 98 Q103 104 110 98" stroke="#9d4edd" stroke-width="3.5" fill="none" stroke-linecap="round" />
            <path d="M130 98 Q137 104 144 98" stroke="#9d4edd" stroke-width="3.5" fill="none" stroke-linecap="round" />
          </template>

          <!-- Rage Eyes (Sharp Crimson Slits) -->
          <template v-else-if="mood === 'rage'">
            <polygon points="94,92 110,99 95,102" fill="#ff0054" />
            <polygon points="146,92 130,99 145,102" fill="#ff0054" />
          </template>
        </g>

        <!-- Cute Vampire Mouth with Fangs -->
        <path d="M112 120 Q120 127 128 120" stroke="#070b14" stroke-width="2.5" fill="none" stroke-linecap="round" />
        <polygon points="114,120 117,126 119,120" fill="#ffffff" />
        <polygon points="121,120 123,126 126,120" fill="#ffffff" />

        <!-- Forehead Yellow Paper Talisman with Tech Inscriptions -->
        <g class="mascot-talisman animate-talisman-flutter origin-top">
          <rect x="105" y="55" width="30" height="62" rx="3" fill="url(#talismanGrad)" stroke="#c9182b" stroke-width="1.2" />
          <!-- Red Header Seal -->
          <circle cx="120" cy="65" r="5" fill="#c9182b" />
          <text x="120" y="68" text-anchor="middle" font-size="6" font-family="monospace" font-weight="bold" fill="#ffd166">&lt;/&gt;</text>
          <!-- Dynamic Mood Inscription -->
          <text
            x="120"
            y="85"
            text-anchor="middle"
            font-size="8"
            font-family="monospace"
            font-weight="bold"
            fill="#c9182b"
            letter-spacing="0.5"
          >
            {{ mood === 'caffeine' ? 'COFFEE' : mood === 'sleepy' ? '4:00 AM' : mood === 'rage' ? 'DEPLOY' : '0 BUG' }}
          </text>
          <!-- Circuit Rune at Bottom -->
          <path d="M112 98 L120 102 L128 98 M120 102 L120 108" stroke="#c9182b" stroke-width="1.5" stroke-linecap="round" fill="none" />
        </g>
      </svg>
    </div>

    <!-- Interactive Mood Switcher & Hop Tracker (Optional Controls) -->
    <div v-if="showControls" class="mt-4 flex flex-col items-center gap-3">
      <!-- Hop Counter Display -->
      <div class="flex items-center gap-2 px-3 py-1 rounded-full bg-midnight-900/90 border border-white/10 text-xs font-mono text-slate-300">
        <span>Hops:</span>
        <span class="text-phantom-mint font-extrabold text-sm tabular-nums">{{ hopCount }}</span>
      </div>

      <!-- Mood Selection Pills -->
      <div class="flex items-center gap-1.5 p-1 rounded-xl bg-midnight-900/80 border border-white/10">
        <button
          v-for="m in (['normal', 'caffeine', 'sleepy', 'rage'] as const)"
          :key="m"
          type="button"
          class="px-2.5 py-1 rounded-lg text-xs font-mono capitalize transition-all min-h-[32px]"
          :class="mood === m
            ? 'bg-phantom-mint text-midnight-950 font-bold shadow-glow-mint'
            : 'text-slate-400 hover:text-slate-200 hover:bg-white/5'"
          @click="setMood(m)"
        >
          {{ m }}
        </button>
      </div>
    </div>
  </div>
</template>
