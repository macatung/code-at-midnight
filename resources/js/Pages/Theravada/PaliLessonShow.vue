<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import confetti from 'canvas-confetti';
import TheravadaLayout from '@/Layouts/TheravadaLayout.vue';
import Icons from '@/Components/ui/Icons.vue';
import { mindfulBell } from '@/audio/mindfulBellAudio';
import {
  PALI_LESSON_CATEGORIES,
  PALI_LESSONS,
  PaliLesson,
  findLessonById,
  getAdjacentLessons,
} from '@/data/paliLessonsData';
import { usePaliProgress } from '@/composables/usePaliProgress';
import { useI18n } from '@/composables/useI18n';

interface LessonMetaProp {
  id: string;
  slug: string;
  title: string;
  pali_title?: string;
  category_id?: string;
  order?: number;
}

const props = defineProps<{
  slug: string;
  lessonMeta?: LessonMetaProp;
  prevLesson?: LessonMetaProp | null;
  nextLesson?: LessonMetaProp | null;
  relatedLessons?: LessonMetaProp[];
  title?: string;
}>();

const { t } = useI18n();

const {
  progressState,
  totalLessonsCount,
  completedCount,
  completionPercentage,
  rankInfo,
  isLessonCompleted,
  isLessonBookmarked,
  getLessonScore,
  markLessonCompleted,
  toggleBookmark,
  setLastActiveLesson,
} = usePaliProgress();

// Resolve active lesson from data using slug or ID
const lesson = computed<PaliLesson | null>(() => {
  const targetSlug = props.slug || props.lessonMeta?.slug;
  if (!targetSlug) return PALI_LESSONS[0];
  return findLessonById(targetSlug) || PALI_LESSONS[0];
});

// Category info
const category = computed(() => {
  if (!lesson.value) return PALI_LESSON_CATEGORIES[0];
  return PALI_LESSON_CATEGORIES.find((c) => c.id === lesson.value?.categoryId) || PALI_LESSON_CATEGORIES[0];
});

// Adjacent lessons navigation
const adjacent = computed(() => {
  if (!lesson.value) return { prevLesson: null, nextLesson: null };
  return getAdjacentLessons(lesson.value.slug);
});

const prevTarget = computed(() => props.prevLesson || adjacent.value.prevLesson);
const nextTarget = computed(() => props.nextLesson || adjacent.value.nextLesson);

// UI Reading Controls
const fontSize = ref<'normal' | 'large' | 'xlarge'>('normal');
const isFocusMode = ref(false);
const showExerciseSolutions = ref<Record<number, boolean>>({});

// Quiz State
const selectedAnswers = ref<Record<string, number>>({});
const quizSubmitted = ref(false);
const quizScore = ref(0);

const isBookmarked = computed(() => {
  return lesson.value ? isLessonBookmarked(lesson.value.id) : false;
});

const isCompleted = computed(() => {
  return lesson.value ? isLessonCompleted(lesson.value.id) : false;
});

const currentScore = computed(() => {
  return lesson.value ? getLessonScore(lesson.value.id) : null;
});

// Dynamic In-Page Table of Contents
const tocItems = computed(() => {
  if (!lesson.value) return [];
  const items: Array<{ id: string; label: string; icon?: string; isHighlight?: boolean }> = [
    { id: 'intro', label: 'Căn Bản' },
    { id: 'grammar', label: 'Ngữ Pháp & Bảng Tra' },
    { id: 'vocab', label: `Từ Vựng (${lesson.value.vocabulary.length})` },
  ];
  if (lesson.value.verseAnalysis) {
    items.push({ id: 'verse', label: 'Kệ Ngôn & Cú Pháp' });
  }
  if (lesson.value.practiceExercises && lesson.value.practiceExercises.length > 0) {
    items.push({ id: 'practice', label: `Luyện Tập (${lesson.value.practiceExercises.length})` });
  }
  items.push({ id: 'quiz', label: `Trắc Nghiệm (${lesson.value.quiz.length})`, isHighlight: true });
  return items;
});

onMounted(() => {
  if (lesson.value) {
    setLastActiveLesson(lesson.value.id);
  }
});

const handleToggleBookmark = () => {
  if (!lesson.value) return;
  toggleBookmark(lesson.value.id);
  mindfulBell.strikeWoodenFish();
};

const handleRingMindfulBell = () => {
  mindfulBell.ringBell(528, 3.5);
};

const toggleFontSize = () => {
  if (fontSize.value === 'normal') fontSize.value = 'large';
  else if (fontSize.value === 'large') fontSize.value = 'xlarge';
  else fontSize.value = 'normal';
  mindfulBell.strikeWoodenFish();
};

const toggleFocusMode = () => {
  isFocusMode.value = !isFocusMode.value;
  mindfulBell.strikeWoodenFish();
};

const toggleExerciseSolution = (idx: number) => {
  showExerciseSolutions.value[idx] = !showExerciseSolutions.value[idx];
  mindfulBell.strikeWoodenFish();
};

const handleSelectQuizOption = (qId: string, optIdx: number) => {
  if (quizSubmitted.value) return;
  selectedAnswers.value[qId] = optIdx;
  mindfulBell.strikeWoodenFish();
};

