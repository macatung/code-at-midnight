<script setup lang="ts">
defineProps<{
  isHovered?: boolean;
}>();
</script>

<template>
  <div class="relative flex flex-col items-center justify-center select-none cursor-pointer group">
    <!-- Clean Zen Mascot on Lotus without big blur aura (Nhỏ gọn, tĩnh tâm đứng yên) -->
    <div class="relative z-10 flex flex-col items-center">
      <svg
        width="130"
        height="145"
        viewBox="0 0 160 170"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
        class="filter drop-shadow-[0_8px_16px_rgba(0,0,0,0.5)] transition-transform duration-200 group-hover:scale-105"
      >
        <defs>
          <linearGradient id="lotusLotus" x1="0%" y1="0%" x2="0%" y2="100%">
            <stop offset="0%" stop-color="#fb7185" />
            <stop offset="50%" stop-color="#f43f5e" />
            <stop offset="100%" stop-color="#9f1239" />
          </linearGradient>

          <linearGradient id="zenRobe" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#fbbf24" />
            <stop offset="50%" stop-color="#d97706" />
            <stop offset="100%" stop-color="#78350f" />
          </linearGradient>

          <linearGradient id="zenSkin" x1="0%" y1="0%" x2="0%" y2="100%">
            <stop offset="0%" stop-color="#fffbeb" />
            <stop offset="70%" stop-color="#fef3c7" />
            <stop offset="100%" stop-color="#fde68a" />
          </linearGradient>
        </defs>

        <!-- Subtle Halo Line Only (No heavy blur) -->
        <circle cx="80" cy="65" r="42" stroke="#f59e0b" stroke-width="1.2" stroke-dasharray="3 3" opacity="0.6" />

        <!-- Blooming Lotus Throne -->
        <g transform="translate(20, 16)">
          <path d="M26 88 C14 80 18 68 32 72 C42 75 46 86 26 88 Z" fill="url(#lotusLotus)" stroke="#f59e0b" stroke-width="1.2" />
          <path d="M94 88 C106 80 102 68 88 72 C78 75 74 86 94 88 Z" fill="url(#lotusLotus)" stroke="#f59e0b" stroke-width="1.2" />
          
          <ellipse cx="60" cy="90" rx="32" ry="11" fill="#f59e0b" stroke="#fef08a" stroke-width="1.5" />
          
          <path d="M38 98 C28 86 34 72 48 76 C56 79 56 94 38 98 Z" fill="url(#lotusLotus)" stroke="#fef08a" stroke-width="1.5" />
          <path d="M82 98 C92 86 86 72 72 76 C64 79 64 94 82 98 Z" fill="url(#lotusLotus)" stroke="#fef08a" stroke-width="1.5" />
          <path d="M60 74 C50 86 52 102 60 105 C68 102 70 86 60 74 Z" fill="#f43f5e" stroke="#fef08a" stroke-width="1.5" />
          <line x1="60" y1="76" x2="60" y2="100" stroke="#fef08a" stroke-width="1.2" stroke-linecap="round" />
        </g>

        <!-- Meditating Body in Kasaya Robe -->
        <g transform="translate(20, 16)">
          <path
            d="M38 68 C36 56 46 52 60 52 C74 52 84 56 82 68 C84 78 88 88 84 92 C76 96 44 96 36 92 C32 88 36 78 38 68 Z"
            fill="url(#zenRobe)"
            stroke="#fef08a"
            stroke-width="1.5"
          />
          <path d="M48 54 C58 62 68 72 70 88" stroke="#fef08a" stroke-width="1.8" fill="none" stroke-linecap="round" />

          <!-- Meditating Hands -->
          <ellipse cx="60" cy="80" rx="10" ry="4.5" fill="#fef3c7" stroke="#fef08a" stroke-width="1.2" />
          <circle cx="60" cy="78" r="1.8" fill="#fbbf24" />

          <!-- Head -->
          <ellipse cx="60" cy="44" rx="19" ry="17" fill="url(#zenSkin)" stroke="#f59e0b" stroke-width="1.8" />
          <circle cx="60" cy="34" r="2" fill="#d97706" />

          <!-- Cheeks -->
          <ellipse cx="48" cy="46" rx="3.5" ry="2" fill="#fb7185" opacity="0.6" />
          <ellipse cx="72" cy="46" rx="3.5" ry="2" fill="#fb7185" opacity="0.6" />

          <!-- Meditating Peaceful Eyes -->
          <path d="M49 43 Q54 48 59 43" stroke="#78350f" stroke-width="2.2" stroke-linecap="round" fill="none" />
          <path d="M61 43 Q66 48 71 43" stroke="#78350f" stroke-width="2.2" stroke-linecap="round" fill="none" />
          
          <path d="M57 52 Q60 55 63 52" stroke="#92400e" stroke-width="1.5" stroke-linecap="round" fill="none" />
        </g>
      </svg>

      <!-- Compact Zen Badge -->
      <div class="mt-0.5 px-2.5 py-0.5 rounded-full bg-stone-950/95 border border-amber-400 text-[10px] font-serif text-amber-300 font-bold shadow-md flex items-center gap-1 backdrop-blur-md">
        <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
        <span>🧘 Ma Tọa Thiền</span>
      </div>
    </div>
  </div>
</template>