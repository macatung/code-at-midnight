<script setup lang="ts">
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import confetti from 'canvas-confetti';
import Icons from '@/Components/ui/Icons.vue';
import { mindfulBell } from '@/audio/mindfulBellAudio';
import { PaliLesson } from '@/data/paliLessonsData';
import { usePaliProgress } from '@/composables/usePaliProgress';

const props = defineProps<{
  isOpen: boolean;
  lesson: PaliLesson | null;
}>();

const emit = defineEmits<{
  (e: 'close'): void;
  (e: 'selectLesson', lessonId: string): void;
}>();

const {
  isLessonCompleted,
  isLessonBookmarked,
  markLessonCompleted,
  toggleBookmark,
  getLessonScore,
} = usePaliProgress();

const activeTab = ref<'theory' | 'vocab' | 'verse' | 'quiz'>('theory');

// Quiz State
const selectedAnswers = ref<Record<string, number>>({});
const quizSubmitted = ref(false);
const quizScore = ref(0);

// Initialize or reset quiz on lesson change
watch(
  () => props.lesson,
  (newLesson) => {
    selectedAnswers.value = {};
    quizSubmitted.value = false;
    quizScore.value = 0;
    activeTab.value = 'theory';
  },
  { immediate: true }
);

watch(
  () => props.isOpen,
  (open) => {
    if (typeof document !== 'undefined') {
      if (open) {
        document.body.style.overflow = 'hidden';
      } else {
        document.body.style.overflow = '';
      }
    }
  },
  { immediate: true }
);

const handleKeyDown = (e: KeyboardEvent) => {
  if (e.key === 'Escape' && props.isOpen) {
    emit('close');
  }
};

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
    document.body.style.overflow = '';
  }
});

const isBookmarked = computed(() => {
  return props.lesson ? isLessonBookmarked(props.lesson.id) : false;
});

const isCompleted = computed(() => {
  return props.lesson ? isLessonCompleted(props.lesson.id) : false;
});

const currentScore = computed(() => {
  return props.lesson ? getLessonScore(props.lesson.id) : null;
});

const handleSelectOption = (questionId: string, optionIdx: number) => {
  if (quizSubmitted.value) return;
  selectedAnswers.value[questionId] = optionIdx;
  mindfulBell.strikeWoodenFish();
};

const handleBookmarkToggle = () => {
  if (!props.lesson) return;
  toggleBookmark(props.lesson.id);
  mindfulBell.strikeWoodenFish();
};

