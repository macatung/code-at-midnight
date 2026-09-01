import { ref, computed, onMounted, getCurrentInstance } from 'vue';
import { PALI_LESSONS } from '../data/paliLessonsData.ts';

const STORAGE_KEY = 'macatung_pali_learning_progress_v1';

export interface PaliProgressState {
  completedLessons: string[];
  lessonScores: Record<string, number>;
  bookmarkedLessons: string[];
  lastActiveLessonId: string | null;
  totalStudyMinutes: number;
}

const defaultState: PaliProgressState = {
  completedLessons: [],
  lessonScores: {},
  bookmarkedLessons: [],
  lastActiveLessonId: null,
  totalStudyMinutes: 0,
};

const progressState = ref<PaliProgressState>({ ...defaultState });
let isHydrated = false;

function getSafeStorage(): Storage | null {
  try {
    if (typeof window !== 'undefined' && window.localStorage) {
      return window.localStorage;
    }
  } catch {
    // SecurityError / sandbox iframe block
  }
  return null;
}

function loadFromStorage() {
  const storage = getSafeStorage();
  if (!storage) return;

  try {
    const raw = storage.getItem(STORAGE_KEY);
    if (raw) {
      const parsed = JSON.parse(raw);
      if (!parsed || typeof parsed !== 'object' || Array.isArray(parsed)) {
        return;
      }

      const rawCompleted = Array.isArray(parsed.completedLessons) ? parsed.completedLessons : [];
      // Deduplicate on load
      const completedSet = new Set<string>();
      rawCompleted.forEach((id: any) => {
        if (typeof id === 'string' && id.trim().length > 0) completedSet.add(id.trim());
      });

      const rawBookmarked = Array.isArray(parsed.bookmarkedLessons) ? parsed.bookmarkedLessons : [];
      const bookmarkedSet = new Set<string>();
      rawBookmarked.forEach((id: any) => {
        if (typeof id === 'string' && id.trim().length > 0) bookmarkedSet.add(id.trim());
      });

      const rawScores = typeof parsed.lessonScores === 'object' && parsed.lessonScores !== null && !Array.isArray(parsed.lessonScores)
        ? parsed.lessonScores
        : {};
      const sanitizedScores: Record<string, number> = {};
      Object.keys(rawScores).forEach((k) => {
        const val = rawScores[k];
        if (typeof val === 'number' && Number.isFinite(val) && !isNaN(val)) {
          sanitizedScores[k] = Math.min(100, Math.max(0, Math.round(val)));
        }
      });

      progressState.value = {
        completedLessons: Array.from(completedSet),
        lessonScores: sanitizedScores,
        bookmarkedLessons: Array.from(bookmarkedSet),
        lastActiveLessonId: typeof parsed.lastActiveLessonId === 'string' && parsed.lastActiveLessonId.trim().length > 0
          ? parsed.lastActiveLessonId.trim()
          : null,
        totalStudyMinutes: typeof parsed.totalStudyMinutes === 'number' && Number.isFinite(parsed.totalStudyMinutes) && !isNaN(parsed.totalStudyMinutes)
          ? Math.max(0, Math.round(parsed.totalStudyMinutes))
          : 0,
      };
    }
  } catch (e) {
    console.warn('[PaliProgress] Failed to parse localStorage state:', e);
  } finally {
    isHydrated = true;
  }
}

function saveToStorage() {
  const storage = getSafeStorage();
  if (!storage) return;

  try {
    storage.setItem(STORAGE_KEY, JSON.stringify(progressState.value));
  } catch (e) {
    console.warn('[PaliProgress] Failed to save state to localStorage:', e);
  }
}

