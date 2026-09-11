<script setup lang="ts">
import { ref, computed } from 'vue';
import { useI18n } from '@/composables/useI18n';
import Icons from '@/Components/ui/Icons.vue';
import { mindfulBell } from '@/audio/mindfulBellAudio';

const { locale } = useI18n();
const isVi = computed(() => locale.value === 'vi');

const isRinging = ref(false);
const ringMessage = ref('');

const ringMindfulChime = () => {
  if (isRinging.value) return;
  isRinging.value = true;
  ringMessage.value = isVi.value ? 'Đang thỉnh chuông chánh niệm 432Hz...' : 'Sounding 432Hz mindful chime...';
  mindfulBell.ringBell(432, 5.5);

  setTimeout(() => {
    isRinging.value = false;
    ringMessage.value = '';
  }, 5000);
};

const pillarFeatures = computed(() => [
  {
    icon: 'BookOpen',
    title: isVi.value ? 'Tam Tạng Tipiṭaka & 50+ Kinh Văn' : 'Tipiṭaka Scriptures & 50+ Suttas',
    desc: isVi.value ? 'Đối chiếu Pāḷi - Việt, font stepper, chế độ đọc thiền tĩnh tại.' : 'Pāḷi-Vietnamese dual view, font stepper, contemplative reading mode.',
    link: '/theravada',
  },
  {
    icon: 'Sparkles',
    title: isVi.value ? 'Khóa Học Pāḷi Từng Bước (10 Bài)' : 'Pāḷi Course (10 Lessons)',
    desc: isVi.value ? 'Ngữ âm, 8 biến cách danh từ, động từ và trắc nghiệm cuối bài.' : 'Phonetics, 8 noun declensions, verbs, and interactive quizzes.',
    link: '/theravada/hoc-pali',
  },
  {
    icon: 'Play',
    title: isVi.value ? 'All-in-One Studio Video CMS' : 'All-in-One Video Studio CMS',
    desc: isVi.value ? 'Dual player YouTube/MP4, Media Canvas 2 cột công thái học, 1-Click Copy kits.' : 'Ergonomic 2-column studio, dual YouTube/MP4 player, 1-click publishing.',
    link: '/admin/theravada/videos',
  },
  {
    icon: 'Heart',
    title: isVi.value ? 'Bộ Ứng Dụng Thiền Quán' : 'Mindfulness Interactive Apps',
    desc: isVi.value ? 'Quán chiếu Vô Ngã (Anattā), thẻ Pháp Cú, đồng hồ chuông 432Hz.' : 'Anattā engine, Dhammapada card generator, 432Hz meditation timer.',
    link: '/theravada/ung-dung-tu-hoc',
  },
]);
</script>

