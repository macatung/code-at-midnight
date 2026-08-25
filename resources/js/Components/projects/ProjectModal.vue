<script setup lang="ts">
import { onMounted, onUnmounted, watch } from 'vue';
import type { Project } from '@/types/portfolio';
import { sound } from '@/audio/soundEffects';
import { useI18n } from '@/composables/useI18n';

const props = defineProps<{
  isOpen: boolean;
  project: Project | null;
}>();

const emit = defineEmits<{
  (e: 'close'): void;
}>();
const { t } = useI18n();

const handleKeyDown = (e: KeyboardEvent) => {
  if (e.key === 'Escape' && props.isOpen) {
    emit('close');
  }
};

const handleSummonClick = () => {
  sound.playTalisman();
  emit('close');
  if (typeof document !== 'undefined') {
    const contactEl = document.getElementById('contact');
    if (contactEl) {
      contactEl.scrollIntoView({ behavior: 'smooth' });
    }
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
      class="fixed inset-0 z-50 bg-black/85 backdrop-blur-md flex items-center justify-center p-4 sm:p-6 overflow-y-auto"
      role="dialog"
      aria-modal="true"
      @click.self="emit('close')"
    >
      <div
        class="relative w-full max-w-3xl rounded-3xl bg-midnight-900 border border-white/15 shadow-2xl overflow-hidden text-left my-8 max-h-[90vh] flex flex-col"
      >
        <!-- Modal Header with Category & Close -->
        <div class="p-6 sm:p-8 bg-gradient-to-b from-midnight-800 to-midnight-900 border-b border-white/10 relative">
          <button
            type="button"
            class="absolute top-6 right-6 w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white flex items-center justify-center transition-colors focus:outline-none focus:ring-2 focus:ring-phantom-mint"
            :aria-label="t('projectModal.closeAria')"
            @click="emit('close')"
          >
            ✕
          </button>

          <div class="flex flex-wrap items-center gap-2 mb-3">
            <span class="px-3 py-1 rounded-full text-xs font-mono bg-phantom-mint/10 text-phantom-mint border border-phantom-mint/30 uppercase tracking-wider font-bold">
              {{ project.category }}
            </span>
            <span v-if="project.featured" class="px-3 py-1 rounded-full text-xs font-mono bg-talisman-gold/10 text-talisman-gold border border-talisman-gold/30 flex items-center gap-1 font-bold">
              <span>★</span>
              <span>{{ t('projectModal.featured') }}</span>
            </span>
            <span class="px-3 py-1 rounded-full text-xs font-mono bg-rose-500/10 text-rose-300 border border-rose-500/30 flex items-center gap-1 font-bold">
              <span>🔒</span>
              <span>{{ t('projectModal.nda') }}</span>
            </span>
          </div>

          <h3 class="text-2xl sm:text-3xl font-display font-extrabold text-white tracking-tight">
            {{ project.title }}
          </h3>
          <p class="text-sm font-mono text-phantom-mint mt-1.5">{{ project.tagline }}</p>
        </div>

        <!-- Modal Body Content -->
        <div class="p-6 sm:p-8 overflow-y-auto flex-1 space-y-6">
          <!-- Overview Description -->
          <div>
            <h4 class="text-xs font-mono text-slate-400 uppercase tracking-wider mb-2">{{ t('projectModal.overview') }}</h4>
            <p class="text-sm sm:text-base text-slate-300 leading-relaxed font-sans">
              {{ project.longDescription || project.description }}
            </p>
          </div>

          <!-- Impact Metrics Grid -->
          <div v-if="project.metrics && project.metrics.length > 0">
            <h4 class="text-xs font-mono text-slate-400 uppercase tracking-wider mb-3">{{ t('projectModal.metrics') }}</h4>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
              <div
                v-for="metric in project.metrics"
                :key="metric.label"
                class="p-3.5 rounded-2xl bg-midnight-950/90 border border-white/5 flex flex-col justify-between"
              >
                <span class="text-xs font-mono text-slate-400">{{ metric.label }}</span>
                <span class="text-base sm:text-lg font-display font-bold text-phantom-mint mt-1">
                  {{ metric.value }}
                </span>
              </div>
            </div>
          </div>

          <!-- Architecture & Highlights -->
          <div v-if="project.highlights && project.highlights.length > 0">
            <h4 class="text-xs font-mono text-slate-400 uppercase tracking-wider mb-3">{{ t('projectModal.highlights') }}</h4>
            <ul class="space-y-2.5">
              <li
                v-for="(highlight, i) in project.highlights"
                :key="i"
                class="flex items-start gap-3 text-xs sm:text-sm text-slate-300 font-sans"
              >
                <span class="text-phantom-mint mt-0.5 shrink-0 text-sm">⚡</span>
                <span class="leading-relaxed">{{ highlight }}</span>
              </li>
            </ul>
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

          <!-- Enterprise NDA Notice Card -->
          <div class="p-4 rounded-2xl bg-midnight-950/90 border border-white/10 flex items-start gap-3.5">
            <span class="text-xl shrink-0 text-amber-400">🛡️</span>
            <div class="text-xs font-sans text-slate-300 leading-relaxed">
              <div class="font-mono font-bold text-amber-300 uppercase tracking-wider mb-1">
                {{ t('projectModal.security') }}
              </div>
              <p>
                {{ t('projectModal.securityText') }}
              </p>
            </div>
          </div>
        </div>

        <!-- Modal Footer Actions -->
        <div class="p-4 sm:p-6 bg-midnight-950/90 border-t border-white/10 flex flex-wrap items-center justify-between gap-4 shrink-0">
          <div class="flex flex-wrap items-center gap-3">
            <button
              type="button"
              class="px-5 py-2.5 rounded-xl bg-phantom-mint text-midnight-950 font-display font-bold text-xs sm:text-sm hover:brightness-110 active:scale-95 transition-all flex items-center gap-2 min-h-[44px] shadow-glow-mint cursor-pointer"
              @click="handleSummonClick"
            >
              <span>{{ t('projectModal.summon') }}</span>
              <span>📜</span>
            </button>
          </div>

          <button
            type="button"
            class="px-4 py-2 rounded-xl text-slate-400 hover:text-white font-mono text-xs min-h-[44px] focus:outline-none cursor-pointer"
            @click="emit('close')"
          >
            {{ t('projectModal.close') }}
          </button>
        </div>
      </div>
    </div>
  </Transition>
</template>