const submitQuiz = () => {
  if (!props.lesson || props.lesson.quiz.length === 0) return;

  let correctCount = 0;
  props.lesson.quiz.forEach((q) => {
    if (selectedAnswers.value[q.id] === q.correctIndex) {
      correctCount++;
    }
  });

  const percentage = Math.round((correctCount / props.lesson.quiz.length) * 100);
  quizScore.value = percentage;
  quizSubmitted.value = true;

  if (percentage >= 70) {
    // Passing score
    mindfulBell.ringBell(528, 4.0);
    markLessonCompleted(props.lesson.id, percentage);
    try {
      confetti({
        particleCount: 80,
        spread: 70,
        origin: { y: 0.6 },
        colors: ['#f59e0b', '#fbbf24', '#d97706', '#10b981'],
      });
    } catch {
      // Ignored in non-browser environments
    }
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

const playVocabSound = (term: string) => {
  mindfulBell.ringBell(432, 2.5);
};

const handleMarkCompleteManual = () => {
  if (!props.lesson) return;
  markLessonCompleted(props.lesson.id, quizScore.value || 100);
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
</script>

<template>
  <transition
    enter-active-class="transition duration-300 ease-out"
    enter-from-class="opacity-0"
    enter-to-class="opacity-100"
    leave-active-class="transition duration-200 ease-in"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <div
      v-if="isOpen && lesson"
      class="fixed inset-0 z-50 flex items-center justify-center p-2 sm:p-4 md:p-6 bg-black/85 backdrop-blur-md overflow-y-auto"
      role="dialog"
      aria-modal="true"
      aria-labelledby="lesson-modal-title"
      @click.self="emit('close')"
    >
      <div
        class="relative w-full max-w-4xl max-h-[92vh] bg-stone-950 border border-amber-500/40 rounded-3xl shadow-[0_25px_60px_rgba(0,0,0,0.9)] flex flex-col overflow-hidden text-stone-100 font-serif"
      >
        <!-- Modal Top Header Banner -->
        <div class="relative px-5 sm:px-8 py-5 border-b border-amber-500/20 bg-gradient-to-r from-amber-950/50 via-stone-900 to-stone-950">
          <div class="flex items-start justify-between gap-4">
            <div class="space-y-1.5 min-w-0">
              <div class="flex flex-wrap items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-md text-[11px] font-sans font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                  {{ lesson.level }}
                </span>
                <span class="text-xs text-stone-400 font-sans flex items-center gap-1">
                  <Icons name="Clock" :size="13" class="text-amber-400" />
                  <span>{{ lesson.estimatedMinutes }} phút học</span>
                </span>
                <span
                  v-if="isCompleted"
                  class="px-2 py-0.5 rounded-md text-[11px] font-sans font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 flex items-center gap-1"
                >
                  <Icons name="Check" :size="12" />
                  <span>Đã hoàn thành</span>
                </span>
                <span
                  v-if="currentScore !== null"
                  class="px-2 py-0.5 rounded-md text-[11px] font-sans font-semibold bg-amber-500/15 text-amber-300 border border-amber-500/30"
                >
                  Điểm: {{ currentScore }}%
                </span>
              </div>

              <h2 id="lesson-modal-title" class="text-lg sm:text-2xl font-bold text-amber-100 leading-tight">
                {{ lesson.title }}
              </h2>
              <p class="text-xs sm:text-sm text-amber-300/80 italic font-sans">
                {{ lesson.paliTitle }}
              </p>
            </div>

            <!-- Top Actions: Bookmark & Close -->
            <div class="flex items-center gap-2 shrink-0">
              <button
                type="button"
                @click="handleBookmarkToggle"
                :class="[
                  'p-2 rounded-xl border transition-all cursor-pointer min-h-[38px] min-w-[38px] flex items-center justify-center',
                  isBookmarked
                    ? 'bg-amber-500 text-stone-950 border-amber-400 shadow-md'
                    : 'bg-stone-900 border-stone-700 text-stone-300 hover:text-amber-300 hover:border-amber-500/40'
                ]"
                :title="isBookmarked ? 'Bỏ lưu bài học' : 'Lưu bài học'"
              >
                <Icons name="Sparkles" :size="18" />
              </button>

              <button
                type="button"
                @click="emit('close')"
                class="p-2 rounded-xl bg-stone-900 border border-stone-700 text-stone-300 hover:text-white hover:bg-stone-800 transition-all cursor-pointer min-h-[38px] min-w-[38px] flex items-center justify-center"
                aria-label="Đóng bài học"
              >
                <Icons name="X" :size="20" />
              </button>
            </div>
          </div>

          <!-- 4 Navigation Tabs -->
          <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 mt-4 pt-3 border-t border-stone-800/80">
            <button
              type="button"
              @click="activeTab = 'theory'"
              :class="[
                'px-3 sm:px-4 py-1.5 sm:py-2 rounded-xl text-xs sm:text-sm font-bold transition-all flex items-center gap-1.5 cursor-pointer',
                activeTab === 'theory'
                  ? 'bg-amber-500 text-stone-950 shadow-md'
                  : 'bg-stone-900/80 text-stone-300 hover:text-white hover:bg-stone-800 border border-stone-800'
              ]"
            >
              <span>📖</span>
              <span>Lý Thuyết & Ngữ Pháp</span>
            </button>

            <button
              type="button"
              @click="activeTab = 'vocab'"
              :class="[
                'px-3 sm:px-4 py-1.5 sm:py-2 rounded-xl text-xs sm:text-sm font-bold transition-all flex items-center gap-1.5 cursor-pointer',
                activeTab === 'vocab'
                  ? 'bg-amber-500 text-stone-950 shadow-md'
                  : 'bg-stone-900/80 text-stone-300 hover:text-white hover:bg-stone-800 border border-stone-800'
              ]"
            >
              <span>🔤</span>
              <span>Từ Vựng ({{ lesson.vocabulary.length }})</span>
            </button>

            <button
              v-if="lesson.verseAnalysis"
              type="button"
              @click="activeTab = 'verse'"
              :class="[
                'px-3 sm:px-4 py-1.5 sm:py-2 rounded-xl text-xs sm:text-sm font-bold transition-all flex items-center gap-1.5 cursor-pointer',
                activeTab === 'verse'
                  ? 'bg-amber-500 text-stone-950 shadow-md'
                  : 'bg-stone-900/80 text-stone-300 hover:text-white hover:bg-stone-800 border border-stone-800'
              ]"
            >
              <span>📜</span>
              <span>Phân Tích Kệ Ngôn</span>
            </button>

            <button
              type="button"
              @click="activeTab = 'quiz'"
              :class="[
                'px-3 sm:px-4 py-1.5 sm:py-2 rounded-xl text-xs sm:text-sm font-bold transition-all flex items-center gap-1.5 cursor-pointer',
                activeTab === 'quiz'
                  ? 'bg-amber-500 text-stone-950 shadow-md'
                  : 'bg-stone-900/80 text-stone-300 hover:text-white hover:bg-stone-800 border border-stone-800'
              ]"
            >
              <span>✨</span>
              <span>Trắc Nghiệm ({{ lesson.quiz.length }})</span>
            </button>
          </div>
        </div>

        <!-- Modal Body Content (Scrollable) -->
        <div class="flex-1 p-5 sm:p-8 overflow-y-auto space-y-6 text-left">
          
          <!-- TAB 1: THEORY & GRAMMAR -->
          <div v-if="activeTab === 'theory'" class="space-y-6">
            <!-- Summary Highlights -->
            <div class="p-4 sm:p-5 rounded-2xl bg-amber-500/10 border border-amber-500/30">
              <h3 class="text-xs sm:text-sm font-bold text-amber-300 uppercase tracking-wider flex items-center gap-2 mb-2">
                <span>☸️</span>
                <span>Điểm Trọng Tâm Cần Nắm Vững</span>
              </h3>
              <ul class="space-y-1.5 text-xs sm:text-sm text-stone-200 list-disc list-inside">
                <li v-for="(point, idx) in lesson.summaryPoints" :key="idx" class="leading-relaxed">
                  {{ point }}
                </li>
              </ul>
            </div>

            <!-- Detailed Grammar Sections -->
            <div v-for="(sec, idx) in lesson.grammarSections" :key="idx" class="space-y-3 pt-2">
              <h3 class="text-base sm:text-lg font-bold text-amber-200 flex items-center gap-2 border-b border-stone-800 pb-2">
                <span class="text-amber-400">§</span>
                <span>{{ sec.title }}</span>
              </h3>
              <p class="text-xs sm:text-sm text-stone-300 leading-relaxed whitespace-pre-line">
                {{ sec.explanation }}
              </p>

              <!-- Grammar Table if present -->
              <div v-if="sec.table" class="overflow-x-auto rounded-2xl border border-stone-800 bg-stone-900/70 p-1">
                <table class="w-full text-xs sm:text-sm text-left border-collapse">
                  <thead>
                    <tr class="bg-amber-500/20 text-amber-300 border-b border-amber-500/30 font-bold">
                      <th v-for="(h, hIdx) in sec.table.headers" :key="hIdx" class="px-3 sm:px-4 py-2.5 whitespace-nowrap">
                        {{ h }}
                      </th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-stone-800">
                    <tr
                      v-for="(row, rIdx) in sec.table.rows"
                      :key="rIdx"
                      class="hover:bg-stone-800/50 transition-colors"
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
              <div v-if="sec.tip" class="p-3 rounded-xl bg-stone-900 border-l-4 border-amber-500 text-xs sm:text-sm text-amber-200/90 italic flex items-start gap-2">
                <span>💡</span>
                <span>{{ sec.tip }}</span>
              </div>
            </div>
          </div>

          <!-- TAB 2: VOCABULARY -->
          <div v-else-if="activeTab === 'vocab'" class="space-y-4">
            <p class="text-xs sm:text-sm text-stone-400">
              Nhấp vào thuật ngữ Pāḷi để lắng nghe âm vang chuông chánh niệm và huân tập từ vựng chuẩn xác:
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
              <div
                v-for="(item, vIdx) in lesson.vocabulary"
                :key="vIdx"
                class="p-4 rounded-2xl bg-stone-900/80 border border-stone-800 hover:border-amber-500/50 transition-all space-y-2 group relative overflow-hidden"
              >
                <div class="flex items-start justify-between gap-2">
                  <div>
                    <div class="flex items-center gap-2">
                      <span class="text-base sm:text-lg font-bold text-amber-300">
                        {{ item.term }}
                      </span>
                      <button
                        type="button"
                        @click="playVocabSound(item.term)"
                        class="text-xs p-1 rounded-md text-amber-400 hover:bg-amber-500/20 transition-all"
                        title="Thỉnh chuông ôn từ"
                      >
                        🔔
                      </button>
                    </div>
                    <span v-if="item.ipa" class="text-[11px] font-mono text-stone-400 block">
                      {{ item.ipa }}
                    </span>
                  </div>

                  <span class="text-[10px] font-sans px-2 py-0.5 rounded-full bg-stone-800 text-amber-400/90 border border-stone-700 whitespace-nowrap">
                    {{ item.partOfSpeech }}
                  </span>
                </div>

                <p class="text-xs sm:text-sm text-stone-100 font-semibold leading-snug">
                  {{ item.vietnamese }}
                </p>

                <div v-if="item.root || item.note || item.example" class="pt-1 text-[11px] text-stone-400 space-y-0.5 border-t border-stone-800/80">
                  <div v-if="item.root" class="font-mono text-amber-300/80">
                    <span class="font-sans text-stone-400">Căn ngữ:</span> {{ item.root }}
                  </div>
                  <div v-if="item.note" class="italic">
                    {{ item.note }}
                  </div>
                  <div v-if="item.example" class="text-amber-100/90 font-serif">
                    <span class="text-stone-400">Ví dụ:</span> {{ item.example }}
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- TAB 3: VERSE ANALYSIS -->
          <div v-else-if="activeTab === 'verse' && lesson.verseAnalysis" class="space-y-6">
            <!-- Original Verse Box -->
            <div class="p-5 rounded-2xl bg-gradient-to-br from-amber-950/40 via-stone-900 to-stone-950 border border-amber-500/40 text-center space-y-3">
              <div class="text-base sm:text-lg text-amber-200 font-serif leading-relaxed whitespace-pre-line font-bold">
                {{ lesson.verseAnalysis.originalPali }}
              </div>
              <div class="text-xs sm:text-sm text-stone-300 italic whitespace-pre-line border-t border-amber-500/20 pt-2">
                {{ lesson.verseAnalysis.vietnamese }}
              </div>
              <p v-if="lesson.verseAnalysis.context" class="text-[11px] text-stone-400 border-t border-stone-800 pt-2">
                <strong class="text-amber-400">Bối cảnh kinh văn:</strong> {{ lesson.verseAnalysis.context }}
              </p>
            </div>

            <!-- Word-by-Word Breakdown Table -->
            <div class="space-y-2">
              <h3 class="text-sm font-bold text-amber-300 uppercase tracking-wider">
                Bảng Phân Tích Từng Từ (Word-by-Word Syntax)
              </h3>
              <div class="overflow-x-auto rounded-2xl border border-stone-800 bg-stone-900/90">
                <table class="w-full text-xs sm:text-sm text-left border-collapse">
                  <thead>
                    <tr class="bg-amber-500/20 text-amber-300 border-b border-amber-500/30 font-bold">
                      <th class="px-3 sm:px-4 py-2.5">Từ Pāḷi</th>
                      <th class="px-3 sm:px-4 py-2.5">Phân Tích Ngữ Pháp</th>
                      <th class="px-3 sm:px-4 py-2.5">Căn / Gốc</th>
                      <th class="px-3 sm:px-4 py-2.5">Nghĩa Cú Pháp</th>
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
          </div>

          <!-- TAB 4: QUIZ -->
          <div v-else-if="activeTab === 'quiz'" class="space-y-6">
            <div class="flex items-center justify-between gap-3 p-3.5 rounded-2xl bg-amber-500/10 border border-amber-500/20">
              <div class="text-xs sm:text-sm text-stone-200">
                <span>Hoàn thành trắc nghiệm để củng cố kiến thức và ghi nhận tiến độ bài học.</span>
              </div>
              <div v-if="quizSubmitted" class="text-xs sm:text-sm font-bold font-sans px-3 py-1 rounded-xl bg-amber-500 text-stone-950">
                Kết quả: {{ quizScore }}%
              </div>
            </div>

            <!-- Questions List -->
            <div class="space-y-6">
              <div
                v-for="(q, qIdx) in lesson.quiz"
                :key="q.id"
                class="p-4 sm:p-5 rounded-2xl bg-stone-900/90 border border-stone-800 space-y-3"
              >
                <div class="flex items-start gap-2">
                  <span class="px-2 py-0.5 rounded-md bg-amber-500/20 text-amber-300 text-xs font-sans font-bold">
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
                    @click="handleSelectOption(q.id, optIdx)"
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
                  class="p-3 rounded-xl text-xs sm:text-sm text-left leading-relaxed mt-2"
                  :class="selectedAnswers[q.id] === q.correctIndex ? 'bg-emerald-950/40 border border-emerald-500/40 text-emerald-200' : 'bg-red-950/40 border border-red-500/40 text-red-200'"
                >
                  <strong class="block mb-0.5 font-bold">
                    {{ selectedAnswers[q.id] === q.correctIndex ? '✓ Đúng rồi!' : '✗ Chưa chính xác!' }}
                  </strong>
                  {{ q.explanation }}
                </div>
              </div>
            </div>

            <!-- Quiz Actions -->
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
                @click="handleMarkCompleteManual"
                class="px-4 py-2.5 rounded-2xl bg-stone-900 border border-emerald-500/40 text-emerald-300 hover:bg-emerald-500/20 text-xs sm:text-sm font-semibold transition-all flex items-center gap-1.5 cursor-pointer"
              >
                <Icons name="Check" :size="16" />
                <span>{{ isCompleted ? 'Đã hoàn thành' : 'Đánh dấu hoàn thành' }}</span>
              </button>
            </div>
          </div>

        </div>

        <!-- Modal Bottom Footer -->
        <div class="px-5 sm:px-8 py-3.5 border-t border-stone-800/80 bg-stone-950/95 flex flex-wrap items-center justify-between gap-3 text-xs text-stone-400 font-serif">
          <div class="flex items-center gap-2">
            <span>☸️ Ma Tọa Thiền Pāḷi Sikkhā</span>
          </div>

          <div class="flex items-center gap-2">
            <button
              type="button"
              @click="handleMarkCompleteManual"
              v-if="!isCompleted"
              class="px-3 py-1.5 rounded-xl bg-amber-500/20 text-amber-300 border border-amber-500/40 hover:bg-amber-500/30 transition-all text-xs font-semibold cursor-pointer"
            >
              ✓ Xác nhận đã học xong
            </button>
            <button
              type="button"
              @click="emit('close')"
              class="px-4 py-1.5 rounded-xl bg-stone-900 border border-stone-700 text-stone-300 hover:text-white text-xs transition-all cursor-pointer"
            >
              Đóng
            </button>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>