<template>
  <article
    class="relative group rounded-3xl border border-amber-500/20 bg-gradient-to-b from-[#14100b] via-midnight-900 to-midnight-950 p-6 sm:p-8 overflow-hidden transition-all duration-300 hover:border-amber-500/40 hover:shadow-2xl hover:shadow-amber-500/10 flex flex-col justify-between"
    aria-label="Pillar 1: Theravada Buddhist Digital Platform"
  >
    <!-- Ambient Background Glow -->
    <div class="absolute -right-20 -top-20 w-72 h-72 bg-amber-500/10 rounded-full blur-3xl pointer-events-none group-hover:bg-amber-500/15 transition-all duration-500"></div>

    <div>
      <!-- Header Badges -->
      <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div class="flex items-center gap-2">
          <span class="px-3 py-1 rounded-full text-xs font-mono font-bold uppercase tracking-wider bg-amber-500/15 text-amber-400 border border-amber-500/30">
            PILLAR 01
          </span>
          <span class="px-2.5 py-1 rounded-full text-xs font-mono bg-stone-800 text-stone-300 border border-white/10 flex items-center gap-1.5">
            <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
            theravada.macatung.dev
          </span>
        </div>

        <button
          type="button"
          @click="ringMindfulChime"
          class="relative inline-flex items-center gap-2 px-3 py-1.5 rounded-xl border text-xs font-medium transition-all duration-200"
          :class="isRinging ? 'border-amber-400 bg-amber-500/20 text-amber-200' : 'border-amber-500/30 bg-amber-950/40 text-amber-300 hover:bg-amber-500/20 hover:border-amber-400'"
          :title="isVi ? 'Bấm để thỉnh chuông thiền quán 432Hz' : 'Click to ring 432Hz mindfulness bell'"
        >
          <span v-if="isRinging" class="absolute inset-0 rounded-xl bg-amber-400/20 animate-ping"></span>
          <Icons name="Volume2" size="14" :class="{ 'animate-bounce text-amber-300': isRinging }" />
          <span>{{ isRinging ? (isVi ? 'Đang Ngân 432Hz...' : 'Chiming 432Hz...') : (isVi ? '🔔 Thỉnh Chuông 432Hz' : '🔔 Mindful Bell') }}</span>
        </button>
      </div>

      <!-- Title & Description -->
      <div class="mb-6">
        <div class="text-xs font-mono uppercase tracking-widest text-amber-400/90 mb-1">
          {{ isVi ? 'PHẬT GIÁO NGUYÊN THỦY & PHÁP HÀNH VIPASSANĀ' : 'THERAVĀDA DHAMMA & VIPASSANĀ PRACTICE' }}
        </div>
        <h3 class="text-2xl sm:text-3xl font-display font-black tracking-tight text-white group-hover:text-amber-300 transition-colors">
          {{ isVi ? 'Ma Tọa Thiền — Nền Tảng Kỹ Thuật Số Theravāda' : 'Theravāda Buddhist Digital Platform' }}
        </h3>
        <p class="mt-3 text-sm sm:text-base text-slate-300 leading-relaxed">
          {{ isVi
            ? 'Hệ thống học tập và bảo tồn kinh điển Pāḷi Tipiṭaka toàn diện: 50+ kinh văn chuẩn xác, giáo trình 10 bài học Pāḷi sư phạm từ con số 0, All-in-One Studio Video CMS biên tập đa kênh và chuông chánh niệm 432Hz.'
            : 'A comprehensive digital repository preserving the Pāḷi Tipiṭaka: 50+ canonical suttas, a 10-lesson pedagogical Pāḷi curriculum, ergonomic All-in-One Video Studio CMS, and 432Hz mindfulness soundscapes.'
          }}
        </p>
      </div>

      <!-- Key Submodules Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-6">
        <a
          v-for="feature in pillarFeatures"
          :key="feature.title"
          :href="feature.link"
          class="p-3.5 rounded-2xl border border-white/10 bg-black/30 hover:border-amber-500/40 hover:bg-amber-950/20 transition-all duration-200 flex flex-col justify-between group/sub"
        >
          <div>
            <div class="flex items-center gap-2 text-amber-400 mb-1.5">
              <Icons :name="feature.icon" size="16" class="shrink-0" />
              <span class="text-xs font-bold text-slate-100 group-hover/sub:text-amber-300 transition-colors">
                {{ feature.title }}
              </span>
            </div>
            <p class="text-xs text-slate-400 leading-relaxed">
              {{ feature.desc }}
            </p>
          </div>
          <div class="mt-2.5 flex items-center gap-1 text-[11px] font-mono text-amber-400/80 group-hover/sub:text-amber-300">
            <span>{{ isVi ? 'Mở phân hệ' : 'Explore' }}</span>
            <Icons name="ChevronRight" size="12" class="group-hover/sub:translate-x-1 transition-transform" />
          </div>
        </a>
      </div>
    </div>

    <!-- Bottom Actions -->
    <div class="pt-4 border-t border-amber-500/20 flex flex-wrap items-center justify-between gap-3">
      <div class="flex flex-wrap gap-2 text-xs font-mono text-slate-400">
        <span class="px-2 py-0.5 rounded bg-white/5 border border-white/5 text-amber-300">50+ Suttas</span>
        <span class="px-2 py-0.5 rounded bg-white/5 border border-white/5 text-amber-300">10 Pali Lessons</span>
        <span class="px-2 py-0.5 rounded bg-white/5 border border-white/5 text-amber-300">Studio CMS</span>
      </div>

      <a
        href="/theravada"
        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-midnight-950 font-bold text-xs tracking-wide transition-all duration-200 hover:-translate-y-0.5 shadow-lg shadow-amber-500/20"
      >
        <span>{{ isVi ? 'Khám Phá Theravāda' : 'Enter Theravāda Platform' }}</span>
        <Icons name="ChevronRight" size="14" />
      </a>
    </div>
  </article>
</template>
