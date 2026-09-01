<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import TheravadaLayout from '@/Layouts/TheravadaLayout.vue';
import Icons from '@/Components/ui/Icons.vue';
import PaliLessonModal from '@/Components/theravada/PaliLessonModal.vue';
import { mindfulBell } from '@/audio/mindfulBellAudio';
import {
  PALI_LESSON_CATEGORIES,
  PALI_LESSONS,
  PaliLesson,
  findLessonById,
} from '@/data/paliLessonsData';
import { usePaliProgress } from '@/composables/usePaliProgress';
import { useI18n } from '@/composables/useI18n';

const props = defineProps<{
  title?: string;
  initialLessonSlug?: string;
}>();

const { locale, t } = useI18n();

const {
  progressState,
  totalLessonsCount,
  completedCount,
  completionPercentage,
  rankInfo,
  isLessonCompleted,
  isLessonBookmarked,
  getLessonScore,
  toggleBookmark,
  setLastActiveLesson,
  resetProgress,
} = usePaliProgress();

// Filter States
const searchQuery = ref('');
const selectedCategoryId = ref<string>('ALL');
const selectedLevel = ref<string>('ALL');
const selectedStatus = ref<'ALL' | 'COMPLETED' | 'INCOMPLETE' | 'BOOKMARKED'>('ALL');

// Modal State
const isModalOpen = ref(false);
const activeLesson = ref<PaliLesson | null>(null);

// Alphabet Reference Accordion Toggle
const isAlphabetRefOpen = ref(false);

const filteredLessons = computed(() => {
  const query = searchQuery.value.trim().toLowerCase();

  return PALI_LESSONS.filter((lesson) => {
    // Category filter
    if (selectedCategoryId.value !== 'ALL' && lesson.categoryId !== selectedCategoryId.value) {
      return false;
    }

    // Level filter
    if (selectedLevel.value !== 'ALL' && lesson.level !== selectedLevel.value) {
      return false;
    }

    // Status filter
    if (selectedStatus.value === 'COMPLETED' && !isLessonCompleted(lesson.id)) {
      return false;
    }
    if (selectedStatus.value === 'INCOMPLETE' && isLessonCompleted(lesson.id)) {
      return false;
    }
    if (selectedStatus.value === 'BOOKMARKED' && !isLessonBookmarked(lesson.id)) {
      return false;
    }

    // Search query
    if (query) {
      const matchTitle = lesson.title.toLowerCase().includes(query);
      const matchPali = lesson.paliTitle.toLowerCase().includes(query);
      const matchDesc = lesson.description.toLowerCase().includes(query);
      const matchTags = lesson.tags.some((t) => t.toLowerCase().includes(query));
      const matchVocab = lesson.vocabulary.some(
        (v) =>
          v.term.toLowerCase().includes(query) ||
          v.vietnamese.toLowerCase().includes(query) ||
          (v.root && v.root.toLowerCase().includes(query)) ||
          (v.note && v.note.toLowerCase().includes(query)) ||
          (v.ipa && v.ipa.toLowerCase().includes(query))
      );
      const matchVerse = lesson.verseAnalysis
        ? lesson.verseAnalysis.originalPali.toLowerCase().includes(query) ||
          lesson.verseAnalysis.vietnamese.toLowerCase().includes(query) ||
          lesson.verseAnalysis.breakdown.some(
            (b) =>
              b.word.toLowerCase().includes(query) ||
              b.meaning.toLowerCase().includes(query) ||
              (b.rootOrStem && b.rootOrStem.toLowerCase().includes(query))
          )
        : false;

      if (!matchTitle && !matchPali && !matchDesc && !matchTags && !matchVocab && !matchVerse) {
        return false;
      }
    }

    return true;
  });
});

const openLesson = (lesson: PaliLesson) => {
  if (!lesson) return;
  activeLesson.value = lesson;
  setLastActiveLesson(lesson.id);
  isModalOpen.value = true;
  mindfulBell.ringBell(528, 2.0);
};

