<script setup lang="ts">
import { ref, computed } from 'vue';
import { projectsData } from '@/data/projectsData';
import type { Project } from '@/types/portfolio';
import ProjectModal from './ProjectModal.vue';
import { sound } from '@/audio/soundEffects';
import Icons from '@/Components/ui/Icons.vue';

interface Props {
  projects?: Project[];
}

const props = withDefaults(defineProps<Props>(), {
  projects: () => [],
});

type CategoryFilter = 'all' | 'fullstack' | 'creative' | 'ai-web3' | 'tools';

const activeCategory = ref<CategoryFilter>('all');
const selectedProject = ref<Project | null>(null);
const isModalOpen = ref(false);

const categories = [
  { id: 'all', label: 'Tất Cả Dự Án' },
  { id: 'fullstack', label: 'Full-Stack' },
  { id: 'creative', label: 'Creative UI & Audio' },
  { id: 'ai-web3', label: 'AI & Microservices' },
  { id: 'tools', label: 'Developer Tools' }
] as const;

const allProjects = computed(() => {
  return props.projects && props.projects.length > 0 ? props.projects : projectsData;
});

const filteredProjects = computed(() => {
  if (activeCategory.value === 'all') return allProjects.value;
  return allProjects.value.filter((p) => p.category === activeCategory.value);
});

const setCategory = (cat: CategoryFilter) => {
  activeCategory.value = cat;
  sound.playClick();
};

const openProject = (project: Project) => {
  selectedProject.value = project;
  isModalOpen.value = true;
  sound.playClick();
};

const closeModal = () => {
  isModalOpen.value = false;
  selectedProject.value = null;
  sound.playClick();
};
</script>

<template>
  <section id="projects" class="scroll-mt-24 w-full py-16 sm:py-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto text-left">
    <!-- Header -->
    <div class="flex flex-col items-start mb-10">
      <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/5 border border-white/10 text-phantom-mint text-xs font-mono mb-3 whitespace-nowrap select-none">
        📜 The Grimoire Portfolio
      </span>
      <h2 class="text-3xl sm:text-4xl lg:text-5xl font-display font-extrabold text-white tracking-tight">
        Tác Phẩm & <span class="text-phantom-mint">Dự Án Thực Chiến</span>
      </h2>
      <p class="text-sm sm:text-base text-slate-400 mt-2 max-w-2xl font-sans">
        Những hệ thống phân tán, web app tải cao và công cụ sáng tạo được kiến tạo trong những phiên lập trình tĩnh lặng.
      </p>
    </div>

    <!-- Category Filter Tabs -->
    <div class="flex items-center gap-2 mb-8 overflow-x-auto no-scrollbar pb-2">
      <button
        v-for="cat in categories"
        :key="cat.id"
        type="button"
        class="px-3.5 py-2 rounded-xl text-xs sm:text-sm font-mono font-medium transition-all min-h-[40px] shrink-0 border select-none whitespace-nowrap"
        :class="activeCategory === cat.id
          ? 'bg-phantom-mint text-midnight-950 border-phantom-mint font-bold shadow-glow-mint'
          : 'bg-midnight-900/80 text-slate-400 border-white/5 hover:border-white/20 hover:text-white'"
        @click="setCategory(cat.id as CategoryFilter)"
      >
        {{ cat.label }}
      </button>
    </div>

    <!-- Project Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
        v-for="project in filteredProjects"
        :key="project.id"
        class="rounded-2xl border border-white/[0.07] glass-panel overflow-hidden flex flex-col justify-between transition-all duration-300 hover:-translate-y-1 hover:border-phantom-mint/40 group"
      >
        <!-- Card Cover Banner (Minimalist Deep Dark Header) -->
        <div class="p-5 pb-3 border-b border-white/5 flex flex-col justify-between bg-midnight-900/60">
          <div class="flex items-center justify-between mb-3">
            <span class="px-2.5 py-0.5 rounded-full bg-white/5 text-[10px] font-mono uppercase tracking-wider text-slate-300 border border-white/10 whitespace-nowrap">
              {{ project.category }}
            </span>
            <span v-if="project.featured" class="text-[11px] font-mono text-talisman-gold bg-amber-500/10 px-2 py-0.5 rounded-full border border-talisman-gold/30 whitespace-nowrap">
              ★ Featured
            </span>
          </div>
          <h3 class="font-display font-bold text-lg text-white group-hover:text-phantom-mint transition-colors">
            {{ project.title }}
          </h3>
          <p class="text-xs font-mono text-phantom-mint mt-1 truncate">{{ project.tagline }}</p>
        </div>

        <!-- Card Content -->
        <div class="p-5 flex-1 flex flex-col justify-between text-left">
          <div>
            <p class="text-xs text-slate-400 line-clamp-2 mb-4 leading-relaxed font-sans">{{ project.description }}</p>

            <!-- Metrics Preview -->
            <div class="grid grid-cols-3 gap-2 p-2.5 rounded-xl bg-midnight-950/80 border border-white/5 mb-4">
              <div v-for="m in project.metrics" :key="m.label" class="text-center">
                <div class="text-[10px] font-mono text-slate-500 truncate">{{ m.label }}</div>
                <div class="text-xs font-display font-bold text-slate-200 mt-0.5 whitespace-nowrap">{{ m.value }}</div>
              </div>
            </div>

            <!-- Tech Stack Badges -->
            <div class="flex flex-wrap gap-1.5 mb-5">
              <span
                v-for="tech in project.tags.slice(0, 4)"
                :key="tech"
                class="px-2 py-0.5 rounded text-[10px] font-mono bg-white/5 text-slate-300 border border-white/5 whitespace-nowrap"
              >
                {{ tech }}
              </span>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex items-center gap-2 pt-2 border-t border-white/5">
            <button
              type="button"
              class="flex-1 py-2.5 px-3 rounded-xl bg-white/5 hover:bg-phantom-mint text-slate-200 hover:text-midnight-950 font-display font-bold text-xs transition-all min-h-[40px] flex items-center justify-center gap-1 whitespace-nowrap border border-white/10 hover:border-phantom-mint"
              @click="openProject(project)"
            >
              <span>Xem Chi Tiết</span>
              <span>→</span>
            </button>
            <a
              v-if="project.githubUrl"
              :href="project.githubUrl"
              target="_blank"
              rel="noopener noreferrer"
              class="p-2.5 rounded-xl bg-midnight-900 border border-white/10 hover:border-white/30 text-slate-300 hover:text-white transition-all min-h-[40px] min-w-[40px] flex items-center justify-center text-xs font-mono"
              title="Mã nguồn GitHub"
            >
              <Icons name="Github" :size="16" />
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- Project Modal Dialog -->
    <ProjectModal
      :is-open="isModalOpen"
      :project="selectedProject"
      @close="closeModal"
    />
  </section>
</template>