const submitQuiz = () => {
  if (!lesson.value || lesson.value.quiz.length === 0) return;

  let correctCount = 0;
  lesson.value.quiz.forEach((q) => {
    if (selectedAnswers.value[q.id] === q.correctIndex) {
      correctCount++;
    }
  });

  const percentage = Math.round((correctCount / lesson.value.quiz.length) * 100);
  quizScore.value = percentage;
  quizSubmitted.value = true;

  if (percentage >= 70) {
    // Passed quiz
    mindfulBell.ringBell(528, 4.0);
    markLessonCompleted(lesson.value.id, percentage);
    try {
      confetti({
        particleCount: 80,
        spread: 70,
        origin: { y: 0.6 },
        colors: ['#f59e0b', '#fbbf24', '#d97706', '#10b981'],
      });
    } catch {}
  } else {
    mindfulBell.ringBell(260, 2.0);
  }
};

const resetQuiz = () => {
  selectedAnswers.value = {};
  quizSubmitted.value = false;
  quizScore.value = 0;
  mindfulBell.strikeWoodenFish();
};

const handleManualComplete = () => {
  if (!lesson.value) return;
  markLessonCompleted(lesson.value.id, quizScore.value || 100);
  mindfulBell.ringBell(528, 3.5);
  try {
    confetti({
      particleCount: 60,
      spread: 60,
      origin: { y: 0.5 },
      colors: ['#f59e0b', '#fbbf24', '#10b981'],
    });
  } catch {}
};

const playVocabSound = () => {
  mindfulBell.ringBell(432, 2.0);
};

// Structured Schema for SEO
const lessonJsonLd = computed(() => {
  if (!lesson.value) return null;
  return {
    '@context': 'https://schema.org',
    '@graph': [
      {
        '@type': 'Course',
        '@id': `https://theravada.macatung.dev/hoc-pali/${lesson.value.slug}#course`,
        name: lesson.value.title,
        alternateName: lesson.value.paliTitle,
        description: lesson.value.description,
        provider: {
          '@type': 'Organization',
          name: 'Ma Tọa Thiền • macatung.dev',
          url: 'https://theravada.macatung.dev',
        },
        inLanguage: ['vi', 'pi'],
        educationalLevel: lesson.value.level,
      },
      {
        '@type': 'BreadcrumbList',
        itemListElement: [
          {
            '@type': 'ListItem',
            position: 1,
            name: 'Theravāda',
            item: 'https://theravada.macatung.dev',
          },
          {
            '@type': 'ListItem',
            position: 2,
            name: 'Học Tiếng Pāḷi',
            item: 'https://theravada.macatung.dev/hoc-pali',
          },
          {
            '@type': 'ListItem',
            position: 3,
            name: lesson.value.title,
            item: `https://theravada.macatung.dev/hoc-pali/${lesson.value.slug}`,
          },
        ],
      },
    ],
  };
});
</script>