const openLastActiveLesson = () => {
  const lastId = progressState.value.lastActiveLessonId;
  const target = (lastId ? findLessonById(lastId) : undefined) || PALI_LESSONS[0];
  if (target) {
    openLesson(target);
  }
};

const handleCategorySelect = (catId: string) => {
  selectedCategoryId.value = catId;
  mindfulBell.strikeWoodenFish();
};

const handleResetConfirm = () => {
  if (confirm('Bạn có chắc chắn muốn đặt lại toàn bộ tiến độ học tập Pāḷi?')) {
    resetProgress();
    mindfulBell.strikeWoodenFish();
  }
};

const toggleAlphabetRef = () => {
  isAlphabetRefOpen.value = !isAlphabetRefOpen.value;
  mindfulBell.strikeWoodenFish();
};

onMounted(() => {
  let targetSlug = props.initialLessonSlug;
  if (!targetSlug && typeof window !== 'undefined') {
    const urlParams = new URLSearchParams(window.location.search);
    const queryParam = urlParams.get('bai') || urlParams.get('lesson');
    const hashParam = window.location.hash.replace(/^#/, '');
    targetSlug = queryParam || hashParam || undefined;
  }
  if (targetSlug && targetSlug.trim().length > 0) {
    const found = findLessonById(targetSlug.trim());
    if (found) {
      openLesson(found);
    }
  }
});

const paliLearningJsonLd = {
  '@context': 'https://schema.org',
  '@graph': [
    {
      '@type': 'Course',
      '@id': 'https://theravada.macatung.dev/hoc-pali#course',
      'name': 'Khóa Học Pāḷi Căn Bản & Khảo Sát Thánh Ngôn Tipiṭaka',
      'description': 'Chương trình học tiếng Pāḷi Phật giáo Theravāda toàn diện: Bảng chữ cái, 8 biến cách danh từ, chia động từ, từ vựng cốt lõi và phân tích kệ ngôn Kinh Pháp Cú.',
      'provider': {
        '@type': 'Organization',
        'name': 'Ma Tọa Thiền • macatung.dev',
        'url': 'https://theravada.macatung.dev'
      },
      'inLanguage': ['vi', 'pi'],
      'educationalLevel': 'Beginner to Intermediate'
    }
  ]
};
</script>

<template>
  <TheravadaLayout
    :title="title || 'Học Tiếng Pāḷi — Bảng Chữ Cái, Ngữ Pháp & Kệ Ngôn Tipiṭaka'"
    description="Khóa học tiếng Pāḷi tương tác trong hệ thống Theravāda: 8 biến cách danh từ, phát âm chuẩn, từ vựng Phật học cốt lõi, phân tích Kinh Pháp Cú và theo dõi tiến độ học tập."
    keywords="Học tiếng Pali, Pali learning, Học Pali online, Ngữ pháp Pali, 8 biến cách Pali, Từ vựng Pali, Kinh Pháp Cú Pali, Theravada Pali"
    canonical="https://theravada.macatung.dev/hoc-pali"
    :json-ld="paliLearningJsonLd"
  >
    <div class="max-w-6xl mx-auto py-3 sm:py-8 space-y-6 sm:space-y-10 text-left font-serif px-2 sm:px-4">
      
      <!-- 1. Breadcrumb & Top Badge -->
      <div class="flex flex-wrap items-center justify-between gap-3 border-b border-stone-800 pb-3">
        <nav class="flex items-center gap-1.5 sm:gap-2 text-[11px] sm:text-xs font-serif text-stone-400">
          <Link href="/theravada" class="hover:text-amber-300 transition-colors">Theravāda</Link>
          <span>/</span>
          <span class="text-amber-400 font-bold">Học Tiếng Pāḷi</span>
        </nav>

        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-300 text-[11px] font-sans font-bold shadow-sm">
          <span>☸️</span>
          <span>PĀḶI BHĀSĀ SIKKHĀ</span>
        </div>
      </div>

      <!-- 2. Hero Header -->
      <div class="space-y-3 sm:space-y-4">
        <h1 class="text-2xl sm:text-4xl lg:text-5xl font-bold text-amber-100 tracking-tight leading-tight">
          Học Tiếng Pāḷi <br class="hidden sm:inline" />
          <span class="text-transparent bg-clip-text bg-gradient-to-r from-amber-400 via-yellow-200 to-amber-500">
            Ngôn Ngữ Đức Phật & Tam Tạng Thánh Điển
          </span>
        </h1>
        <p class="text-xs sm:text-sm md:text-base text-stone-300 max-w-3xl leading-relaxed">
          Khảo cứu trực tiếp nguyên tác Pāḷi Tipiṭaka để thấu suốt lời dạy nguyên thủy của Bậc Đạo Sư mà không bị khúc xạ qua các bản dịch. Lộ trình bài học từ Bảng chữ cái, Ngữ pháp 8 biến cách đến Khảo sát Kinh Pháp Cú và Kinh tụng thiền môn.
        </p>
      </div>

      <!-- 3. Progress Tracking Dashboard (Local Storage) -->
      <div class="p-4 sm:p-6 rounded-3xl bg-gradient-to-br from-amber-950/40 via-stone-900/90 to-stone-950 border border-amber-500/30 shadow-2xl relative overflow-hidden">
        <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-5 sm:gap-6">
          
          <!-- Left: Progress & Rank Status -->
          <div class="space-y-2.5 flex-1 w-full">
            <div class="flex flex-wrap items-center justify-between gap-2">
              <div class="flex items-center gap-2">
                <span class="text-xs sm:text-sm font-bold text-amber-200">
                  Tiến Độ Học Tập Của Bạn
                </span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-sans font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40">
                  {{ rankInfo.badge }}
                </span>
              </div>
              <span class="text-xs sm:text-sm font-mono font-bold text-amber-400">
                {{ completedCount }}/{{ totalLessonsCount }} bài ({{ completionPercentage }}%)
              </span>
            </div>

            <!-- Progress Bar Line -->
            <div class="w-full h-3 rounded-full bg-stone-950 border border-stone-800 overflow-hidden p-0.5">
              <div
                class="h-full rounded-full bg-gradient-to-r from-amber-500 via-yellow-400 to-amber-300 transition-all duration-700 shadow-sm"
                :style="{ width: `${completionPercentage}%` }"
              />
            </div>

            <p class="text-[11px] sm:text-xs text-stone-400 italic">
              {{ rankInfo.description }}
            </p>
          </div>

          <!-- Right: Action Buttons -->
          <div class="flex flex-wrap items-center gap-2 shrink-0 w-full md:w-auto justify-start md:justify-end">
            <button
              type="button"
              @click="openLastActiveLesson"
              class="px-4 py-2.5 rounded-2xl bg-gradient-to-r from-amber-400 to-amber-500 text-stone-950 font-bold text-xs sm:text-sm hover:brightness-110 transition-all shadow-md flex items-center gap-1.5 cursor-pointer"
            >
              <span>▶</span>
              <span>{{ progressState.lastActiveLessonId ? 'Tiếp Tục Bài Học' : 'Bắt Đầu Bài 1' }}</span>
            </button>

            <button
              type="button"
              @click="selectedStatus = selectedStatus === 'BOOKMARKED' ? 'ALL' : 'BOOKMARKED'"
              :class="[
                'px-3.5 py-2.5 rounded-2xl border text-xs sm:text-sm font-semibold transition-all flex items-center gap-1.5 cursor-pointer',
                selectedStatus === 'BOOKMARKED'
                  ? 'bg-amber-500 text-stone-950 border-amber-400 shadow-md'
                  : 'bg-stone-900 border-stone-700 text-stone-300 hover:text-amber-300 hover:border-amber-500/40'
              ]"
            >
              <Icons name="Sparkles" :size="15" />
              <span>Đã Lưu ({{ progressState.bookmarkedLessons.length }})</span>
            </button>

            <button
              type="button"
              @click="handleResetConfirm"
              class="px-3 py-2.5 rounded-2xl bg-stone-900/80 border border-stone-800 text-stone-400 hover:text-red-300 hover:border-red-500/30 text-xs transition-all cursor-pointer"
              title="Đặt lại tiến độ học tập"
            >
              <Icons name="RotateCcw" :size="15" />
            </button>
          </div>

        </div>
      </div>

      <!-- 4. Quick Reference Accordion: Bảng Chữ Cái & 8 Biến Cách -->
      <div class="border border-stone-800 rounded-2xl bg-stone-900/50 overflow-hidden transition-all">
        <button
          type="button"
          @click="toggleAlphabetRef"
          class="w-full px-5 py-3.5 flex items-center justify-between gap-3 text-left hover:bg-stone-800/40 transition-colors cursor-pointer"
        >
          <div class="flex items-center gap-2.5">
            <span class="text-amber-400">📖</span>
            <span class="text-xs sm:text-sm font-bold text-amber-200">
              Tra Cứu Nhanh: 41 Mẫu Tự Pāḷi & 8 Biến Cách Căn Bản
            </span>
          </div>
          <Icons :name="isAlphabetRefOpen ? 'ChevronUp' : 'ChevronDown'" :size="18" class="text-stone-400" />
        </button>

        <div v-if="isAlphabetRefOpen" class="p-5 border-t border-stone-800 space-y-4 text-xs sm:text-sm">
          <!-- 8 Vowels -->
          <div class="space-y-1.5">
            <h4 class="font-bold text-amber-300">1. Tám Nguyên Âm (Sara):</h4>
            <p class="text-stone-300 font-mono text-xs sm:text-sm">
              <span class="text-amber-200 font-bold">a, ā, i, ī, u, ū, e, o</span> (a, i, u: ngắn; ā, ī, ū, e, o: dài)
            </p>
          </div>

          <!-- 33 Consonants -->
          <div class="space-y-1.5">
            <h4 class="font-bold text-amber-300">2. Ba Mươi Ba Phụ Âm (Vyañjana):</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2 text-stone-300 font-sans text-xs">
              <div class="p-2 rounded-lg bg-stone-900 border border-stone-800">
                <strong>Ka-vagga (Họng):</strong> k, kh, g, gh, ṅ
              </div>
              <div class="p-2 rounded-lg bg-stone-900 border border-stone-800">
                <strong>Ca-vagga (Vòm họng):</strong> c, ch, j, jh, ñ
              </div>
              <div class="p-2 rounded-lg bg-stone-900 border border-stone-800">
                <strong>Ṭa-vagga (Uốn lưỡi):</strong> ṭ, ṭh, ḍ, ḍh, ṇ
              </div>
              <div class="p-2 rounded-lg bg-stone-900 border border-stone-800">
                <strong>Ta-vagga (Răng):</strong> t, th, d, dh, n
              </div>
              <div class="p-2 rounded-lg bg-stone-900 border border-stone-800">
                <strong>Pa-vagga (Môi):</strong> p, ph, b, bh, m
              </div>
              <div class="p-2 rounded-lg bg-stone-900 border border-stone-800">
                <strong>Avagga (Không nhóm):</strong> y, r, l, v, s, h, ḷ, ṃ
              </div>
            </div>
          </div>

          <!-- 8 Cases -->
          <div class="space-y-1.5">
            <h4 class="font-bold text-amber-300">3. Tám Biến Cách Danh Từ (Aṭṭha Vibhatti):</h4>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs text-stone-300">
              <div class="p-2 rounded-lg bg-stone-900/90 border border-stone-800">1. Paṭhamā (Chủ cách)</div>
              <div class="p-2 rounded-lg bg-stone-900/90 border border-stone-800">2. Dutiyā (Đối cách)</div>
              <div class="p-2 rounded-lg bg-stone-900/90 border border-stone-800">3. Tatiyā (Sở dụng cách)</div>
              <div class="p-2 rounded-lg bg-stone-900/90 border border-stone-800">4. Catutthī (Chỉ định cách)</div>
              <div class="p-2 rounded-lg bg-stone-900/90 border border-stone-800">5. Pañcamī (Xuất xứ cách)</div>
              <div class="p-2 rounded-lg bg-stone-900/90 border border-stone-800">6. Chaṭṭhī (Sở thuộc cách)</div>
              <div class="p-2 rounded-lg bg-stone-900/90 border border-stone-800">7. Sattamī (Định vị cách)</div>
              <div class="p-2 rounded-lg bg-stone-900/90 border border-stone-800">8. Ālapana (Hô cách)</div>
            </div>
          </div>
        </div>
      </div>

      <!-- 5. Category Tabs & Search Filter Controls -->
      <div class="space-y-4 pt-2">
        <!-- Search Input -->
        <div class="relative max-w-xl">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Tìm kiếm bài học, thuật ngữ Pāḷi (Vibhatti, Buddha, Sati, Dhammapada)..."
            class="w-full pl-10 sm:pl-11 pr-4 py-3 rounded-2xl bg-stone-900 border border-stone-700 text-xs sm:text-base text-stone-100 placeholder-stone-400 focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-500/40 shadow-inner font-serif"
          />
          <span class="absolute left-3.5 sm:left-4 top-3.5 sm:top-4 text-amber-400 text-sm">🔍</span>
          <button
            v-if="searchQuery"
            type="button"
            @click="searchQuery = ''"
            class="absolute right-3.5 top-3.5 text-stone-400 hover:text-white"
          >
            ✕
          </button>
        </div>

        <!-- Category Filter Pills -->
        <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
          <button
            type="button"
            @click="handleCategorySelect('ALL')"
            :class="[
              'px-3.5 py-2 rounded-2xl text-xs sm:text-sm font-semibold transition-all cursor-pointer whitespace-nowrap border',
              selectedCategoryId === 'ALL'
                ? 'bg-amber-500 text-stone-950 font-bold border-amber-400 shadow-md'
                : 'bg-stone-900/90 border-stone-800 text-stone-300 hover:text-amber-300 hover:border-amber-500/40'
            ]"
          >
            Tất Cả Chủ Đề ({{ PALI_LESSONS.length }})
          </button>

          <button
            v-for="cat in PALI_LESSON_CATEGORIES"
            :key="cat.id"
            type="button"
            @click="handleCategorySelect(cat.id)"
            :class="[
              'px-3.5 py-2 rounded-2xl text-xs sm:text-sm font-semibold transition-all cursor-pointer whitespace-nowrap border',
              selectedCategoryId === cat.id
                ? 'bg-amber-500 text-stone-950 font-bold border-amber-400 shadow-md'
                : 'bg-stone-900/90 border-stone-800 text-stone-300 hover:text-amber-300 hover:border-amber-500/40'
            ]"
          >
            {{ cat.name }}
          </button>
        </div>

        <!-- Level & Status Secondary Filters -->
        <div class="flex flex-wrap items-center justify-between gap-3 text-xs font-sans pt-1">
          <div class="flex items-center gap-2">
            <span class="text-stone-400">Trạng thái:</span>
            <select
              v-model="selectedStatus"
              class="px-2.5 py-1 rounded-xl bg-stone-900 border border-stone-700 text-stone-200 text-xs focus:outline-none focus:border-amber-500"
            >
              <option value="ALL">Tất cả bài học</option>
              <option value="COMPLETED">Đã hoàn thành</option>
              <option value="INCOMPLETE">Chưa hoàn thành</option>
              <option value="BOOKMARKED">Đã lưu bài</option>
            </select>
          </div>

          <div class="flex items-center gap-2">
            <span class="text-stone-400">Cấp độ:</span>
            <select
              v-model="selectedLevel"
              class="px-2.5 py-1 rounded-xl bg-stone-900 border border-stone-700 text-stone-200 text-xs focus:outline-none focus:border-amber-500"
            >
              <option value="ALL">Tất cả cấp độ</option>
              <option value="Căn bản">Căn bản</option>
              <option value="Trung cấp">Trung cấp</option>
              <option value="Nâng cao">Nâng cao</option>
            </select>
          </div>
        </div>
      </div>

      <!-- 6. Lesson Cards Grid -->
      <div v-if="filteredLessons.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
        <div
          v-for="lesson in filteredLessons"
          :key="lesson.id"
          class="p-5 sm:p-6 rounded-3xl bg-stone-900/80 border transition-all duration-300 flex flex-col justify-between group hover:shadow-2xl hover:scale-[1.01]"
          :class="[
            isLessonCompleted(lesson.id)
              ? 'border-emerald-500/40 bg-gradient-to-br from-emerald-950/20 via-stone-900/90 to-stone-950'
              : 'border-stone-800 hover:border-amber-500/50'
          ]"
        >
          <!-- Card Top Row -->
          <div class="space-y-3">
            <div class="flex items-start justify-between gap-2">
              <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-sans font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                  {{ lesson.level }}
                </span>
                <span class="text-[11px] text-stone-400 font-sans flex items-center gap-1">
                  <Icons name="Clock" :size="12" class="text-amber-400" />
                  <span>{{ lesson.estimatedMinutes }} phút</span>
                </span>
                <span
                  v-if="isLessonCompleted(lesson.id)"
                  class="px-2 py-0.5 rounded-lg text-[10px] font-sans font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 flex items-center gap-1"
                >
                  <Icons name="Check" :size="11" />
                  <span>Hoàn thành</span>
                </span>
              </div>

              <!-- Bookmark Button -->
              <button
                type="button"
                @click.stop="toggleBookmark(lesson.id)"
                :class="[
                  'p-1.5 rounded-xl border transition-all cursor-pointer',
                  isLessonBookmarked(lesson.id)
                    ? 'bg-amber-500 text-stone-950 border-amber-400'
                    : 'bg-stone-950 border-stone-800 text-stone-400 hover:text-amber-300'
                ]"
                :title="isLessonBookmarked(lesson.id) ? 'Bỏ lưu' : 'Lưu bài'"
              >
                <Icons name="Sparkles" :size="14" />
              </button>
            </div>

            <!-- Title & Pali Subtitle -->
            <div>
              <h3 class="text-base sm:text-lg font-bold text-amber-100 group-hover:text-amber-300 transition-colors leading-snug">
                {{ lesson.title }}
              </h3>
              <p class="text-xs text-amber-300/80 font-sans italic mt-0.5">
                {{ lesson.paliTitle }}
              </p>
            </div>

            <!-- Description -->
            <p class="text-xs sm:text-sm text-stone-300 line-clamp-3 leading-relaxed">
              {{ lesson.description }}
            </p>

            <!-- Tags -->
            <div class="flex flex-wrap gap-1.5 pt-1">
              <span
                v-for="(tag, tIdx) in lesson.tags.slice(0, 4)"
                :key="tIdx"
                class="px-2 py-0.5 rounded-md text-[10px] font-sans bg-stone-950 border border-stone-800 text-stone-400"
              >
                #{{ tag }}
              </span>
            </div>
          </div>

          <!-- Card Bottom CTA -->
          <div class="pt-4 mt-4 border-t border-stone-800/80 flex items-center justify-between gap-3">
            <div class="text-[11px] font-sans text-stone-400">
              <span>{{ lesson.vocabulary.length }} từ vựng</span>
              <span v-if="lesson.verseAnalysis"> • Kệ ngôn</span>
            </div>

            <button
              type="button"
              @click="openLesson(lesson)"
              class="px-4 py-2 rounded-xl font-bold text-xs transition-all shadow-sm flex items-center gap-1.5 cursor-pointer"
              :class="[
                isLessonCompleted(lesson.id)
                  ? 'bg-stone-800 text-amber-300 hover:bg-stone-700 border border-amber-500/30'
                  : 'bg-gradient-to-r from-amber-400 to-amber-500 text-stone-950 hover:brightness-110'
              ]"
            >
              <span>{{ isLessonCompleted(lesson.id) ? 'Ôn Tập Lại' : 'Vào Học Ngay' }}</span>
              <span>➔</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Empty Filter State -->
      <div v-else class="py-12 text-center p-8 rounded-3xl bg-stone-900/50 border border-stone-800 space-y-3">
        <span class="text-4xl">☸️</span>
        <h3 class="text-base sm:text-lg font-bold text-amber-200">
          Không tìm thấy bài học phù hợp
        </h3>
        <p class="text-xs sm:text-sm text-stone-400 max-w-md mx-auto">
          Hãy thử xóa bộ lọc tìm kiếm hoặc chọn "Tất Cả Chủ Đề" để xem toàn bộ danh sách bài học Pāḷi.
        </p>
        <button
          type="button"
          @click="searchQuery = ''; selectedCategoryId = 'ALL'; selectedStatus = 'ALL'; selectedLevel = 'ALL'"
          class="px-4 py-2 rounded-xl bg-amber-500/20 text-amber-300 border border-amber-500/40 text-xs font-semibold hover:bg-amber-500/30 transition-all cursor-pointer"
        >
          Xóa Tất Cả Bộ Lọc
        </button>
      </div>

      <!-- 7. Cross-Links to Other Theravada Hubs -->
      <div class="pt-6 border-t border-stone-800 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 text-left">
        <Link
          href="/theravada/tu-dien-pali"
          class="p-4 rounded-2xl bg-stone-900/70 border border-stone-800 hover:border-amber-500/40 transition-all space-y-1.5 group"
        >
          <div class="text-amber-400 text-xl">📖</div>
          <h4 class="text-xs sm:text-sm font-bold text-amber-200 group-hover:text-amber-300">
            Từ Điển Thuật Ngữ Pāḷi ➔
          </h4>
          <p class="text-[11px] text-stone-400">
            Tra cứu hơn 45+ thuật ngữ Phật học cốt lõi từ A-Z với định nghĩa chuyên sâu.
          </p>
        </Link>

        <Link
          href="/theravada/ung-dung-tu-hoc"
          class="p-4 rounded-2xl bg-stone-900/70 border border-stone-800 hover:border-amber-500/40 transition-all space-y-1.5 group"
        >
          <div class="text-amber-400 text-xl">🧘</div>
          <h4 class="text-xs sm:text-sm font-bold text-amber-200 group-hover:text-amber-300">
            Ứng Dụng Pháp Bảo & Thiền Quán ➔
          </h4>
          <p class="text-[11px] text-stone-400">
            Quán chiếu Ngũ Uẩn Vô Ngã, Đồng hồ Vipassanā và Tạo thẻ Pháp Cú HD.
          </p>
        </Link>

        <Link
          href="/theravada/danh-muc/kinh-tung"
          class="p-4 rounded-2xl bg-stone-900/70 border border-stone-800 hover:border-amber-500/40 transition-all space-y-1.5 group"
        >
          <div class="text-amber-400 text-xl">📜</div>
          <h4 class="text-xs sm:text-sm font-bold text-amber-200 group-hover:text-amber-300">
            Kinh Tụng & Paritta Pāḷi ➔
          </h4>
          <p class="text-[11px] text-stone-400">
            Các bản kinh hộ trì Pāḷi — Việt thiêng liêng: Kinh Chuyển Pháp Luân, Kinh Từ Bi.
          </p>
        </Link>
      </div>

    </div>

    <!-- Interactive Lesson Detail Modal -->
    <PaliLessonModal
      :is-open="isModalOpen"
      :lesson="activeLesson"
      @close="isModalOpen = false"
    />
  </TheravadaLayout>
</template>