export function usePaliProgress() {
  if (getCurrentInstance()) {
    onMounted(() => {
      if (!isHydrated) {
        loadFromStorage();
      }
    });
  }

  // If already running on client side
  if (typeof window !== 'undefined' && !isHydrated) {
    loadFromStorage();
  }

  const totalLessonsCount = computed(() => PALI_LESSONS.length);

  const completedCount = computed(() => {
    const validIds = new Set(PALI_LESSONS.map((l) => l.id));
    const uniqueCompleted = new Set(progressState.value.completedLessons.filter((id) => validIds.has(id)));
    return uniqueCompleted.size;
  });

  const completionPercentage = computed(() => {
    if (totalLessonsCount.value === 0) return 0;
    return Math.min(100, Math.max(0, Math.round((completedCount.value / totalLessonsCount.value) * 100)));
  });

  const rankInfo = computed(() => {
    const percent = completionPercentage.value;
    if (percent >= 100) {
      return {
        title: 'Bậc Thông Suốt Tipiṭaka (Tipiṭakadhara)',
        badge: '☸️ Đại Học Giả Pāḷi',
        description: 'Đã hoàn thành toàn diện 100% chương trình Pāḷi căn bản & kệ ngôn kinh điển.',
        color: 'from-amber-400 via-yellow-300 to-amber-500',
      };
    }
    if (percent >= 70) {
      return {
        title: 'Học Giả Tinh Tấn (Padagū Pāḷi)',
        badge: '📜 Học Giả Tinh Tấn',
        description: 'Nắm vững phần lớn ngữ pháp 8 biến cách và cấu trúc kệ ngôn.',
        color: 'from-amber-500 to-yellow-400',
      };
    }
    if (percent >= 40) {
      return {
        title: 'Hành Giả Trung Cấp (Saddasatthi)',
        badge: '✨ Hành Giả Căn Bản',
        description: 'Đã nắm vững bảng chữ cái, phát âm và các thuật ngữ giáo lý căn bản.',
        color: 'from-emerald-400 to-amber-400',
      };
    }
    if (percent > 0) {
      return {
        title: 'Tập Sự Pāḷi (Sikkhāka)',
        badge: '🌱 Tập Sự Pāḷi',
        description: 'Bắt đầu bước chân vào hành trình học hiểu ngôn ngữ Đức Phật.',
        color: 'from-stone-400 to-amber-300',
      };
    }
    return {
      title: 'Sơ Khai (Ārambha)',
      badge: '🌸 Người Tìm Hiểu',
      description: 'Chưa hoàn thành bài học nào. Hãy bắt đầu từ Bài 1!',
      color: 'from-stone-500 to-stone-400',
    };
  });

  const isLessonCompleted = (lessonId: string): boolean => {
    if (!lessonId || typeof lessonId !== 'string') return false;
    return progressState.value.completedLessons.includes(lessonId.trim());
  };

  const isLessonBookmarked = (lessonId: string): boolean => {
    if (!lessonId || typeof lessonId !== 'string') return false;
    return progressState.value.bookmarkedLessons.includes(lessonId.trim());
  };

  const getLessonScore = (lessonId: string): number | null => {
    if (!lessonId || typeof lessonId !== 'string') return null;
    return progressState.value.lessonScores[lessonId.trim()] ?? null;
  };

  const markLessonCompleted = (lessonId: string, score?: number) => {
    if (!lessonId || typeof lessonId !== 'string') return;
    const cleanId = lessonId.trim();
    if (!cleanId) return;

    if (!progressState.value.completedLessons.includes(cleanId)) {
      progressState.value.completedLessons.push(cleanId);
    }
    if (typeof score === 'number' && Number.isFinite(score) && !isNaN(score)) {
      const sanitized = Math.min(100, Math.max(0, Math.round(score)));
      progressState.value.lessonScores[cleanId] = Math.max(
        progressState.value.lessonScores[cleanId] ?? 0,
        sanitized
      );
    }
    progressState.value.lastActiveLessonId = cleanId;
    saveToStorage();
  };

  const toggleBookmark = (lessonId: string) => {
    if (!lessonId || typeof lessonId !== 'string') return;
    const cleanId = lessonId.trim();
    if (!cleanId) return;

    const idx = progressState.value.bookmarkedLessons.indexOf(cleanId);
    if (idx >= 0) {
      progressState.value.bookmarkedLessons.splice(idx, 1);
    } else {
      progressState.value.bookmarkedLessons.push(cleanId);
    }
    saveToStorage();
  };

  const setLastActiveLesson = (lessonId: string) => {
    if (!lessonId || typeof lessonId !== 'string') return;
    const cleanId = lessonId.trim();
    if (!cleanId) return;

    progressState.value.lastActiveLessonId = cleanId;
    saveToStorage();
  };

  const addStudyTime = (minutes: number) => {
    if (typeof minutes === 'number' && Number.isFinite(minutes) && !isNaN(minutes) && minutes > 0) {
      progressState.value.totalStudyMinutes += Math.round(minutes);
      saveToStorage();
    }
  };

  const resetProgress = () => {
    progressState.value = {
      completedLessons: [],
      lessonScores: {},
      bookmarkedLessons: [],
      lastActiveLessonId: null,
      totalStudyMinutes: 0,
    };
    saveToStorage();
  };

  return {
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
    addStudyTime,
    resetProgress,
  };
}
