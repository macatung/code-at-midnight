<script setup lang="ts">
import { computed } from 'vue';
import { experienceData } from '@/data/experienceData';
import Icons from '@/Components/ui/Icons.vue';

interface ExperienceRecord {
  id: number;
  role: string;
  company: string;
  period: string;
  type: string;
  location: string;
  summary: string;
  achievements?: string[];
  technologies?: string[];
}

interface Props {
  experiences?: ExperienceRecord[];
}

const props = withDefaults(defineProps<Props>(), {
  experiences: () => [],
});

const displayExperiences = computed(() => {
  if (props.experiences && props.experiences.length > 0) {
    return props.experiences;
  }
  return experienceData;
});
</script>

<template>
  <section id="experience" class="scroll-mt-24 w-full py-16 sm:py-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto text-left">
    <!-- Header -->
    <div class="flex flex-col items-start mb-12">
      <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/5 border border-white/10 text-phantom-mint text-xs font-mono mb-3 whitespace-nowrap select-none">
        📜 The Career Chronicles
      </span>
      <h2 class="text-3xl sm:text-4xl lg:text-5xl font-display font-extrabold text-white tracking-tight">
        Kinh Nghiệm & <span class="text-phantom-mint">Biên Niên Sử</span>
      </h2>
      <p class="text-sm sm:text-base text-slate-400 mt-2 max-w-2xl font-sans">
        Dấu chân qua các dự án thực chiến tải cao, hành trình phát triển từ Indie Hacker sang Lead Systems Architect.
      </p>
    </div>

    <!-- Timeline Container -->
    <div class="relative pl-6 sm:pl-8 space-y-8">
      <!-- Continuous Vertical Connector Line -->
      <div class="absolute top-2 bottom-2 left-3 w-[1px] bg-white/10" />

      <!-- Timeline Items -->
      <div
        v-for="item in displayExperiences"
        :key="item.id"
        class="relative flex flex-col items-start"
      >
        <!-- Node Dot Indicator -->
        <div class="absolute -left-[27px] sm:-left-[31px] top-1.5 w-5 h-5 rounded-full bg-midnight-950 border-2 border-phantom-mint flex items-center justify-center text-phantom-mint shadow-glow-mint z-10 select-none">
          <span class="w-1.5 h-1.5 rounded-full bg-phantom-mint" />
        </div>

        <!-- Experience Card (Clean Minimalist) -->
        <div class="w-full glass-panel p-6 sm:p-7 rounded-2xl border border-white/[0.07] hover:border-white/20 transition-all text-left">
          <!-- Period & Type Badges -->
          <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
            <div class="flex items-center gap-2">
              <span class="px-2.5 py-0.5 rounded-full bg-phantom-mint/10 text-phantom-mint font-mono text-xs font-bold border border-phantom-mint/20 whitespace-nowrap">
                {{ item.period }}
              </span>
              <span class="px-2 py-0.5 rounded-full bg-white/5 text-slate-300 font-mono text-[11px] border border-white/5 whitespace-nowrap">
                {{ item.type }}
              </span>
            </div>
            <span class="text-xs font-mono text-slate-400 whitespace-nowrap">
              📍 {{ item.location }}
            </span>
          </div>

          <!-- Role & Company -->
          <h3 class="text-lg sm:text-xl font-display font-bold text-white">
            {{ item.role }}
          </h3>
          <div class="text-xs font-mono text-phantom-mint mt-0.5">
            {{ item.company }}
          </div>

          <!-- Summary -->
          <p class="text-xs sm:text-sm text-slate-300 mt-2.5 leading-relaxed font-sans">
            {{ item.summary }}
          </p>

          <!-- Key Achievements -->
          <div class="mt-3.5">
            <ul class="space-y-1.5 text-xs sm:text-sm text-slate-300 font-sans">
              <li v-for="(ach, i) in item.achievements" :key="i" class="flex items-start gap-2">
                <span class="text-phantom-mint mt-0.5 select-none shrink-0 text-xs">✦</span>
                <span class="leading-relaxed">{{ ach }}</span>
              </li>
            </ul>
          </div>

          <!-- Tech Stack Badges -->
          <div class="flex flex-wrap gap-1.5 mt-4 pt-3 border-t border-white/5">
            <span
              v-for="tech in item.technologies"
              :key="tech"
              class="px-2 py-0.5 rounded text-[11px] font-mono bg-white/5 text-slate-300 border border-white/5 whitespace-nowrap"
            >
              {{ tech }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
