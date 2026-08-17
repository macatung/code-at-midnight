<script setup lang="ts">
import { onMounted, onUnmounted, watch } from 'vue';
import type { Project } from '@/types/portfolio';
import Icons from '@/Components/ui/Icons.vue';

const props = defineProps<{
  isOpen: boolean;
  project: Project | null;
}>();

const emit = defineEmits<{
  (e: 'close'): void;
}>();

const handleKeyDown = (e: KeyboardEvent) => {
  if (e.key === 'Escape' && props.isOpen) {
    emit('close');
  }
};

watch(() => props.isOpen, (open) => {
  if (typeof document !== 'undefined') {
    if (open) {
      document.body.classList.add('overflow-hidden');
    } else {
      document.body.classList.remove('overflow-hidden');
    }
  }
});

onMounted(() => {
  if (typeof window !== 'undefined') {
    window.addEventListener('keydown', handleKeyDown);
  }
});

onUnmounted(() => {
  if (typeof window !== 'undefined') {
    window.removeEventListener('keydown', handleKeyDown);
  }
  if (typeof document !== 'undefined') {
    document.body.classList.remove('overflow-hidden');
  }
});
</script>

<template>
  <Transition
    enter-active-class="transition duration-200 ease-out"
    enter-from-class="opacity-0"
    enter-to-class="opacity-100"
    leave-active-class="transition duration-150 ease-in"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <div
      v-if="isOpen && project"
      class="fixed inset-0 z-50 bg-black/80 backdrop-blur-md flex items-center justify-center p-4 sm:p-6 overflow-y-auto"
      role="dialog"
      aria-modal="true"
      @click.self="emit('close')"
    >
      <div
        class="relative w-full max-w-3xl rounded-3xl bg-midnight-900 border border-white/15 shadow-2xl overflow-hidden text-left my-8 max-h-[90vh] flex flex-col"
      >
        <!-- Modal Banner Header -->
        <div class="h-32 sm:h-40 w-full p-6 flex flex-col justify-between bg-gradient-to-br relative shrink-0" :class="project.coverGradient">
          <div class="flex items-center justify-between">
            <span class="px-3 py-1 rounded-full bg-black/60 backdrop-blur-md text-xs font-mono uppercase tracking-wider text-slate-200 border border-white/10">
              {{ project.category }}
            </span>
            <button
              type="button"
              class="w-10 h-10 rounded-full bg-black/60 hover:bg-rose-500/80 text-white flex items-center justify-center transition-colors border border-white/20 min-h-[40px] min-w-[40px] focus:outline-none focus:ring-2 focus:ring-phantom-mint"
              aria-label="Đóng cửa sổ"
              @click="emit('close')"
            >
              ✕
            </button>
          </div>
          <div>
            <h2 class="text-xl sm:text-3xl font-display font-extrabold text-white">{{ project.title }}</h2>
            <p class="text-xs sm:text-sm font-mono text-phantom-mint mt-1">{{ project.tagline }}</p>
          </div>
        </div>

        <!-- Modal Body Scrollable Container -->
        <div class="p-6 sm:p-8 space-y-6 overflow-y-auto scrollbar-thin flex-1">
          <!-- Description -->
          <p class="text-sm sm:text-base text-slate-300 leading-relaxed font-sans">{{ project.description }}</p>

          <!-- Key Metrics Grid -->
          <div>
            <h4 class="text-xs font-mono text-slate-400 uppercase tracking-wider mb-2.5">Chỉ Số Hiệu Năng (Key Metrics)</h4>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
              <div v-for="m in project.metrics" :key="m.label" class="p-3 rounded-xl bg-midnight-950 border border-white/5 text-center">
                <div class="text-xs font-mono text-slate-400 truncate">{{ m.label }}</div>
                <div class="text-base sm:text-lg font-display font-bold text-phantom-mint mt-1">{{ m.value }}</div>
              </div>
            </div>
          </div>

          <!-- Architecture Highlights -->
          <div>
            <h4 class="text-xs font-mono text-slate-400 uppercase tracking-wider mb-2.5">Kiến Trúc & Giải Pháp Kỹ Thuật</h4>
            <ul class="space-y-2 text-xs sm:text-sm text-slate-300">
              <li v-for="(highlight, i) in project.architectureHighlights" :key="i" class="flex items-start gap-2.5">
                <span class="text-phantom-mint mt-0.5">⚡</span>
                <span class="leading-relaxed">{{ highlight }}</span>
              </li>
            </ul>
          </div>

          <!-- Midnight Fact / Lore -->
          <div class="p-4 rounded-xl bg-amber-950/20 border border-talisman-gold/30 text-xs sm:text-sm text-amber-200/90 flex items-start gap-3">
            <span class="text-xl shrink-0">🌙</span>
            <div>
              <div class="font-mono font-bold text-talisman-gold text-xs uppercase mb-0.5">Midnight Lore</div>
              <p class="leading-relaxed">{{ project.midnightFact }}</p>
            </div>
          </div>

          <!-- Tech Stack -->
          <div>
            <h4 class="text-xs font-mono text-slate-400 uppercase tracking-wider mb-2.5">Tech Rune Stack</h4>
            <div class="flex flex-wrap gap-2">
              <span
                v-for="tech in project.techStack"
                :key="tech"
                class="px-3 py-1 rounded-lg text-xs font-mono bg-midnight-950 text-slate-200 border border-white/10 shadow-sm"
              >
                {{ tech }}
              </span>
            </div>
          </div>
        </div>

        <!-- Modal Footer Actions -->
        <div class="p-4 sm:p-6 bg-midnight-950/90 border-t border-white/10 flex flex-wrap items-center justify-between gap-4 shrink-0">
          <div class="flex items-center gap-3">
            <a
              v-if="project.liveUrl"
              :href="project.liveUrl"
              target="_blank"
              rel="noopener noreferrer"
              class="px-4 py-2.5 rounded-xl bg-phantom-mint text-midnight-950 font-display font-bold text-xs sm:text-sm hover:brightness-110 transition-all flex items-center gap-2 min-h-[44px]"
            >
              <span>Xem Trực Tiếp Demo</span>
              <span>↗</span>
            </a>
            <a
              v-if="project.githubUrl"
              :href="project.githubUrl"
              target="_blank"
              rel="noopener noreferrer"
              class="px-4 py-2.5 rounded-xl bg-midnight-800 border border-white/10 hover:border-white/30 text-white font-mono text-xs sm:text-sm transition-all flex items-center gap-2 min-h-[44px]"
            >
              <Icons name="Github" :size="16" />
              <span>Mã Nguồn GitHub</span>
            </a>
          </div>

          <button
            type="button"
            class="px-4 py-2 rounded-xl text-slate-400 hover:text-white font-mono text-xs min-h-[44px] focus:outline-none"
            @click="emit('close')"
          >
            Đóng [ESC]
          </button>
        </div>
      </div>
    </div>
  </Transition>
</template>
