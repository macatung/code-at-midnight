<script setup lang="ts">
import { ref, computed } from 'vue';
import { skillsData } from '@/data/skillsData';
import Icons from '@/Components/ui/Icons.vue';
import { sound } from '@/audio/soundEffects';

interface SkillRecord {
  id: number;
  name: string;
  category: string;
  level: number;
  rune: string;
  tag: string;
  order?: number;
}

interface Props {
  skills?: SkillRecord[];
}

const props = withDefaults(defineProps<Props>(), {
  skills: () => [],
});

const hoveredSkill = ref<string | null>(null);

const onSkillHover = (skillName: string) => {
  if (hoveredSkill.value !== skillName) {
    hoveredSkill.value = skillName;
    sound.playClick();
  }
};

const displayCategories = computed(() => {
  if (!props.skills || props.skills.length === 0) {
    return skillsData;
  }

  const categoryMap: Record<string, { title: string; subtitle: string; iconName: string; badge: string; skills: any[] }> = {
    frontend: {
      title: 'Frontend & Creative Sorcery',
      subtitle: 'Giao diện mượt mà & âm thanh tương tác',
      iconName: 'Layout',
      badge: 'Core UI',
      skills: [],
    },
    backend: {
      title: 'Backend & High-Load Architecture',
      subtitle: 'Kiến trúc phân tán, cache & queue xử lý tải cao',
      iconName: 'Database',
      badge: 'Architecture',
      skills: [],
    },
    cloud: {
      title: 'Cloud, Infra & DevOps',
      subtitle: 'Triển khai tự động hóa & hạ tầng bền bỉ',
      iconName: 'Terminal',
      badge: 'Infra & CI/CD',
      skills: [],
    },
    ai: {
      title: 'AI, Automation & Microservices',
      subtitle: 'Tích hợp mô hình ngôn ngữ lớn & bot tự động',
      iconName: 'Sparkles',
      badge: 'Automation',
      skills: [],
    },
  };

  props.skills.forEach((s) => {
    const cat = categoryMap[s.category] || categoryMap.frontend;
    cat.skills.push({
      name: s.name,
      rune: s.rune,
      level: s.level,
      categoryTag: s.tag,
      description: `${s.level}% Mastery`,
    });
  });

  return Object.values(categoryMap).filter((c) => c.skills.length > 0);
});
</script>

<template>
  <section id="skills" class="scroll-mt-24 w-full py-16 sm:py-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto text-left">
    <!-- Header -->
    <div class="flex flex-col items-start mb-10">
      <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/5 border border-white/10 text-phantom-mint text-xs font-mono mb-3 select-none">
        ⚡ Technical Arsenal
      </span>
      <h2 class="text-3xl sm:text-4xl lg:text-5xl font-display font-extrabold text-white tracking-tight">
        Kỹ Năng & <span class="text-phantom-mint">Công Nghệ</span>
      </h2>
      <p class="text-sm sm:text-base text-slate-400 mt-2 max-w-2xl font-sans">
        18 kỹ năng cốt lõi được tôi luyện qua các hệ thống phân tán, web app tải cao và giao diện sáng tạo.
      </p>
    </div>

    <!-- 4 Categories Grid (Clean & Scannable) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-10">
      <div
        v-for="category in displayCategories"
        :key="category.title"
        class="p-6 sm:p-7 rounded-2xl glass-panel border border-white/[0.07] flex flex-col justify-between hover:border-white/20 transition-colors"
      >
        <!-- Category Header -->
        <div class="flex items-center justify-between pb-3.5 border-b border-white/5 mb-5">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-midnight-900 border border-white/10 flex items-center justify-center text-phantom-mint">
              <Icons :name="category.iconName" :size="18" />
            </div>
            <h3 class="font-display font-bold text-base sm:text-lg text-white">{{ category.title }}</h3>
          </div>
          <span class="text-[11px] font-mono px-2.5 py-0.5 rounded-full bg-white/5 text-slate-400 border border-white/5">
            {{ category.badge }}
          </span>
        </div>

        <!-- Skills List (Compact & Clean without verbose paragraph walls) -->
        <div class="space-y-3">
          <div
            v-for="skill in category.skills"
            :key="skill.name"
            class="group/skill p-2.5 rounded-xl hover:bg-white/[0.04] transition-colors cursor-default"
            @mouseenter="onSkillHover(skill.name)"
          >
            <div class="flex items-center justify-between mb-1.5 min-w-0">
              <div class="flex items-center gap-2 min-w-0">
                <span class="text-base select-none shrink-0">{{ skill.rune }}</span>
                <span class="text-xs sm:text-sm font-semibold text-slate-200 group-hover/skill:text-phantom-mint transition-colors truncate">
                  {{ skill.name }}
                </span>
                <span class="text-[10px] font-mono px-1.5 py-0.2 rounded bg-white/5 text-slate-400 shrink-0">
                  {{ skill.tag }}
                </span>
              </div>
              <span class="text-xs font-mono font-bold text-phantom-mint ml-2 shrink-0 tabular-nums">
                {{ skill.level }}%
              </span>
            </div>

            <!-- Proficiency Bar -->
            <div class="w-full h-1.5 bg-midnight-900 rounded-full overflow-hidden border border-white/5">
              <div
                class="proficiency-bar-fill h-full rounded-full bg-phantom-mint transition-all duration-700 ease-out"
                :style="{ width: `${skill.level}%` }"
              />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Quality Guarantee Badge -->
    <div class="p-4 sm:p-5 rounded-2xl glass-panel border border-white/10 flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left">
      <div class="flex items-center gap-3">
        <span class="text-2xl select-none">🛡️</span>
        <div>
          <h4 class="text-white font-display font-bold text-sm sm:text-base">100% Tested & Type-Safe</h4>
          <p class="text-slate-400 text-xs mt-0.5 font-sans">
            Mọi hệ thống đều được kiểm thử tự động, cấu trúc chuẩn SOLID và tối ưu hiệu năng cao.
          </p>
        </div>
      </div>
      <div class="flex items-center gap-2 shrink-0">
        <span class="px-3 py-1 rounded-xl bg-white/5 border border-white/10 text-xs font-mono text-phantom-mint font-semibold">
          ✓ Zero Runtime Crash
        </span>
      </div>
    </div>
  </section>
</template>