<template>
  <TheravadaLayout
    v-if="lesson"
    :title="title || `${lesson.title} — Học Tiếng Pāḷi — Ma Tọa Thiền`"
    :description="lesson.description"
    :keywords="`Pali learning, ${lesson.tags.join(', ')}, Hoc tieng Pali, Pāḷi Tipiṭaka`"
    :canonical="`https://theravada.macatung.dev/hoc-pali/${lesson.slug}`"
    :json-ld="lessonJsonLd"
  >
    <div
      class="max-w-6xl mx-auto py-3 sm:py-6 px-2 sm:px-4 font-serif text-stone-100 transition-all duration-300 space-y-6 sm:space-y-8"
      :class="{
        'text-base': fontSize === 'normal',
        'text-lg sm:text-xl': fontSize === 'large',
        'text-xl sm:text-2xl': fontSize === 'xlarge',
      }"
    >
      <!-- 1. Top Breadcrumb & Study Control Bar -->
      <div class="flex flex-wrap items-center justify-between gap-3 border-b border-stone-800/90 pb-3.5">
        <!-- Breadcrumb -->
        <nav class="flex flex-wrap items-center gap-1.5 sm:gap-2 text-[11px] sm:text-xs text-stone-400 font-sans">
          <Link href="/theravada" class="hover:text-amber-300 transition-colors">Theravāda</Link>
          <span>/</span>
          <Link href="/theravada/hoc-pali" class="hover:text-amber-300 transition-colors">Học Tiếng Pāḷi</Link>
          <span>/</span>
          <span class="text-amber-400 font-semibold truncate max-w-[200px] sm:max-w-xs">
            {{ lesson.title }}
          </span>
        </nav>

        <!-- Right Toolbar Tools -->
        <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
          <!-- Font Size Adjuster -->
          <button
            type="button"
            @click="toggleFontSize"
            class="px-2.5 py-1.5 rounded-xl bg-stone-900 border border-stone-700 text-stone-300 hover:text-amber-300 hover:border-amber-500/40 text-xs font-mono font-bold transition-all cursor-pointer flex items-center gap-1"
            :title="`Cỡ chữ hiện tại: ${fontSize}`"
          >
            <span>A</span>
            <span class="text-[10px] text-amber-400 font-sans">
              {{ fontSize === 'normal' ? 'Chuẩn' : fontSize === 'large' ? '+ Lớn' : '++ Rất lớn' }}
            </span>
          </button>

          <!-- Mindful Bell -->
          <button
            type="button"
            @click="handleRingMindfulBell"
            class="px-2.5 py-1.5 rounded-xl bg-stone-900 border border-stone-700 text-amber-400 hover:bg-amber-500/10 hover:border-amber-500/40 text-xs transition-all cursor-pointer flex items-center gap-1"
            title="Thỉnh chuông chánh niệm"
          >
            <span>🔔</span>
            <span class="hidden sm:inline text-[11px] text-stone-300 font-sans">Chuông</span>
          </button>

          <!-- Focus Mode Toggle -->
          <button
            type="button"
            @click="toggleFocusMode"
            :class="[
              'px-2.5 py-1.5 rounded-xl border text-xs transition-all cursor-pointer flex items-center gap-1 font-sans',
              isFocusMode
                ? 'bg-amber-500 text-stone-950 font-bold border-amber-400'
                : 'bg-stone-900 border-stone-700 text-stone-300 hover:text-white'
            ]"
            title="Chế độ đọc tập trung không phân tâm"
          >
            <span>🧘</span>
            <span class="hidden sm:inline text-[11px]">Tập Trung</span>
          </button>

          <!-- Bookmark Button -->
          <button
            type="button"
            @click="handleToggleBookmark"
            :class="[
              'p-2 rounded-xl border transition-all cursor-pointer flex items-center justify-center min-h-[34px] min-w-[34px]',
              isBookmarked
                ? 'bg-amber-500 text-stone-950 border-amber-400 shadow-md'
                : 'bg-stone-900 border-stone-700 text-stone-400 hover:text-amber-300 hover:border-amber-500/40'
            ]"
            :title="isBookmarked ? 'Bỏ lưu bài học' : 'Lưu bài học'"
          >
            <Icons name="Sparkles" :size="15" />
          </button>
        </div>
      </div>

      <!-- 2. Quick In-Page Table of Contents (Anchor Navigation) -->
      <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 py-2 px-3 sm:px-4 rounded-2xl bg-stone-900/60 border border-stone-800 text-xs font-sans">
        <span class="text-amber-400 font-bold flex items-center gap-1">
          <Icons name="Compass" :size="14" />
          <span class="hidden sm:inline">Mục lục:</span>
        </span>
        <a
          v-for="(item, idx) in tocItems"
          :key="item.id"
          :href="`#${item.id}`"
          class="transition-colors px-2.5 py-1 rounded-lg flex items-center gap-1"
          :class="[
            item.isHighlight
              ? 'text-amber-300 font-bold hover:text-amber-200 bg-amber-500/10 border border-amber-500/20'
              : 'text-stone-300 hover:text-amber-300 hover:bg-stone-800'
          ]"
        >
          <span class="text-stone-500 font-mono text-[10px]">§{{ idx + 1 }}</span>
          <span>{{ item.label }}</span>
        </a>
      </div>

      <!-- 3. Main Grid Layout (Article Body + Sticky Sidebar) -->
      <div class="grid grid-cols-1 gap-6 sm:gap-8" :class="isFocusMode ? '' : 'lg:grid-cols-12'">
        
        <!-- Left: Main Pedagogical Content -->
        <main :class="isFocusMode ? 'w-full' : 'lg:col-span-8'" class="space-y-8 sm:space-y-10 text-left">
          
          <!-- Hero Header Section -->
          <header class="p-5 sm:p-8 rounded-3xl bg-gradient-to-br from-amber-950/40 via-stone-900/90 to-stone-950 border border-amber-500/30 shadow-2xl space-y-4">
            <div class="flex flex-wrap items-center gap-2">
              <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-sans font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                {{ category.name }}
              </span>
              <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-sans font-bold bg-stone-800 text-stone-300 border border-stone-700">
                Bài {{ lesson.order }}/10
              </span>
              <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-sans font-semibold bg-amber-500/10 text-amber-300">
                {{ lesson.level }}
              </span>
              <span class="text-xs text-stone-400 font-sans flex items-center gap-1 ml-auto">
                <Icons name="Clock" :size="13" class="text-amber-400" />
                <span>{{ lesson.estimatedMinutes }} phút học</span>
              </span>
            </div>

            <div>
              <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-amber-100 leading-tight">
                {{ lesson.title }}
              </h1>
              <p class="text-sm sm:text-base text-amber-300/90 font-sans italic mt-1">
                {{ lesson.paliTitle }}
              </p>
            </div>

            <p class="text-xs sm:text-sm text-stone-300 leading-relaxed border-t border-stone-800 pt-3">
              {{ lesson.description }}
            </p>

            <!-- Tags -->
            <div class="flex flex-wrap gap-1.5 pt-1">
              <span
                v-for="(tag, idx) in lesson.tags"
                :key="idx"
                class="px-2.5 py-0.5 rounded-md text-[10px] font-sans bg-stone-950/80 border border-stone-800 text-stone-400"
              >
                #{{ tag }}
              </span>
            </div>
          </header>

          <!-- Key Highlights Callout -->
          <div class="p-5 rounded-2xl bg-amber-500/10 border-l-4 border-amber-500 space-y-2">
            <h3 class="text-xs sm:text-sm font-bold text-amber-300 uppercase tracking-wider flex items-center gap-2">
              <span>☸️</span>
              <span>Điểm Trọng Tâm Cần Ghi Nhớ</span>
            </h3>
            <ul class="space-y-1.5 text-xs sm:text-sm text-stone-200 list-disc list-inside">
              <li v-for="(point, pIdx) in lesson.summaryPoints" :key="pIdx" class="leading-relaxed">
                {{ point }}
              </li>
            </ul>
          </div>

          <!-- Section 1: Beginner Foundation Guide -->
          <section v-if="lesson.beginnerGuide" id="intro" class="space-y-4 pt-2">
            <div class="border-b border-amber-500/20 pb-2 flex items-center gap-2">
              <span class="text-amber-400 text-lg">🌱</span>
              <h2 class="text-lg sm:text-xl font-bold text-amber-200">
                {{ lesson.beginnerGuide.title }}
              </h2>
            </div>

            <p class="text-xs sm:text-sm text-stone-300 leading-relaxed">
              {{ lesson.beginnerGuide.introduction }}
            </p>

            <!-- Core Concept Card -->
            <div class="p-4 sm:p-5 rounded-2xl bg-stone-900 border border-amber-500/30 text-amber-200/90 text-xs sm:text-sm leading-relaxed">
              <strong class="text-amber-300 block mb-1 text-xs uppercase tracking-wide">
                💡 Khái niệm nền tảng:
              </strong>
              {{ lesson.beginnerGuide.coreConcept }}
            </div>

            <!-- Step by Step -->
            <div class="space-y-3 pt-2">
              <div
                v-for="(stepItem, sIdx) in lesson.beginnerGuide.stepByStep"
                :key="sIdx"
                class="p-4 rounded-2xl bg-stone-900/70 border border-stone-800 space-y-1.5"
              >
                <h4 class="text-xs sm:text-sm font-bold text-amber-300">
                  {{ stepItem.step }}
                </h4>
                <p class="text-xs sm:text-sm text-stone-300 leading-relaxed">
                  {{ stepItem.explanation }}
                </p>
                <div v-if="stepItem.example" class="text-xs text-amber-100 font-sans bg-stone-950 p-2 rounded-lg border border-stone-800/80">
                  <span class="text-stone-400">Ví dụ:</span> {{ stepItem.example }}
                </div>
              </div>
            </div>

            <!-- Common Pitfalls & Tips -->
            <div v-if="lesson.beginnerGuide.commonMistakes" class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
              <div class="p-4 rounded-2xl bg-red-950/20 border border-red-500/30 text-xs sm:text-sm space-y-1.5">
                <h4 class="font-bold text-red-300 flex items-center gap-1.5">
                  <span>⚠️</span>
                  <span>Lỗi Thường Gặp</span>
                </h4>
                <ul class="list-disc list-inside text-stone-300 space-y-1 text-xs">
                  <li v-for="(m, mIdx) in lesson.beginnerGuide.commonMistakes" :key="mIdx">
                    {{ m }}
                  </li>
                </ul>
              </div>

              <div v-if="lesson.beginnerGuide.memoryTips" class="p-4 rounded-2xl bg-emerald-950/20 border border-emerald-500/30 text-xs sm:text-sm space-y-1.5">
                <h4 class="font-bold text-emerald-300 flex items-center gap-1.5">
                  <span>🧠</span>
                  <span>Mẹo Ghi Nhớ Nhanh</span>
                </h4>
                <ul class="list-disc list-inside text-stone-300 space-y-1 text-xs">
                  <li v-for="(t, tIdx) in lesson.beginnerGuide.memoryTips" :key="tIdx">
                    {{ t }}
                  </li>
                </ul>
              </div>
            </div>
          </section>

          <!-- Section 2: Grammar & Paradigms -->
          <section id="grammar" class="space-y-6 pt-2">
            <div class="border-b border-amber-500/20 pb-2 flex items-center gap-2">
              <span class="text-amber-400 text-lg">📖</span>
              <h2 class="text-lg sm:text-xl font-bold text-amber-200">
                Lý Thuyết & Quy Tắc Ngữ Pháp (Saddanīti)
              </h2>
            </div>

            <div v-for="(sec, gIdx) in lesson.grammarSections" :key="gIdx" class="space-y-3.5">
              <h3 class="text-base sm:text-lg font-bold text-amber-200 flex items-center gap-2">
                <span class="text-amber-400">§{{ gIdx + 1 }}.</span>
                <span>{{ sec.title }}</span>
              </h3>
              <p class="text-xs sm:text-sm text-stone-300 leading-relaxed whitespace-pre-line">
                {{ sec.explanation }}
              </p>

              <!-- Grammar Table -->
              <div v-if="sec.table" class="overflow-x-auto rounded-2xl border border-stone-800 bg-stone-900/90 p-1 shadow-lg">
                <table class="w-full text-xs sm:text-sm text-left border-collapse">
                  <thead>
                    <tr class="bg-amber-500/20 text-amber-300 border-b border-amber-500/30 font-bold font-sans">
                      <th v-for="(h, hIdx) in sec.table.headers" :key="hIdx" class="px-3 sm:px-4 py-2.5 whitespace-nowrap">
                        {{ h }}
                      </th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-stone-800">
                    <tr
                      v-for="(row, rIdx) in sec.table.rows"
                      :key="rIdx"
                      class="hover:bg-stone-800/40 transition-colors"
                    >
                      <td
                        v-for="(cell, cIdx) in row"
                        :key="cIdx"
                        class="px-3 sm:px-4 py-2.5 text-stone-200"
                        :class="{ 'font-bold text-amber-200': cIdx === 0 }"
                      >
                        {{ cell }}
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <!-- Tip Box -->
              <div v-if="sec.tip" class="p-3.5 rounded-xl bg-stone-900 border-l-4 border-amber-500 text-xs sm:text-sm text-amber-200/90 italic flex items-start gap-2">
                <span class="text-base">💡</span>
                <span class="leading-relaxed">{{ sec.tip }}</span>
              </div>
            </div>
          </section>

          <!-- Section 3: Vocabulary & Roots -->
          <section id="vocab" class="space-y-4 pt-2">
            <div class="border-b border-amber-500/20 pb-2 flex items-center justify-between gap-2">
              <div class="flex items-center gap-2">
                <span class="text-amber-400 text-lg">🔤</span>
                <h2 class="text-lg sm:text-xl font-bold text-amber-200">
                  Kho Từ Vựng & Căn Ngữ ({{ lesson.vocabulary.length }} từ)
                </h2>
              </div>
              <span class="text-xs text-stone-400 font-sans hidden sm:inline">
                Nhấp chuông 🔔 để nghe âm vang thiền vị
              </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
              <div
                v-for="(item, vIdx) in lesson.vocabulary"
                :key="vIdx"
                class="p-4 rounded-2xl bg-stone-900/80 border border-stone-800 hover:border-amber-500/50 transition-all space-y-2 group shadow-sm"
              >
                <div class="flex items-start justify-between gap-2">
                  <div>
                    <div class="flex items-center gap-2">
                      <span class="text-base sm:text-lg font-bold text-amber-300">
                        {{ item.term }}
                      </span>
                      <button
                        type="button"
                        @click="playVocabSound"
                        class="text-xs p-1 rounded-md text-amber-400 hover:bg-amber-500/20 transition-all cursor-pointer"
                        title="Thỉnh chuông ôn từ"
                      >
                        🔔
                      </button>
                    </div>
                    <span v-if="item.ipa" class="text-[11px] font-mono text-stone-400 block">
                      {{ item.ipa }}
                    </span>
                  </div>

                  <span class="text-[10px] font-sans px-2 py-0.5 rounded-full bg-stone-800 text-amber-300 border border-stone-700 whitespace-nowrap">
                    {{ item.partOfSpeech }}
                  </span>
                </div>

                <p class="text-xs sm:text-sm text-stone-100 font-semibold leading-snug">
                  {{ item.vietnamese }}
                </p>

                <div v-if="item.root || item.note || item.example" class="pt-1 text-[11px] text-stone-400 space-y-0.5 border-t border-stone-800/80 font-sans">
                  <div v-if="item.root" class="font-mono text-amber-300/90">
                    <span class="text-stone-400 font-sans">Căn ngữ:</span> {{ item.root }}
                  </div>
                  <div v-if="item.note" class="italic text-stone-300">
                    {{ item.note }}
                  </div>
                  <div v-if="item.example" class="text-amber-100/90 font-serif">
                    <span class="text-stone-400 font-sans">Ví dụ:</span> {{ item.example }}
                  </div>
                </div>
              </div>
            </div>
          </section>

          <!-- Section 4: Verse & Sutta Analysis -->
          <section v-if="lesson.verseAnalysis" id="verse" class="space-y-6 pt-2">
            <div class="border-b border-amber-500/20 pb-2 flex items-center gap-2">
              <span class="text-amber-400 text-lg">📜</span>
              <h2 class="text-lg sm:text-xl font-bold text-amber-200">
                Khảo Sát Kệ Ngôn & Phân Tích Cú Pháp Từng Chữ
              </h2>
            </div>

            <!-- Sacred Verse Card -->
            <div class="p-6 rounded-3xl bg-gradient-to-br from-amber-950/40 via-stone-900 to-stone-950 border border-amber-500/40 text-center space-y-3 shadow-xl">
              <div class="text-base sm:text-xl text-amber-200 font-serif leading-relaxed whitespace-pre-line font-bold">
                {{ lesson.verseAnalysis.originalPali }}
              </div>
              <div class="text-xs sm:text-sm text-stone-300 italic whitespace-pre-line border-t border-amber-500/20 pt-3">
                {{ lesson.verseAnalysis.vietnamese }}
              </div>
              <div v-if="lesson.verseAnalysis.english" class="text-xs text-stone-400 italic pt-1">
                "{{ lesson.verseAnalysis.english }}"
              </div>
              <p v-if="lesson.verseAnalysis.context" class="text-[11px] text-stone-400 border-t border-stone-800 pt-2 font-sans">
                <strong class="text-amber-400">Bối cảnh xuất xứ:</strong> {{ lesson.verseAnalysis.context }}
              </p>
            </div>

            <!-- Word-by-Word Breakdown Table -->
            <div class="space-y-2">
              <h3 class="text-xs sm:text-sm font-bold text-amber-300 uppercase tracking-wider font-sans">
                Bảng Phân Tích Cú Pháp Từng Từ (Word-by-Word Breakdown)
              </h3>
              <div class="overflow-x-auto rounded-2xl border border-stone-800 bg-stone-900/90 shadow-lg">
                <table class="w-full text-xs sm:text-sm text-left border-collapse">
                  <thead>
                    <tr class="bg-amber-500/20 text-amber-300 border-b border-amber-500/30 font-bold font-sans">
                      <th class="px-3 sm:px-4 py-2.5 whitespace-nowrap">Từ Pāḷi</th>
                      <th class="px-3 sm:px-4 py-2.5 whitespace-nowrap">Phân Tích Ngữ Pháp</th>
                      <th class="px-3 sm:px-4 py-2.5 whitespace-nowrap">Căn / Thân Từ</th>
                      <th class="px-3 sm:px-4 py-2.5">Ý Nghĩa Cú Pháp</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-stone-800">
                    <tr
                      v-for="(w, wIdx) in lesson.verseAnalysis.breakdown"
                      :key="wIdx"
                      class="hover:bg-stone-800/40 transition-colors"
                    >
                      <td class="px-3 sm:px-4 py-2.5 font-bold text-amber-200 whitespace-nowrap">
                        {{ w.word }}
                      </td>
                      <td class="px-3 sm:px-4 py-2.5 text-stone-300 font-sans text-xs">
                        {{ w.grammar }}
                      </td>
                      <td class="px-3 sm:px-4 py-2.5 font-mono text-xs text-amber-400/80">
                        {{ w.rootOrStem || '—' }}
                      </td>
                      <td class="px-3 sm:px-4 py-2.5 text-stone-200">
                        {{ w.meaning }}
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </section>

          <!-- Section 5: Practice Exercises -->
          <section v-if="lesson.practiceExercises" id="practice" class="space-y-4 pt-2">
            <div class="border-b border-amber-500/20 pb-2 flex items-center gap-2">
              <span class="text-amber-400 text-lg">✍️</span>
              <h2 class="text-lg sm:text-xl font-bold text-amber-200">
                Bài Tập Thực Hành & Củng Cố
              </h2>
            </div>

            <div class="space-y-3.5">
              <div
                v-for="(ex, eIdx) in lesson.practiceExercises"
                :key="eIdx"
                class="p-4 sm:p-5 rounded-2xl bg-stone-900/80 border border-stone-800 space-y-2.5"
              >
                <div class="flex items-start justify-between gap-2">
                  <span class="px-2 py-0.5 rounded-md bg-amber-500/20 text-amber-300 text-xs font-sans font-bold">
                    Bài tập {{ eIdx + 1 }}
                  </span>
                  <button
                    type="button"
                    @click="toggleExerciseSolution(eIdx)"
                    class="text-xs text-amber-400 hover:text-amber-300 underline font-sans cursor-pointer"
                  >
                    {{ showExerciseSolutions[eIdx] ? 'Ẩn đáp án' : 'Xem đáp án & giải thích' }}
                  </button>
                </div>

                <p class="text-xs sm:text-sm font-semibold text-stone-200">
                  {{ ex.instruction }}
                </p>

                <div class="p-3 rounded-xl bg-stone-950 border border-stone-800 font-serif text-amber-200 text-xs sm:text-sm font-bold">
                  {{ ex.paliText }}
                </div>

                <div v-if="ex.hint && !showExerciseSolutions[eIdx]" class="text-[11px] text-stone-400 italic">
                  💡 Gợi ý: {{ ex.hint }}
                </div>

                <div
                  v-if="showExerciseSolutions[eIdx]"
                  class="p-3.5 rounded-xl bg-emerald-950/30 border border-emerald-500/30 text-xs sm:text-sm text-emerald-200 space-y-1"
                >
                  <div><strong>Đáp án:</strong> {{ ex.solution }}</div>
                  <div v-if="ex.breakdown" class="text-stone-300 text-xs font-sans pt-1">
                    <strong>Phân tích:</strong> {{ ex.breakdown }}
                  </div>
                </div>
              </div>
            </div>
          </section>

          <!-- Section 6: Interactive Quiz -->
          <section id="quiz" class="space-y-6 pt-2">
            <div class="border-b border-amber-500/20 pb-2 flex flex-wrap items-center justify-between gap-2">
              <div class="flex items-center gap-2">
                <span class="text-amber-400 text-lg">✨</span>
                <h2 class="text-lg sm:text-xl font-bold text-amber-200">
                  Bài Tập Trắc Nghiệm Củng Cố Kiến Thức ({{ lesson.quiz.length }} câu)
                </h2>
              </div>
              <div v-if="quizSubmitted" class="text-xs font-bold font-sans px-3 py-1 rounded-xl bg-amber-500 text-stone-950 shadow-md">
                Điểm Đạt Được: {{ quizScore }}%
              </div>
            </div>

            <div class="space-y-5">
              <div
                v-for="(q, qIdx) in lesson.quiz"
                :key="q.id"
                class="p-4 sm:p-6 rounded-2xl bg-stone-900/90 border border-stone-800 space-y-3 shadow-md"
              >
                <div class="flex items-start gap-2.5">
                  <span class="px-2 py-0.5 rounded-md bg-amber-500/20 text-amber-300 text-xs font-sans font-bold shrink-0 mt-0.5">
                    Câu {{ qIdx + 1 }}
                  </span>
                  <h4 class="text-xs sm:text-base font-bold text-stone-100 leading-snug">
                    {{ q.question }}
                  </h4>
                </div>

                <!-- Options -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-1">
                  <button
                    v-for="(opt, optIdx) in q.options"
                    :key="optIdx"
                    type="button"
                    @click="handleSelectQuizOption(q.id, optIdx)"
                    :disabled="quizSubmitted"
                    :class="[
                      'p-3 rounded-xl text-xs sm:text-sm text-left transition-all border flex items-start gap-2 cursor-pointer font-serif',
                      selectedAnswers[q.id] === optIdx
                        ? 'border-amber-400 bg-amber-500/20 text-amber-200 font-bold shadow-sm'
                        : 'border-stone-800 bg-stone-950/80 text-stone-300 hover:border-amber-500/40 hover:bg-stone-900',
                      quizSubmitted && optIdx === q.correctIndex
                        ? '!border-emerald-500 !bg-emerald-500/20 !text-emerald-200 !font-bold'
                        : '',
                      quizSubmitted && selectedAnswers[q.id] === optIdx && optIdx !== q.correctIndex
                        ? '!border-red-500 !bg-red-500/20 !text-red-200'
                        : ''
                    ]"
                  >
                    <span class="font-sans font-bold text-xs opacity-70 mt-0.5">
                      {{ ['A', 'B', 'C', 'D'][optIdx] }}.
                    </span>
                    <span class="flex-1">{{ opt }}</span>
                  </button>
                </div>

                <!-- Explanation when submitted -->
                <div
                  v-if="quizSubmitted"
                  class="p-3.5 rounded-xl text-xs sm:text-sm text-left leading-relaxed mt-2"
                  :class="selectedAnswers[q.id] === q.correctIndex ? 'bg-emerald-950/40 border border-emerald-500/40 text-emerald-200' : 'bg-red-950/40 border border-red-500/40 text-red-200'"
                >
                  <strong class="block mb-0.5 font-bold font-sans">
                    {{ selectedAnswers[q.id] === q.correctIndex ? '✓ Chính xác!' : '✗ Chưa chính xác!' }}
                  </strong>
                  {{ q.explanation }}
                </div>
              </div>
            </div>

            <!-- Quiz Bottom Actions -->
            <div class="pt-4 border-t border-stone-800 flex flex-wrap items-center justify-between gap-3">
              <div class="flex items-center gap-2">
                <button
                  v-if="!quizSubmitted"
                  type="button"
                  @click="submitQuiz"
                  class="px-6 py-3 rounded-2xl bg-gradient-to-r from-amber-400 to-amber-500 text-stone-950 font-bold text-xs sm:text-sm hover:brightness-110 transition-all shadow-lg cursor-pointer"
                >
                  Nộp Bài Trắc Nghiệm ➔
                </button>
                <button
                  v-else
                  type="button"
                  @click="resetQuiz"
                  class="px-5 py-2.5 rounded-2xl bg-stone-900 border border-stone-700 text-stone-300 hover:text-white text-xs sm:text-sm font-semibold transition-all cursor-pointer"
                >
                  Làm Lại Bài Thi
                </button>
              </div>

              <button
                type="button"
                @click="handleManualComplete"
                class="px-4 py-2.5 rounded-2xl bg-stone-900 border border-emerald-500/40 text-emerald-300 hover:bg-emerald-500/20 text-xs sm:text-sm font-semibold transition-all flex items-center gap-1.5 cursor-pointer"
              >
                <Icons name="Check" :size="16" />
                <span>{{ isCompleted ? 'Đã Hoàn Thành Bài' : 'Đánh Dấu Hoàn Thành' }}</span>
              </button>
            </div>
          </section>

          <!-- 4. Prev / Next Lesson Navigation Cards -->
          <div class="pt-6 border-t border-stone-800 grid grid-cols-1 sm:grid-cols-2 gap-4">
            <Link
              v-if="prevTarget"
              :href="`/theravada/hoc-pali/${prevTarget.slug}`"
              class="p-4 rounded-2xl bg-stone-900/70 border border-stone-800 hover:border-amber-500/50 transition-all group space-y-1 text-left"
            >
              <div class="text-[11px] font-sans text-stone-400 flex items-center gap-1">
                <span>←</span>
                <span>Bài Trước</span>
              </div>
              <div class="text-xs sm:text-sm font-bold text-amber-200 group-hover:text-amber-300">
                {{ prevTarget.title }}
              </div>
            </Link>
            <div v-else class="p-4 rounded-2xl bg-stone-950/40 border border-stone-900 text-stone-600 text-xs font-sans">
              Đây là bài đầu tiên trong chương trình.
            </div>

            <Link
              v-if="nextTarget"
              :href="`/theravada/hoc-pali/${nextTarget.slug}`"
              class="p-4 rounded-2xl bg-stone-900/70 border border-stone-800 hover:border-amber-500/50 transition-all group space-y-1 text-right sm:text-right"
            >
              <div class="text-[11px] font-sans text-stone-400 flex items-center justify-end gap-1">
                <span>Bài Tiếp Theo</span>
                <span>→</span>
              </div>
              <div class="text-xs sm:text-sm font-bold text-amber-200 group-hover:text-amber-300">
                {{ nextTarget.title }}
              </div>
            </Link>
            <div v-else class="p-4 rounded-2xl bg-stone-950/40 border border-stone-900 text-stone-600 text-xs font-sans text-right">
              Bạn đã đến bài học cuối cùng!
            </div>
          </div>

        </main>

        <!-- Right: Sticky Study Navigator & Progress Sidebar (hidden in focus mode) -->
        <aside v-if="!isFocusMode" class="lg:col-span-4 space-y-5 text-left">
          
          <!-- Progress Box -->
          <div class="p-5 rounded-3xl bg-gradient-to-br from-amber-950/40 via-stone-900 to-stone-950 border border-amber-500/30 shadow-xl space-y-3 sticky top-4">
            <div class="flex items-center justify-between gap-2">
              <span class="text-xs font-bold text-amber-200">Tiến Độ Toàn Khóa</span>
              <span class="text-xs font-mono font-bold text-amber-400">
                {{ completedCount }}/{{ totalLessonsCount }} bài ({{ completionPercentage }}%)
              </span>
            </div>

            <div class="w-full h-2.5 rounded-full bg-stone-950 border border-stone-800 overflow-hidden p-0.5">
              <div
                class="h-full rounded-full bg-gradient-to-r from-amber-500 to-yellow-400 transition-all duration-500"
                :style="{ width: `${completionPercentage}%` }"
              />
            </div>

            <div class="text-[11px] text-stone-400 font-sans italic">
              {{ rankInfo.badge }} — {{ rankInfo.title }}
            </div>

            <!-- List of All 10 Lessons -->
            <div class="pt-3 border-t border-stone-800 space-y-1.5 max-h-[380px] overflow-y-auto pr-1">
              <div class="text-[11px] font-sans font-bold text-stone-400 uppercase tracking-wider mb-1">
                Danh Sách Bài Học (10 Bài):
              </div>
              <Link
                v-for="l in PALI_LESSONS"
                :key="l.id"
                :href="`/theravada/hoc-pali/${l.slug}`"
                :class="[
                  'block p-2.5 rounded-xl text-xs transition-all border',
                  l.slug === lesson.slug
                    ? 'bg-amber-500 text-stone-950 font-bold border-amber-400 shadow-md'
                    : isLessonCompleted(l.id)
                    ? 'bg-stone-900/90 border-emerald-500/30 text-stone-200 hover:text-white hover:border-emerald-500/50'
                    : 'bg-stone-950 border-stone-800/80 text-stone-400 hover:text-amber-200 hover:border-stone-700'
                ]"
              >
                <div class="flex items-center justify-between gap-1.5">
                  <span class="truncate">Bài {{ l.order }}: {{ l.title.replace(/^Bài \d+:\s*/, '') }}</span>
                  <span v-if="isLessonCompleted(l.id)" class="text-[10px] shrink-0" :class="l.slug === lesson.slug ? 'text-stone-950' : 'text-emerald-400'">
                    ✓
                  </span>
                </div>
              </Link>
            </div>

            <!-- Back to Hub Button -->
            <div class="pt-3 border-t border-stone-800">
              <Link
                href="/theravada/hoc-pali"
                class="w-full py-2 px-3 rounded-xl bg-stone-900 border border-stone-700 hover:border-amber-500/40 text-stone-300 hover:text-amber-200 text-xs font-semibold transition-all flex items-center justify-center gap-1.5"
              >
                <span>☸️</span>
                <span>Về Trang Tổng Quan Khóa Học</span>
              </Link>
            </div>
          </div>

          <!-- Dictionary Cross Link -->
          <div class="p-4 rounded-2xl bg-stone-900/60 border border-stone-800 space-y-2">
            <h4 class="text-xs font-bold text-amber-300 flex items-center gap-1.5">
              <span>📖</span>
              <span>Từ Điển Thuật Ngữ Pāḷi</span>
            </h4>
            <p class="text-[11px] text-stone-400">
              Tra cứu nhanh hơn 45+ thuật ngữ Phật học cốt lõi từ A-Z với định nghĩa chuyên sâu.
            </p>
            <Link
              href="/theravada/tu-dien-pali"
              class="inline-block text-xs text-amber-400 hover:text-amber-300 underline font-sans"
            >
              Mở Từ Điển Pāḷi ➔
            </Link>
          </div>

        </aside>

      </div>

    </div>
  </TheravadaLayout>
</template>
